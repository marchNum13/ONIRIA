<?php  
// check login status
if($_SESSION['login_ads'] == true){
    header('Location: home');
    exit();
}
include "databaseClass/connMySQLClass.php";
include "databaseClass/userTableClass.php";

$userTableClass = new userTableClass();

$reset = false;

if(isset($_GET['email']) && isset($_GET['v'])){
    $email = strtolower(trim(htmlspecialchars($_GET['email'])));
    $v = $_GET['v'];

    $checkEmail = $userTableClass->loginMember($email, "email");
    if($checkEmail['num'] > 0){
        $codeString = $checkEmail['code'];
        if(password_verify($codeString, $v)){
            $reset = true;
        }
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['reset'])){
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

        if($responseData['success']){
            $password = $_POST['password'];
            $passwordConfirm = $_POST['passwordConfirm'];

            if($_POST['password'] != "" && $_POST['passwordConfirm'] != ""){
                if($password == $passwordConfirm) {
                    $email = strtolower(trim(htmlspecialchars($_GET['email'])));
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $updatePassword = $userTableClass->updateUser(
                        dataSet:"user_password = '$hashedPassword'",
                        key:"user_email = '$email'"
                    );
                    if($updatePassword){
                        $updateCode = $userTableClass->updateUser(
                            dataSet:"user_code_verif = '0'",
                            key:"user_email = '$email'"
                        );
                        if($updateCode){
                            sleep(2);
                            $_SESSION['alert_success'] = "Reset password berhasil!";
                            header("Location: home");
                            exit();
                        }
                    }
                }else{
                    sleep(2);
                    $alert_error = "Konfirmasi password tidak sama.";
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