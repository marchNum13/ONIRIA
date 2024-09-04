<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}
if($_SESSION['user_role'] == "Member"){
    header('Location: home');
    exit();
}

include "databaseClass/connMySQLClass.php";

include "databaseClass/withdrawTableClass.php";
include "databaseClass/userTableClass.php";
include "databaseClass/walletUserTableClass.php";

$userTableClass = new userTableClass();
$withdrawTableClass = new withdrawTableClass();
$walletUserTableClass = new walletUserTableClass();
$page = isset($_GET['page']) ? $_GET['page'] : '1'; // number page

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $idWd = $_POST['idWd'];
    $pendingWdCheck = $withdrawTableClass->selectWithdraw(
        fields:"
            withdraw_user_id, 
            withdraw_nominal
        ",
        key:"withdraw_id = '$idWd' AND withdraw_status = 'Pending' LIMIT 1"
    );
    if($pendingWdCheck['row'] > 0){
        if(isset($_POST['tolakWD'])){
            $updateWd = $withdrawTableClass->updateWithdraw("withdraw_status = 'Ditolak'", "withdraw_id = '$idWd' AND withdraw_status = 'Pending'");
            if($updateWd){
                sleep(2);
                $_SESSION['alert_success'] = "Tolak withdraw berhasil.";
                header("Location: withdraw");
                exit();
            }else{
                sleep(2);
                $alert_error = "Error.";
            }
        }elseif(isset($_POST['konfirWD'])){
            $updateWd = $withdrawTableClass->updateWithdraw("withdraw_status = 'Success'", "withdraw_id = '$idWd' AND withdraw_status = 'Pending'");
            if($updateWd){
                $user = $pendingWdCheck['data'][0]['withdraw_user_id'];
                $saldoUser = getWallet($user);
                $totalWd = $pendingWdCheck['data'][0]['withdraw_nominal'];
                $saldoNow = $saldoUser-$totalWd;
                $updateWallet = $walletUserTableClass->updateWalletUser("user_saldo = '$saldoNow'", "user_refferal = '$user'");
                if($updateWallet){
                    sleep(2);
                    $_SESSION['alert_success'] = "Konfirmasi withdraw berhasil.";
                    header("Location: withdraw");
                    exit();
                }else{
                    sleep(2);
                    $alert_error = "Error.";
                }
            }else{
                sleep(2);
                $alert_error = "Error.";
            }
        }
    }else{
        sleep(2);
        $alert_error = "Data withdraw tidak ditemukan.";
    }
}

function tableWd($page){
    global $withdrawTableClass;
    $start = 10 * ($page - 1);
    $data = $withdrawTableClass->selectWithdraw(
        fields:"
            withdraw_id,
            withdraw_user_id, 
            withdraw_nominal, 
            withdraw_fee_admin, 
            withdraw_bank_user, 
            withdraw_status,
            DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
        ",
        key:"1 ORDER BY withdraw_date DESC LIMIT $start, 10"
    );

    return $data;
}

function tableCount(){
    global $withdrawTableClass;
    $data = $withdrawTableClass->selectWithdraw(
        fields:"
            COUNT(withdraw_user_id) AS total
        ",
        key:"1"
    );

    return $data['data'][0]['total'];
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

function getWallet($userAds){
    global $walletUserTableClass;

    $data = $walletUserTableClass->selectWalletUser(
        fields:"user_saldo",
        key:"user_refferal = '$userAds'"
    );
    return $data['data'][0]['user_saldo'];
}
?>