<?php  
// check login status
if($_SESSION['login_ads'] == true){
    header('Location: home');
    exit();
}
include "databaseClass/connMySQLClass.php";
include "databaseClass/userTableClass.php";

$userTableClass = new userTableClass();

$alert_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['login'])){
        $recaptchaSecret = '6LfEie0pAAAAAOtcPwGBTO5cGnIaxp68WdXkzD6e';
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

        $usernameOrEmail = strtolower(trim(htmlspecialchars($_POST['usernameOrEmail'])));
        $password = $_POST['password'];
        if($responseData['success']){
            if($_POST['usernameOrEmail'] != "" && $_POST['password'] !=  ""){
                $loginUsername = $userTableClass->loginMember($usernameOrEmail, "username");
                if($loginUsername['num'] > 0){
                    $passdb = $loginUsername['pass_user'];
                    if(password_verify($password, $passdb)){
                        if($loginUsername['status_user'] == "true"){
                            sleep(2);
                            $_SESSION['login_ads'] = true;
                            $_SESSION['user_ads'] = $loginUsername['user_id'];
                            $_SESSION['user_role'] = $loginUsername['role'];
                            $_SESSION['alert_success'] = "Anda berhasil masuk.";
                            header('Location: home');
                            exit();
                        }else{
                            sleep(2);
                            $alert_error = "Informasi Akun tidak ditemukan.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Informasi Akun tidak ditemukan.";
                    }
                }else{
                    $loginEmail = $userTableClass->loginMember($usernameOrEmail, "email");
                    if($loginEmail['num'] > 0){
                        $passdb = $loginEmail['pass_user'];
                        if(password_verify($password, $passdb)){
                            if($loginEmail['status_user'] == "true"){
                                sleep(2);
                                $_SESSION['login_ads'] = true;
                                $_SESSION['user_ads'] = $loginEmail['user_id'];
                                $_SESSION['user_role'] = $loginEmail['role'];
                                $_SESSION['alert_success'] = "Anda berhasil masuk.";
                                header('Location: home');
                                exit();
                            }else{
                                sleep(2);
                                $alert_error = "Informasi Akun tidak ditemukan.";
                            }
                        }else{
                            sleep(2);
                            $alert_error = "Informasi Akun tidak ditemukan.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Informasi Akun tidak ditemukan.";
                    }
                }
            }else{
                sleep(2);
                $alert_error = "Data tidak boleh kosong.";
            }
        }else{
            sleep(2);
            $alert_error = "Verifikasi reCAPTCHA gagal!";
        }
    }
}
?>