<?php  
// check login status
if($_SESSION['login_ads'] == true){
    header('Location: home');
    exit();
}
include "databaseClass/connMySQLClass.php";
include "databaseClass/userTableClass.php";

$userTableClass = new userTableClass();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require "vendor/autoload.php";

$alert_error = "";
$alert_success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['reset'])){
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
        if($responseData['success']){
            if($_POST['email'] != ""){
                $checkEmail = $userTableClass->loginMember($email, "email");
                if($checkEmail['num'] > 0){
                    $codeOTP = createCode();
                    $updateCode = $userTableClass->updateUser(
                        dataSet:"user_code_verif = '$codeOTP'",
                        key:"user_email = '$email'"
                    );
                    if($updateCode){
                        $sendEmail = sendEmail($email, $codeOTP);
                        if($sendEmail){
                            $alert_success = "Your OTP code has been sent. Please check your email!";
                        }else{
                            sleep(2);
                            $alert_error = "OTP code failed to send.";
                        }
                    }
                }else{
                    sleep(2);
                    $alert_error = "We couldn't find your account information.";
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

function sendEmail($emailDestination, $codeOTP){
    $memberName = explode("@", $emailDestination)[0];
    $url = "https://oniria.click/reset-password?email=" . $emailDestination . "&v=" . password_hash($codeOTP, PASSWORD_DEFAULT);
    $subject = 'Reset Password Account!';
    $message = '<html>
                    <body>
                    <h4>Confidential Message!</h4>
                    <p>Click the URL to reset your password: ' . $url . '</p>
                    </body>
                </html>';

 
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    
    // $mail->Host = "smtp.hostinger.com";
    $mail->Host = "smtp.hostinger.com";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->Username = "admin@oniria.click";
    $mail->Password = "4:uq;Qm1UdHR";

    $mail->setFrom("admin@oniria.click", "Oniria");
    $mail->addAddress($emailDestination, $memberName);
    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body = $message;

    return $mail->send();
}

// Fungsi untuk membuat kode refferal unik
function createCode() {
    global $userTableClass;

    $user_code_verif = substr(md5(uniqid(rand(), true)), 0, 7);

    $check = $userTableClass->selectUser(
        fields:"user_code_verif",
        key:"user_code_verif = '$user_code_verif' LIMIT 1"
    );

    if($check['row'] > 0){
        return createCode();
    }else{
        return $user_code_verif;
    }
}

?>