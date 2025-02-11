<?php  
// check login status
if($_SESSION['login_ads'] == true){
    header('Location: home');
    exit();
}
include "databaseClass/connMySQLClass.php";
include "databaseClass/userTableClass.php";
include "databaseClass/settingsPaketTableClass.php";
include "databaseClass/paketNonPremiumTableClass.php";
include "apiTele.php";


$userTableClass = new userTableClass();
$settingsPaketTableClass = new settingsPaketTableClass();
$paketNonPremiumTableClass = new paketNonPremiumTableClass();

$alert_error = "";

if(isset($_GET['reff'])){
    $upline = $_GET['reff'];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['registrasi'])){
        $recaptchaSecret = '6LfaTkcqAAAAAFfZX0dVfSN0N0li1twZqptAh8lw';
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        // Kirim request POST ke server Google
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $recaptchaSecret,
            'response' => $recaptchaResponse
        ];

        // Inisialisasi curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // Eksekusi curl dan dapatkan respons
        $response = curl_exec($ch);
        curl_close($ch);

        // Parsing JSON respons dari Google
        $responseData = json_decode($response, true);
        
        $email = strtolower(trim(htmlspecialchars($_POST['email'])));
        $upline = $_POST['reff'] == "" ? "NONE" : $_POST['reff'];
        $password = $_POST['password'];
        $passwordConfirm = $_POST['passwordConfirm'];
        if($responseData['success']){
            if($_POST['email'] != "" && $_POST['password'] != "" && $_POST['passwordConfirm'] != ""){
                if ($password == $passwordConfirm) {
                    $checkEmail = $userTableClass->selectUser(
                        fields:"user_email",
                        key:"user_email = '$email' LIMIT 1"
                    );
                    if($checkEmail['row'] == 0){
                        $checkReff = $userTableClass->selectUser(
                            fields:"user_refferal",
                            key:"user_refferal = '$upline' LIMIT 1"
                        );
                        if($checkReff['row'] > 0){
                            $username = createUsername($email);
                            $refferalUser = createRefferalCode();
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            // Data yang akan disimpan ke database
                            $fields = "user_username, user_refferal, user_email, user_password, user_upline";
                            $values = "'$username', '$refferalUser', '$email', '$hashedPassword', '$upline'";

                            // Insert data user baru ke database
                            $insert = $userTableClass->insertUser($fields, $values);
                            if($insert){
                                sleep(2);
                                // insertPaket basic
                                $dataPaket = dataPaket();
                                $dateNow = round(microtime(true) * 1000);
                                $nominal = 0;
                                $paket = "Free";
                                $rewardSatu = $dataPaket['settings_reward_tugas_satu'];
                                $rewardDua = $dataPaket['settings_reward_tugas_dua'];
                                $jumlah = $dataPaket['settings_jumlah_tugas'];
                                $userAds = $refferalUser;

                                // paketBasic
                                $idPaketBuy = generateUniquePaketBasicId();
                                $insertBasicPaket = $paketNonPremiumTableClass->insertPaket(
                                    fields:"paket_id, paket_user_id, paket_nominal, paket_name, paket_reward_tugas_satu, paket_reward_tugas_dua, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
                                    value:"'$idPaketBuy', '$userAds', '$nominal', '$paket', '$rewardSatu', '$rewardDua', '$jumlah', '$dateNow', '$dateNow'"
                                );
                                if($insertBasicPaket){
                                    $usernameUpline = memberName($upline);
                                    sendMessage("regis", $username, $usernameUpline, "", "");
                                    $_SESSION['alert_success'] = "Log in now!";
                                    header("Location: index");
                                    exit();
                                }

                            }
                        }else{
                            sleep(2);
                            $alert_error = "Referral code not found.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "This email address is already in use.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "Please make sure your passwords match. Your password and confirmation must be identical.";
                }
            }else{
                sleep(2);
                $alert_error = "This field is required.";
            }
        }else{
            sleep(2);
            $alert_error = "reCAPTCHA verification failed!";
        }
    }
}

function dataPaket(){
    global $settingsPaketTableClass;

    $data = $settingsPaketTableClass->selectPaket(
        fields:"id, settings_nama_paket, settings_harga_paket, settings_reward_tugas_satu, settings_reward_tugas_dua, settings_jumlah_tugas",
        key:"settings_nama_paket = 'Membership'"
    );

    return $data['data'][0];
}

function generateUniquePaketBasicId(){
    global $paketNonPremiumTableClass;
    $paketId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $data = $paketNonPremiumTableClass->selectPaket(
        fields:"paket_id",
        key:"paket_id = '$paketId'"
    );
    if ($data['row'] > 0) {
        return generateUniquePaketBasicId();
    } else {
        return $paketId;
    }
}

function memberName($id){
    global $userTableClass;
    $data = $userTableClass->selectUser("user_username", "user_refferal = '$id' LIMIT 1");
    if($data['row'] > 0){
        return $data['data'][0]['user_username'];
    }else{
        return "Unknown";
    }
}

// Fungsi untuk membuat username otomatis dari email
function createUsername($email) {
    global $userTableClass;

    $username = explode('@', $email)[0];
    $username = substr($username, 0, 4) . substr(md5(uniqid(rand(), true)), 0, 6);
    
    $check = $userTableClass->selectUser(
        fields:"user_username",
        key:"user_username = '$username' LIMIT 1"
    );

    if($check['row'] > 0){
        return createUsername($email);
    }else{
        return $username;
    }
    
}

// Fungsi untuk membuat kode refferal unik
function createRefferalCode() {
    global $userTableClass;

    $referal = substr(md5(uniqid(rand(), true)), 0, 7);

    $check = $userTableClass->selectUser(
        fields:"user_refferal",
        key:"user_refferal = '$referal' LIMIT 1"
    );

    if($check['row'] > 0){
        return createRefferalCode();
    }else{
        return $referal;
    }
}
?>