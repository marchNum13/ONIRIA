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

include "databaseClass/userTableClass.php";
include "databaseClass/klimBonusTableClass.php";
include "databaseClass/walletUserTableClass.php";
include "databaseClass/paketTableClass.php";

$userTableClass = new userTableClass();
$klimBonusTableClass = new klimBonusTableClass();
$paketTableClass = new paketTableClass();
$page = isset($_GET['page']) ? $_GET['page'] : '1'; // number page

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $idWd = $_POST['idWd'];
    $pendingKlaimCheck = $klimBonusTableClass->selectKlimBonus(
        fields:"
            klim_user_id
        ",
        key:"id = '$idWd' AND klim_status = 'Pending' LIMIT 1"
    );
    if($pendingKlaimCheck['row'] > 0){
        if(isset($_POST['tolakWD'])){
            $updateKlaim = $klimBonusTableClass->updateKlimBonus("klim_status = 'Ditolak'", "id = '$idWd' AND klim_status = 'Pending'");
            if($updateKlaim){
                sleep(2);
                $_SESSION['alert_success'] = "Tolak klaim berhasil.";
                header("Location: klaim");
                exit();
            }else{
                sleep(2);
                $alert_error = "Error.";
            }
        }elseif(isset($_POST['konfirWD'])){
            $updateKlaim = $klimBonusTableClass->updateKlimBonus("klim_status = 'Diterima'", "id = '$idWd' AND klim_status = 'Pending'");  
            if($updateKlaim){
                $idPaketBuy = generateUniquePaketBuyId();
                $userAds = $pendingKlaimCheck['data'][0]['klim_user_id'];
                $dateNow = round(microtime(true) * 1000);
                $insertBuyPaket = $paketTableClass->insertPaket(
                    fields:"paket_id, paket_user_id, paket_nominal, paket_estimasi, paket_name, paket_reward_tugas, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
                    value:"'$idPaketBuy', '$userAds', '0', 'Trial', 'Magang', '1250', '5', '$dateNow', '$dateNow'"
                );
                if($insertBuyPaket){
                    sleep(2);
                    // $userBuy = memberName($userAds);
                    // sendMessage("buyPaket", $userBuy, "", $nominal, $paket);
                    $_SESSION['alert_success'] = "Konfirmasi berhasil.";
                    header("Location: klaim");
                    exit();                   
                }
            }
        }
    }else{
        sleep(2);
        $alert_error = "Data klaim tidak ditemukan.";
    }
}

function generateUniquePaketBuyId(){
    global $paketTableClass;
    $paketId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $data = $paketTableClass->selectPaket(
        fields:"paket_id",
        key:"paket_id = '$paketId'"
    );
    if($data['row'] > 0){
        return generateUniquePaketBuyId();
    }else{
        return $paketId;
    }
}

function tableWd($page){
    global $klimBonusTableClass;
    $start = 10 * ($page - 1);
    $data = $klimBonusTableClass->selectKlimBonus(
        fields:"
            id,
            klim_user_id,
            klim_code,
            klim_status,
            DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(klim_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
        ",
        key:"1 ORDER BY klim_date DESC LIMIT $start, 10"
    );
    return $data;
}

function tableCount(){
    global $klimBonusTableClass;
    $data = $klimBonusTableClass->selectKlimBonus(
        fields:"
            COUNT(klim_user_id) AS total
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