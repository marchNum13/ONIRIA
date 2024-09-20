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

include "databaseClass/depositTableClass.php";
include "databaseClass/userTableClass.php";
include "databaseClass/walletUserTableClass.php";

$userTableClass = new userTableClass();
$depositTableClass = new depositTableClass();
$walletUserTableClass = new walletUserTableClass();
$page = isset($_GET['page']) ? $_GET['page'] : '1'; // number page

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $idDepo = $_POST['idDepo'];
    $pendingDepoCheck = $depositTableClass->selectDeposit(
        fields:"
            deposit_user_id, 
            deposit_nominal
        ",
        key:"deposit_id = '$idDepo' AND deposit_status = 'Pending' LIMIT 1"
    );
    if($pendingDepoCheck['row'] > 0){
        if(isset($_POST['tolakDEPO'])){
            $updateDepo = $depositTableClass->updateDeposit("deposit_status = 'Ditolak'", "deposit_id = '$idDepo' AND deposit_status = 'Pending'");
            if($updateDepo){
                sleep(2);
                $_SESSION['alert_success'] = "Tolak deposit berhasil.";
                header("Location: deposit");
                exit();
            }else{
                sleep(2);
                $alert_error = "Error.";
            }
        }elseif(isset($_POST['konfirDEPO'])){
            $updateDepo = $depositTableClass->updateDeposit("deposit_status = 'Success'", "deposit_id = '$idDepo' AND deposit_status = 'Pending'");
            if($updateDepo){
                $user = $pendingDepoCheck['data'][0]['deposit_user_id'];
                // $saldoUser = getWallet($user);
                // $totalDepo = $pendingDepoCheck['data'][0]['deposit_nominal'];
                // $saldoNow = $saldoUser+$totalDepo;
                $updateWallet = true;;
                if($updateWallet){
                    sleep(2);
                    $_SESSION['alert_success'] = "Konfirmasi deposit berhasil.";
                    header("Location: deposit");
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
        $alert_error = "Data deposit tidak ditemukan.";
    }
}

function tableDepo($page){
    global $depositTableClass;
    $start = 10 * ($page - 1);
    $data = $depositTableClass->selectDeposit(
        fields:"
            deposit_id,
            deposit_user_id, 
            deposit_nominal, 
            deposit_bank_admin, 
            deposit_bank_user, 
            deposit_bukti,
            deposit_status,
            DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(deposit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
        ",
        key:"1 ORDER BY deposit_date DESC LIMIT $start, 10"
    );

    return $data;
}

function tableCount(){
    global $depositTableClass;
    $data = $depositTableClass->selectDeposit(
        fields:"
            COUNT(deposit_user_id) AS total
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