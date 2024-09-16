<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}
include "databaseClass/connMySQLClass.php";
include "databaseClass/userTableClass.php";
include "databaseClass/bankUserTableClass.php";
include "databaseClass/bankAdminTableClass.php";

$userTableClass = new userTableClass();
$bankUserTableClass = new bankUserTableClass();
$bankAdminTableClass = new bankAdminTableClass();
$settingData = settingData();
$settingBank = settingBank();
$emailUbahEmail = $settingData['data'][0]['user_email'];
$usernameUbahUsername = $settingData['data'][0]['user_username'];

if($settingBank['row'] > 0){
    $noBank = $settingBank['data'][0]['rek'];
    $previewBank = substr($noBank, 0, 4) . "****";
}else{
    $previewBank = "Not yet set";
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $userAds = $_SESSION['user_ads'];
    $passDb = $settingData['data'][0]['user_password'];
    if(isset($_POST['simpanUbahEmail'])){
        $emailUbahEmail = strtolower(trim(htmlspecialchars($_POST['emailUbahEmail'])));
        $passwordUbahEmail = trim($_POST['passwordUbahEmail']);
        if($_POST['emailUbahEmail'] != "" && $_POST['passwordUbahEmail'] != ""){
            if(password_verify($passwordUbahEmail, $passDb)){
                $checkEmail = $userTableClass->selectUser(
                    fields:"user_email",
                    key:"user_email = '$emailUbahEmail' AND user_refferal <> '$userAds' LIMIT 1"
                );
                if($checkEmail['row'] == 0){
                    $updateEmail = $userTableClass->updateUser(
                        dataSet:"user_email = '$emailUbahEmail'",
                        key:"user_refferal = '$userAds'"
                    );
                    if($updateEmail){
                        sleep(2);
                        $_SESSION['alert_success'] = "Your email has been changed successfully!";
                        header("Location: app-settings");
                        exit();
                    }
                }else{
                    sleep(2);
                    $alert_error = "This email address is already in use.";
                }
            }else{
                sleep(2);
                $alert_error = "Wrong password.";
            }
        }else{
            sleep(2);
            $alert_error = "This field is required.";
        }
    }
    if(isset($_POST['simpanUbahUsername'])){
        $usernameUbahUsername = strtolower(trim(htmlspecialchars($_POST['usernameUbahUsername'])));
        $passwordUbahUsername = trim($_POST['passwordUbahUsername']);
        if($_POST['usernameUbahUsername'] != "" && $_POST['passwordUbahUsername'] != ""){
            // Periksa jumlah karakter
            $jumlahKarakter = strlen($usernameUbahUsername);
            if($jumlahKarakter <= 10){
                if(password_verify($passwordUbahUsername, $passDb)){
                    $checkUsername = $userTableClass->selectUser(
                        fields:"user_username",
                        key:"user_username = '$usernameUbahUsername' AND user_refferal <> '$userAds' LIMIT 1"
                    );
                    if($checkUsername['row'] == 0){
                        $updateUsername = $userTableClass->updateUser(
                            dataSet:"user_username = '$usernameUbahUsername'",
                            key:"user_refferal = '$userAds'"
                        );
                        if($updateUsername){
                            sleep(2);
                            $_SESSION['alert_success'] = "Username has been changed successfully!";
                            header("Location: app-settings");
                            exit();
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Username is already in use.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "Wrong password.";
                }
            }else{
                sleep(2);
                $alert_error = "Username cannot exceed 10 characters.";
            }
        }else{
            sleep(2);
            $alert_error = "This field is required.";
        }
    }
    if(isset($_POST['ubahPassword'])){
        $passwordbaru = trim($_POST['passwordbaru']);
        $passwordkonfirm = trim($_POST['passwordkonfirm']);
        $password = trim($_POST['password']);
        if($_POST['passwordbaru'] != "" && $_POST['passwordkonfirm'] != "" && $_POST['password']){
            if(password_verify($password, $passDb)){
                if($passwordbaru == $passwordkonfirm){
                    $hashedPassword = password_hash($passwordbaru, PASSWORD_DEFAULT);
                    $updatePassword = $userTableClass->updateUser(
                        dataSet:"user_password = '$hashedPassword'",
                        key:"user_refferal = '$userAds'"
                    );
                    if($updatePassword){
                        sleep(2);
                        $_SESSION['alert_success'] = "Ypur password has been changed successfully!";
                        header("Location: app-settings");
                        exit();
                    }
                }else{
                    sleep(2);
                    $alert_error = "Please make sure your passwords match. Your password and confirmation must be identical.";
                }
            }else{
                sleep(2);
                $alert_error = "Wrong password.";
            }
        }else{
            sleep(2);
            $alert_error = "This field is required.";
        }
    }
    if(isset($_POST['simpanBank'])){
        // $namaBank = strtoupper(trim(htmlspecialchars($_POST['namaBank'])));
        // $namaAkunBank = ucwords(strtolower(trim(htmlspecialchars($_POST['namaAkunBank']))));
        $noBank = trim(htmlspecialchars($_POST['noBank']));
        $passwordAkunBank = trim($_POST['passwordAkunBank']);
        if($_POST['noBank'] != "" && $_POST['passwordAkunBank'] != ""){
            if(password_verify($passwordAkunBank, $passDb)){
                $updateBank = false;
                if($settingBank['row'] > 0){
                    if($_SESSION['user_role'] != "Admin"){
                        $updateBank = $bankUserTableClass->updateBanktUser(
                            dataSet:"bank_user_number = '$noBank'",
                            key:"bank_user_refferal = '$userAds'"
                        );
                    }else{
                        $updateBank = $bankAdminTableClass->updateBanktAdmin(
                            dataSet:"bank_admin_number = '$noBank'",
                            key:"1"
                        );
                    }
                }else{
                    if($_SESSION['user_role'] != "Admin"){
                        $updateBank = $bankUserTableClass->insertBanktUser(
                            fields:"bank_user_refferal, bank_user_number",
                            value:"'$userAds','$noBank'"
                        );
                    }else{
                        $updateBank = $bankAdminTableClass->insertBanktAdmin(
                            fields:"bank_admin_number",
                            value:"'$noBank'"
                        );
                    }
                }
                if($updateBank){
                    sleep(2);
                    $_SESSION['alert_success'] = "Your Wallet address has been updated successfully!";
                    header("Location: app-settings");
                    exit();
                }
            }else{
                sleep(2);
                $alert_error = "Wrong password";
            }
        }else{
            sleep(2);
            $alert_error = "This field is required.";
        }
    }
}

function settingBank(){
    global $bankUserTableClass;
    global $bankAdminTableClass;
    $userAds = $_SESSION['user_ads'];
    $role = $_SESSION['user_role'];

    if($role != "Admin"){
        $data = $bankUserTableClass->selectBanktUser(
            fields:"bank_user_number AS rek",
            key:"bank_user_refferal = '$userAds'"
        );
    }else{
        $data = $bankAdminTableClass->selectBanktAdmin(
            fields:"bank_admin_number AS rek",
            key:"1"
        );
    }
    return $data;
}

function settingData(){
    global $userTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $userTableClass->selectUser(
        fields:"user_username, user_email, user_password",
        key:"user_refferal = '$userAds' LIMIT 1"
    );
    return $data;
}




?>