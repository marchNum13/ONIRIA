<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}

include "databaseClass/connMySQLClass.php";

include "databaseClass/walletUserTableClass.php";
include "databaseClass/bankUserTableClass.php";
include "databaseClass/bankAdminTableClass.php";
include "databaseClass/depositTableClass.php";
include "databaseClass/withdrawTableClass.php";
include "databaseClass/bonusTableClass.php";
include "databaseClass/profitTableClass.php";
include "databaseClass/adsUserTableClass.php";
include "databaseClass/paketTableClass.php";
include "databaseClass/settingsPaketTableClass.php";
include "databaseClass/userTableClass.php";
include "apiTele.php";

$walletUserTableClass = new walletUserTableClass();
$bankUserTableClass = new bankUserTableClass();
$bankAdminTableClass = new bankAdminTableClass();
$depositTableClass = new depositTableClass();
$withdrawTableClass = new withdrawTableClass();
$bonusTableClass = new bonusTableClass();
$profitTableClass = new profitTableClass();
$adsUserTableClass = new adsUserTableClass();
$paketTableClass = new paketTableClass();
$settingsPaketTableClass = new settingsPaketTableClass();
$userTableClass = new userTableClass();

$dateNowMilis = round(microtime(true) * 1000); // UTC currentmillis
$date = dateTimeNow(currentmillisdate: $dateNowMilis); // localtime
$firstDayOfMonth = $date['firstDayOfMonth'];
$lastDayOfMonth = $date['lastDayOfMonth'];
$page = isset($_GET['page']) ? $_GET['page'] : '1'; // number page
$getWallet = getWallet();
$memberBank = memberBank();
if($memberBank['row'] > 0){
    $namaBank = $memberBank['data'][0]['bank_user_name'];
    $namaAkunBank = $memberBank['data'][0]['bank_user_account_name'];
    $noBank = $memberBank['data'][0]['bank_user_number'];
    $bankUserDepo = $bankUserWd = $namaBank . ": " . $namaAkunBank . " (" . $noBank . ")";
}else{
    $bankUserDepo = $bankUserWd = "Belum diatur";
}

$adminBank = adminBank();
$createdAds = createdAds();
if($adminBank['row'] > 0){
    $namaBankAdmin = $adminBank['data'][0]['bank_admin_name'];
    $namaAkunBankAdmin = $adminBank['data'][0]['bank_admin_account_name'];
    $noBankAdmin = $adminBank['data'][0]['bank_admin_number'];
    $bankAdminDepo = $namaBankAdmin . ": " . $namaAkunBankAdmin . " (" . $noBankAdmin . ")";
}else{
    $bankAdminDepo = "Belum diatur";
}

$minWD = 100000;
$feeWD = (12.5 / 100);
$alert_error = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userAds = $_SESSION['user_ads'];
    if(isset($_POST['deposit'])){
        $checkDepo = $depositTableClass->selectDeposit("deposit_user_id", "deposit_user_id = '$userAds' AND deposit_status = 'Pending' LIMIT 1");
        if($checkDepo['row'] == 0){
            $bankAdminDepo = trim(htmlspecialchars($_POST['bankAdminDepo']));
            $bankUserDepo = trim(htmlspecialchars($_POST['bankUserDepo']));
            $jumlahDepo = $_POST['jumlahDepo'] == "" ? "0" : trim(htmlspecialchars($_POST['jumlahDepo']));
            $depositDate = round(microtime(true) * 1000);
            // Validasi input
            if($jumlahDepo > 0 && $_FILES['bukti_tf']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Mengunggah bukti transfer
                $targetDir = "bukti_tf/";
                $fileType = pathinfo($_FILES["bukti_tf"]["name"], PATHINFO_EXTENSION);
                $uniqueFileName = uniqid() . '.' . $fileType; // Menggunakan kode unik untuk nama file
                $targetFilePath = $targetDir . $uniqueFileName;
                // Memeriksa tipe file yang diunggah
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
                if(in_array($fileType, $allowTypes)) {
                    // Pindahkan file yang diunggah ke direktori tujuan
                    if(move_uploaded_file($_FILES["bukti_tf"]["tmp_name"], $targetFilePath)) {
                        $depositId = generateUniqueDepositId();
                        $fields = "deposit_id, deposit_user_id, deposit_nominal, deposit_bank_admin, deposit_bank_user, deposit_bukti, deposit_date";
                        $values = "'$depositId', '$userAds', '$jumlahDepo', '$bankAdminDepo', '$bankUserDepo', '$targetFilePath', '$depositDate'";
                        if ($depositTableClass->insertDeposit($fields, $values)) {
                            sleep(2);
                            $userData = memberName($userAds);
                            sendMessage("depo", $userData, "", $jumlahDepo, "");
                            $_SESSION['alert_success'] = "Deposit berhasil.";
                            header("Location: home");
                            exit();
                        }else{
                            sleep(2);
                            $alert_error = "Gagal mengunggah file.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Gagal mengunggah file.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "Format file yang diperbolehkan: JPG, JPEG, PNG, GIF, PDF.";
                }
            }else{
                sleep(2);
                $alert_error = "Data tidak boleh kosong.";
            }
        }else{
            sleep(2);
            $alert_error = "Anda memiliki pending deposit.";
        }
    }
    if(isset($_POST['withdraw'])){
        $checkPaketBayar = $paketTableClass->selectPaket(
            fields:"paket_name, paket_nominal",
            key:"paket_user_id = '$userAds' ORDER BY paket_name DESC LIMIT 1"

        );
        if($checkPaketBayar['row'] > 0){
            if($checkPaketBayar['data'][0]['paket_name'] != "Magang"){
                $checkWd = $withdrawTableClass->selectWithdraw("withdraw_user_id", "withdraw_user_id = '$userAds' AND withdraw_status = 'Pending' LIMIT 1");
                if($checkWd['row'] == 0){
                    $bankUserWd = trim(htmlspecialchars($_POST['bankUserWd']));
                    $mount = $_POST['mount'] == "" ? "0" : trim(htmlspecialchars($_POST['mount']));
                    $withdrawDate = round(microtime(true) * 1000);
                    if($mount > 0){
                        $saldo = getWallet();
                        if($saldo >= $mount){
                            if($mount >= $minWD){
                                $maxWd = $checkPaketBayar['data'][0]['paket_nominal'];
                                $sisaWd = sisaWD($userAds, $maxWd);
                                if($mount <= $sisaWd){
                                    $wdId = generateUniqueWDId();
                
                                    $fields = "withdraw_id, withdraw_user_id, withdraw_nominal, withdraw_fee_admin, withdraw_bank_user, withdraw_date";
                                    $values = "'$wdId', '$userAds', '$mount', $feeWD, '$bankUserWd', '$withdrawDate'";
                                    if($withdrawTableClass->insertWithdraw($fields, $values)) {
                                        sleep(2);
                                        $userData = memberName($userAds);
                                        sendMessage("wd", $userData, "", $mount, "");
                                        $_SESSION['alert_success'] = "Withdraw berhasil.";
                                        header("Location: home");
                                        exit();
                                    }else{
                                        sleep(2);
                                        $alert_error = "Gagal menyimpan penarikan.";
                                    }
                                }else{
                                    sleep(2);
                                    $alert_error = "Sisa WD harian Rp" . number_format($sisaWd) . " (Max WD perhari Rp" . number_format($maxWd) . ")";
                                }
                            }else{
                                sleep(2);
                                $alert_error = "Min WD Rp" . number_format($minWD);
                            }
                        }else{
                            sleep(2);
                            $alert_error = "Saldo tidak cukup.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Data tidak boleh kosong.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "Anda memiliki pending Withdraw.";
                }
            }else{
                sleep(2);
                $alert_error = "Minimal berlangganan 1 paket berbayar.";
            }
        }else{
            sleep(2);
            $alert_error = "Minimal berlangganan 1 paket berbayar.";
        }
    }
    if(isset($_POST['klaim'])){
        $adsID = trim(htmlspecialchars($_POST['adsID']));
        if($adsID != ""){
            $checkAds = $adsUserTableClass->selectAds("ads_reward", "ads_id = '$adsID' AND ads_status = 'Aktif' AND ads_user_id = '$userAds' LIMIT 1");
            if($checkAds['row'] > 0){
                $nominalRW = $checkAds['data'][0]['ads_reward'];
                $proftId = generateUniqueProfitId();
                $dateNowUTC = round(microtime(true) * 1000);
                $insertProfit = $profitTableClass->insertProfit(
                    fields:"
                        profit_id,
                        profit_user_id,
                        profit_ads_id,
                        profit_nominal,
                        profit_date
                    ",
                    value:"
                        '$proftId',
                        '$userAds',
                        '$adsID',
                        '$nominalRW',
                        '$dateNowUTC'
                    "
                );
                if($insertProfit){
                    $updateStatusAds = $adsUserTableClass->updateAds(
                        dataSet:"ads_status = 'Tidak Aktif'",
                        key:"ads_id = '$adsID'"
                    );
                    if($updateStatusAds){
                        $saldo = getWallet();
                        $saldoNow = $nominalRW + $saldo;
                        $updateWallet = $walletUserTableClass->updateWalletUser(
                            dataSet:"user_saldo = '$saldoNow'",
                            key:"user_refferal = '$userAds'"
                        );
                        if($updateWallet){
                            sleep(2);
                            $_SESSION['alert_success'] = "Klaim berhasil.";
                            header("Location: home");
                            exit();
                        }else{
                            sleep(2);
                            $alert_error = "Error.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Error.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "Error.";
                }
            }else{
                sleep(2);
                $alert_error = "Error.";
            }
        }else{
            sleep(2);
            $alert_error = "Error.";
        }
    }
}

function geSaldoCuan(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];

    $data = $paketTableClass->selectPaket(
        fields: "paket_nominal_cuan, paket_date_capitalback",
        key: "paket_user_id = '$userAds' AND paket_name <> 'Magang'"
    );

    return $data;
}

function sisaWD($userAds, $maxWd){
    global $withdrawTableClass;
    global $date;
    $today = $date['date'];

    $data = $withdrawTableClass->selectWithdraw(
        fields:"SUM(withdraw_nominal) total", 
        key:"
            withdraw_user_id = '$userAds' AND withdraw_status = 'Success' AND (
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$today 00:00:00' AND 
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$today 23:59:59'
            ) 
        "
    );
    $totalWd = 0;
    if($data['row'] > 0){
        $totalWd = $data['data'][0]['total'];
    }
    $sisa = $maxWd - $totalWd;

    return $sisa;
}

function tablePaket($page){
    global $paketTableClass;
    $start = 10 * ($page - 1);
    $paketUser = $paketTableClass->selectPaket(
        fields:"
            paket_user_id, 
            paket_nominal, 
            paket_name, 
            DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
        ",
        key:"paket_user_id <> '3d77666' AND paket_user_id <> '58a5c8e' AND paket_user_id <> 'c050c7f' AND paket_user_id <> '809b34b' ORDER BY paket_date DESC LIMIT $start, 10"
    );

    return $paketUser;
}

function tableCount(){
    global $paketTableClass;
    $paketUser = $paketTableClass->selectPaket(
        fields:"
            COUNT(paket_user_id) AS total
        ",
        key:"paket_user_id <> '3d77666' AND paket_user_id <> '58a5c8e' AND paket_user_id <> 'c050c7f' AND paket_user_id <> '809b34b'"
    );

    return $paketUser['data'][0]['total'];
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

function omsetMounth(){
    global $paketTableClass;
    global $lastDayOfMonth;
    global $firstDayOfMonth;
    $paketUser = $paketTableClass->selectPaket(
        fields:"SUM(paket_nominal) total",
        key:"
            paket_estimasi = 'Berbayar' 
            AND (
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$firstDayOfMonth 00:00:00' AND 
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$lastDayOfMonth 23:59:59'
            ) AND (paket_user_id <> '3d77666' AND paket_user_id <> '58a5c8e' AND paket_user_id <> 'c050c7f' AND paket_user_id <> '809b34b')
        "
    );

    return $paketUser;
}

function depoMounth(){
    global $depositTableClass;
    global $lastDayOfMonth;
    global $firstDayOfMonth;
    $depo = $depositTableClass->selectDeposit(
        fields:"SUM(deposit_nominal) total",
        key:"
            deposit_status = 'Success' 
            AND (
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(deposit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$firstDayOfMonth 00:00:00' AND 
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(deposit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$lastDayOfMonth 23:59:59'
            ) AND (deposit_user_id <> '3d77666' AND deposit_user_id <> '58a5c8e' AND deposit_user_id <> 'c050c7f' AND deposit_user_id <> '809b34b')
        "
    );

    return $depo;
}

function wdMounth(){
    global $withdrawTableClass;
    global $lastDayOfMonth;
    global $firstDayOfMonth;
    $wd = $withdrawTableClass->selectWithdraw(
        fields:"SUM(withdraw_nominal) total",
        key:"
            withdraw_status = 'Success' 
            AND (
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$firstDayOfMonth 00:00:00' AND 
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$lastDayOfMonth 23:59:59'
            ) AND (withdraw_user_id <> '3d77666' AND withdraw_user_id <> '58a5c8e' AND withdraw_user_id <> 'c050c7f' AND withdraw_user_id <> '809b34b')
        "
    );

    return $wd;
}

function totalAds(){
    global $adsUserTableClass;
    $paket = getPaketUser();
    $userAds = $_SESSION['user_ads'];
    $totalAdsAktif = 0;
    $totalAds = 0;
    if($paket['row'] > 0){
        foreach($paket['data'] as $row){
            $date = $row['paket_ads_stop_date'] - (1*24*60*60*1000);
            $paketId = $row['paket_id'];
            $jumlahAds = $row['paket_jumlah_tugas'];
            $dataAktif = $adsUserTableClass->selectAds("COUNT(ads_id) AS total", "ads_status = 'Aktif' AND ads_paket_id = '$paketId' AND ads_user_id = '$userAds' AND ads_date = '$date' ORDER BY ads_date DESC LIMIT $jumlahAds");
            if($dataAktif['row'] > 0){
                $totalAdsAktif += $dataAktif['data'][0]['total'];
            }
            $data = $adsUserTableClass->selectAds("COUNT(ads_id) AS total", "ads_paket_id = '$paketId' AND ads_user_id = '$userAds' AND ads_date = '$date' ORDER BY ads_date DESC LIMIT $jumlahAds");
            if($data['row'] > 0){
                $totalAds += $data['data'][0]['total'];
            }
        }
    } 
    
    return [
        'total' => $totalAds,
        'sisa' => $totalAdsAktif,
    ];
}

function createdAds(){
    $paket = getPaketUser();
    if($paket['row'] > 0){
        foreach($paket['data'] as $row){
            // tgl pengisian
            $paketId = $row['paket_id'];
            $namePaket = $row['paket_name'];
            $rewardPerAds = $row['paket_reward_tugas'];
            $jumlahAds = $row['paket_jumlah_tugas'];
            $endDay = $row['paket_ads_stop_date'];
            $dateBuy = $row['paket_date'];
            $inputAds = inputAds($paketId, $namePaket, $rewardPerAds, $jumlahAds, $endDay, $dateBuy);
        }
    }
}

function inputAds($paketId, $namePaket, $rewardPerAds, $jumlahAds, $endDay, $dateBuy){
    global $adsUserTableClass;
    global $paketTableClass;
    // tgl hari ini
    $thisDay = round(microtime(true) * 1000);
    if($thisDay >= $endDay){
        $process = true;
        if($namePaket == "Magang"){
            $fourDay = 4*24*60*60*1000;
            if($endDay - $dateBuy > $fourDay){
                $process = false;
            }
        }
        if($process){
            $userAds = $_SESSION['user_ads'];
            $nameAds = "YouTube";
            $linkAds = "tes";
            for ($i = 1; $i <= $jumlahAds; $i++) {
                $idAds = generateUniqueAdsId();
                $adsUserTableClass->insertAds(
                    fields:"
                        ads_id,
                        ads_user_id,
                        ads_paket_id,
                        ads_name,
                        ads_reward,
                        ads_link,
                        ads_date
                    ",
                    value:"
                        '$idAds',
                        '$userAds',
                        '$paketId',
                        '$nameAds',
                        '$rewardPerAds',
                        '$linkAds',
                        '$endDay'
                    "
                );
            }
            $nextDay = $endDay + (1*24*60*60*1000);
            $updateEndDay = $paketTableClass->updatePaket(dataSet:"paket_ads_stop_date = '$nextDay'",key:"paket_id = '$paketId'");
            if($updateEndDay){
                return inputAds($paketId, $namePaket, $rewardPerAds, $jumlahAds, $nextDay, $dateBuy);
            }
        }else{
            return true;
        }
    }else{
        return true;
    }

}

function dateTimeNow($currentmillisdate){
        
    // Mengatur zona waktu lokal Jakarta
    date_default_timezone_set('Asia/Makassar');

    // Konversi waktu UTC ke format tanggal dan waktu
    $datetime = date("Y-m-d H:i:s", $currentmillisdate / 1000);
    // Konversi waktu UTC ke format tanggal
    $date = date("Y-m-d", $currentmillisdate / 1000);
    // Dapatkan nama hari dalam bahasa Inggris
    $day = date("l", $currentmillisdate / 1000);
    // Dapatkan tanggal terakhir dari bulan saat ini
    $lastDayOfMonth = date("Y-m-t", $currentmillisdate / 1000);
    $firstDayOfMonth = date("Y-m", $currentmillisdate / 1000) . "-01";

    return [
        "datetime" => $datetime,
        "date" => $date,
        "day" => $day,
        "lastDayOfMonth" => $lastDayOfMonth,
        "firstDayOfMonth" => $firstDayOfMonth,
    ];
}

function generateUniqueProfitId() {
    global $profitTableClass;
    $profitId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $check = $profitTableClass->selectProfit("profit_id", "profit_id = '$profitId' LIMIT 1");
    
    if ($check['row'] > 0) {
        return generateUniqueProfitId();
    } else {
        return $profitId;
    }
}

function generateUniqueAdsId() {
    global $adsUserTableClass;
    $adsId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $check = $adsUserTableClass->selectAds("ads_id", "ads_id = '$adsId' LIMIT 1");
    
    if ($check['row'] > 0) {
        return generateUniqueAdsId();
    } else {
        return $adsId;
    }
}

function getPaketUser(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];
    $paketUser = $paketTableClass->selectPaket(
        fields:"paket_id, paket_user_id, paket_name, paket_reward_tugas, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
        key:"paket_user_id = '$userAds'"
    );

    return $paketUser;
}

function getSumBonus(){
    global $bonusTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $bonusTableClass->selectBonus(
        fields:"SUM(bonus_nominal) total",
        key:"bonus_user_id = '$userAds'"
    );
    return number_format($data['data'][0]['total']);
}

function getSumProfit(){
    global $profitTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $profitTableClass->selectProfit(
        fields:"SUM(profit_nominal) total",
        key:"profit_user_id = '$userAds'"
    );
    return number_format($data['data'][0]['total']);
}

function generateUniqueDepositId() {
    global $depositTableClass;
    $depositId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $check = $depositTableClass->selectDeposit("deposit_id", "deposit_id = '$depositId' LIMIT 1");
    
    if ($check['row'] > 0) {
        return generateUniqueDepositId();
    } else {
        return $depositId;
    }
}

function generateUniqueWDId() {
    global $withdrawTableClass;
    $withdrawId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $check = $withdrawTableClass->selectWithdraw("withdraw_id", "withdraw_id = '$withdrawId' LIMIT 1");
    
    if ($check['row'] > 0) {
        return generateUniqueWDId();
    } else {
        return $withdrawId;
    }
}

function getWallet(){
    global $walletUserTableClass;
    $userAds = $_SESSION['user_ads'];

    $data = $walletUserTableClass->selectWalletUser(
        fields:"user_saldo",
        key:"user_refferal = '$userAds'"
    );
    if($data['row'] > 0){
        return $data['data'][0]['user_saldo'];
    }else{
        $createWallet = $walletUserTableClass->insertWalletUser(
            fields:"user_refferal",
            value:"'$userAds'"
        );
        if($createWallet){
            return getWallet();
        }
    }
}

function adminBank(){
    global $bankAdminTableClass;

    $data = $bankAdminTableClass->selectBanktAdmin(
        fields:"bank_admin_account_name, bank_admin_name, bank_admin_number",
        key:"id = '1'"
    );
    return $data;
}

function memberBank(){
    global $bankUserTableClass;
    $userAds = $_SESSION['user_ads'];

    $data = $bankUserTableClass->selectBanktUser(
        fields:"bank_user_account_name, bank_user_name, bank_user_number",
        key:"bank_user_refferal = '$userAds'"
    );
    return $data;
}

?>