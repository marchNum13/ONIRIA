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
include "databaseClass/bonusMatchingTableClass.php";
include "databaseClass/profitTableClass.php";
include "databaseClass/adsUserTableClass.php";
include "databaseClass/adsBasicUserTableClass.php";
include "databaseClass/paketTableClass.php";
include "databaseClass/paketNonPremiumTableClass.php";
include "databaseClass/settingsPaketTableClass.php";
include "databaseClass/settingsMatchingTableClass.php";
include "databaseClass/userTableClass.php";
include "apiTele.php";

$walletUserTableClass = new walletUserTableClass();
$bankUserTableClass = new bankUserTableClass();
$bankAdminTableClass = new bankAdminTableClass();
$depositTableClass = new depositTableClass();
$withdrawTableClass = new withdrawTableClass();
$bonusTableClass = new bonusTableClass();
$bonusMatchingTableClass = new bonusMatchingTableClass();
$profitTableClass = new profitTableClass();
$adsUserTableClass = new adsUserTableClass();
$adsBasicUserTableClass = new adsBasicUserTableClass();
$paketTableClass = new paketTableClass();
$paketNonPremiumTableClass = new paketNonPremiumTableClass();
$settingsPaketTableClass = new settingsPaketTableClass();
$settingsMatchingTableClass = new settingsMatchingTableClass();
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
    $bankUserDepo = $bankUserWd = $memberBank['data'][0]['bank_user_number'];
}else{
    $bankUserDepo = $bankUserWd = "Not yet set";
}

$adminBank = adminBank();
$createdAds = createdAds();

if(getPaketUser()['row'] == 0){
    $createdAdsBasicPaket = createdAdsBasicPaket();
}

if($adminBank['row'] > 0){
    $namaBankAdmin = $adminBank['data'][0]['bank_admin_name'];
    $namaAkunBankAdmin = $adminBank['data'][0]['bank_admin_account_name'];
    $noBankAdmin = $adminBank['data'][0]['bank_admin_number'];
    $bankAdminDepo = $adminBank['data'][0]['bank_admin_number'];
}else{
    $bankAdminDepo = "Not yet set";
}

$minWD = 20;
$feeWD = 3;
$alert_error = "";

$totalBalance = totalBalance();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userAds = $_SESSION['user_ads'];
    if(isset($_POST['deposit'])){
        $checkDepo = $depositTableClass->selectDeposit("deposit_user_id", "deposit_user_id = '$userAds' AND deposit_status = 'Pending' LIMIT 1");
        if($checkDepo['row'] == 0){
            $bankAdminDepo = trim(htmlspecialchars($_POST['bankAdminDepo']));
            $bankUserDepo = trim(htmlspecialchars($_POST['bankUserDepo']));
            $bukti_tf = trim(htmlspecialchars($_POST['bukti_tf']));
            $jumlahDepo = $_POST['jumlahDepo'] == "" ? "0" : trim(htmlspecialchars($_POST['jumlahDepo']));
            $depositDate = round(microtime(true) * 1000);
            // Validasi input
            if($jumlahDepo > 0 && $bukti_tf != "" && $bankAdminDepo != "" && ($bankUserDepo != "" && $bankUserDepo != "Not yet set")) {
                $depositId = generateUniqueDepositId();
                $fields = "deposit_id, deposit_user_id, deposit_nominal, deposit_bank_admin, deposit_bank_user, deposit_bukti, deposit_date";
                $values = "'$depositId', '$userAds', '$jumlahDepo', '$bankAdminDepo', '$bankUserDepo', '$bukti_tf', '$depositDate'";
                if ($depositTableClass->insertDeposit($fields, $values)) {
                    sleep(2);
                    $userData = memberName($userAds);
                    sendMessage("depo", $userData, "", $jumlahDepo, "");
                    $_SESSION['alert_success'] = "Deposit successful!";
                    header("Location: home");
                    exit();
                }else{
                    sleep(2);
                    $alert_error = "Data saving failed.";
                }
                // // Mengunggah bukti transfer
                // $targetDir = "bukti_tf/";
                // $fileType = pathinfo($_FILES["bukti_tf"]["name"], PATHINFO_EXTENSION);
                // $uniqueFileName = uniqid() . '.' . $fileType; // Menggunakan kode unik untuk nama file
                // $targetFilePath = $targetDir . $uniqueFileName;
                // // Memeriksa tipe file yang diunggah
                // $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
                // if(in_array($fileType, $allowTypes)) {
                //     // Pindahkan file yang diunggah ke direktori tujuan
                //     if(move_uploaded_file($_FILES["bukti_tf"]["tmp_name"], $targetFilePath)) {
                //     }else{
                //         sleep(2);
                //         $alert_error = "Gagal mengunggah file.";
                //     }
                // }else{
                //     sleep(2);
                //     $alert_error = "Format file yang diperbolehkan: JPG, JPEG, PNG, GIF, PDF.";
                // }
            }else{
                sleep(2);
                $alert_error = "This field is required.";
            }
        }else{
            sleep(2);
            $alert_error = "You have a pending deposit";
        }
    }
    if(isset($_POST['withdraw'])){
        $withdrawDate = round(microtime(true) * 1000);
        $checkPaketBasic = $paketNonPremiumTableClass->selectPaket(
            fields:"paket_name, paket_date",
            key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
        );
        if($checkPaketBasic['data'][0]['paket_name'] == "Free"){
            $dateEnd = 0;
        }elseif($checkPaketBasic['data'][0]['paket_name'] == "Membership"){
            $dateEnd = $checkPaketBasic['data'][0]['paket_date'] + (30*24*60*60*1000);
        }
        if($dateEnd >= $withdrawDate){
            $checkWd = $withdrawTableClass->selectWithdraw("withdraw_user_id", "withdraw_user_id = '$userAds' AND withdraw_status = 'Pending' LIMIT 1");
            if($checkWd['row'] == 0){
                $bankUserWd = trim(htmlspecialchars($_POST['bankUserWd']));
                $mount = $_POST['mount'] == "" ? "0" : trim(htmlspecialchars($_POST['mount']));
                if($mount > 0){
                    // $saldo = getWallet()['user_saldo'];
                    if($totalBalance >= $mount){
                        if($mount >= $minWD){
                            $wdId = generateUniqueWDId();
        
                            $fields = "withdraw_id, withdraw_user_id, withdraw_nominal, withdraw_fee_admin, withdraw_bank_user, withdraw_date";
                            $values = "'$wdId', '$userAds', '$mount', $feeWD, '$bankUserWd', '$withdrawDate'";
                            if($withdrawTableClass->insertWithdraw($fields, $values)) {
                                sleep(2);
                                $userData = memberName($userAds);
                                sendMessage("wd", $userData, "", $mount, "");
                                $_SESSION['alert_success'] = "Withdraw successful!";
                                header("Location: home");
                                exit();
                            }else{
                                sleep(2);
                                $alert_error = "Data saving failed.";
                            }
                            // $maxWd = $checkPaketBayar['data'][0]['paket_nominal'];
                            // $sisaWd = sisaWD($userAds, $maxWd);
                            // if($mount <= $sisaWd){
                            // }else{
                            //     sleep(2);
                            //     $alert_error = "Sisa WD harian Rp" . number_format($sisaWd) . " (Max WD perhari Rp" . number_format($maxWd) . ")";
                            // }
                        }else{
                            sleep(2);
                            $alert_error = "Min WD " . number_format($minWD) . " USDT";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "You do not have enough balance.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "This field is required.";
                }
            }else{
                sleep(2);
                $alert_error = "You have a pending Withdraw.";
            }
            // if($checkPaketBayar['data'][0]['paket_name'] != "Magang"){
            // }else{
            //     sleep(2);
            //     $alert_error = "Minimal berlangganan 1 paket berbayar.";
            // }
        }else{
            sleep(2);
            $alert_error = "You are not yet registered as a member.";
        }
    }
    if(isset($_POST['klaim'])){
        $adsID = trim(htmlspecialchars($_POST['adsID']));
        $adsType = trim(htmlspecialchars($_POST['adsType']));
        $dateNowUTC = round(microtime(true) * 1000);
        if($adsID != ""){
            if($adsType == "basic"){
                $paketBasicUser = $paketNonPremiumTableClass->selectPaket(
                    fields:"paket_name, paket_date",
                    key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
                );
                $dateEnd = $paketBasicUser['data'][0]['paket_date'] + (7*24*60*60*1000);
                if($dateEnd >= $dateNowUTC && $paketBasicUser['data'][0]['paket_name'] == "Free"){
                    $checkAds = $adsBasicUserTableClass->selectAds("ads_reward", "ads_id = '$adsID' AND ads_status = 'Aktif' AND ads_user_id = '$userAds' LIMIT 1");
                    if($checkAds['row'] > 0){
                        $nominalRW = $checkAds['data'][0]['ads_reward'];
                        $proftId = generateUniqueProfitId();
                        $insertProfit = $profitTableClass->insertProfit(
                            fields:"
                                profit_id,
                                profit_user_id,
                                profit_ads_id,
                                profit_nominal,
                                profit_type,
                                profit_date
                            ",
                            value:"
                                '$proftId',
                                '$userAds',
                                '$adsID',
                                '$nominalRW',
                                'Basic',
                                '$dateNowUTC'
                            "
                        );
                        if($insertProfit){
                            $updateStatusAds = $adsBasicUserTableClass->updateAds(
                                dataSet:"ads_status = 'Tidak Aktif'",
                                key:"ads_id = '$adsID'"
                            );
                            if($updateStatusAds){
                                $saldo = getWallet()['user_saldo_token'];
                                $saldoNow = $nominalRW + $saldo;
                                $updateWallet = $walletUserTableClass->updateWalletUser(
                                    dataSet:"user_saldo_token = '$saldoNow'",
                                    key:"user_refferal = '$userAds'"
                                );
                                if($updateWallet){
                                    // bonus upline
                                    sleep(2);
                                    $_SESSION['alert_success'] = "Klaim successful!";
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
                    $alert_error = "Your trial period is over.";
                }

            }elseif($adsType == "premium"){
                $paketBasicUser = $paketNonPremiumTableClass->selectPaket(
                    fields:"paket_name, paket_date",
                    key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
                );
                $dateEnd = $paketBasicUser['data'][0]['paket_date'] + (37*24*60*60*1000);
                if($dateEnd >= $dateNowUTC && $paketBasicUser['data'][0]['paket_name'] == "Membership"){
                    $checkAds = $adsUserTableClass->selectAds("ads_reward", "ads_id = '$adsID' AND ads_status = 'Aktif' AND ads_user_id = '$userAds' LIMIT 1");
                    if($checkAds['row'] > 0){
                        $nominalRW = $checkAds['data'][0]['ads_reward'];
                        if(isLimitProfit($userAds, $nominalRW)){
                            sleep(2);
                            $alert_error = "Your Premium Package has Reached Its Limit";
                        }else{
                            $proftId = generateUniqueProfitId();
                            $insertProfit = $profitTableClass->insertProfit(
                                fields:"
                                    profit_id,
                                    profit_user_id,
                                    profit_ads_id,
                                    profit_nominal,
                                    profit_type,
                                    profit_date
                                ",
                                value:"
                                    '$proftId',
                                    '$userAds',
                                    '$adsID',
                                    '$nominalRW',
                                    'Premium',
                                    '$dateNowUTC'
                                "
                            );
                            if($insertProfit){
                                $updateStatusAds = $adsUserTableClass->updateAds(
                                    dataSet:"ads_status = 'Tidak Aktif'",
                                    key:"ads_id = '$adsID'"
                                );
                                if($updateStatusAds){
                                    // $saldo = getWallet()['user_saldo'];
                                    // $saldoNow = $nominalRW + $saldo;
                                    $updateWallet = true;
                                    if($updateWallet){
                                        // update bonus upline
                                        $userAdsUpline = getUser($userAds)['data'][0]['user_upline'];
                                        $lvl = 1;
                                        $giveBonus = bonusUpline($userAds, $userAdsUpline, $lvl, $nominalRW, $dateNowUTC);
                                        if($giveBonus){
                                            sleep(2);
                                            $_SESSION['alert_success'] = "Klaim successful!";
                                            header("Location: home");
                                            exit();
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
                    }else{
                        sleep(2);
                        $alert_error = "Error.";
                    }
                }else{
                    sleep(2);
                    $alert_error = "You are not yet registered as a member.";
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

function isLimitProfit($user, $addBonus = 0){

    global $bonusTableClass;
    global $bonusMatchingTableClass;
    global $profitTableClass;
    global $paketTableClass;

    $bonusSponsor = $bonusTableClass->selectBonus(
        fields: "SUM(bonus_nominal) AS total",
        key: "bonus_user_id = '$user'"
    )['data'][0]['total'];

    $bonusMatching = $bonusMatchingTableClass->selectBonus(
        fields: "SUM(bonus_nominal) AS total",
        key: "bonus_user_id = '$user'"
    )['data'][0]['total'];

    $bonusVideo = $profitTableClass->selectProfit(
        fields: "SUM(profit_nominal) AS total",
        key: "profit_user_id = '$user' AND profit_type = 'Premium'"
    )['data'][0]['total'];

    $totalProfit = $bonusSponsor + $bonusMatching + $bonusVideo + $addBonus;

    $paketUser = $paketTableClass->selectPaket(
        fields:"SUM(paket_nominal) AS total",
        key:"paket_user_id = '$user'"
    )['data'][0]['total'];

    $totalLimit = $paketUser * 2.5;

    if($totalProfit >= $totalLimit){
        return true;
    }

    return false;
}

function totalBalance(){

    global $depositTableClass;
    global $profitTableClass;
    global $bonusTableClass;
    global $bonusMatchingTableClass;
    global $paketTableClass;
    global $paketNonPremiumTableClass;
    global $withdrawTableClass;
    $userAds = $_SESSION['user_ads'];

    $getDeposit = $depositTableClass->selectDeposit(
        fields: "SUM(deposit_nominal) AS total", 
        key: "deposit_user_id = '$userAds' AND deposit_status = 'Success'"
    )['data'][0]['total'];
    
    $getProfit = $profitTableClass->selectProfit(
        fields: "SUM(profit_nominal) AS total", 
        key: "profit_user_id = '$userAds' AND profit_type = 'Premium'"
    )['data'][0]['total'];
    
    $getBonusSponsor = $bonusTableClass->selectBonus(
        fields: "SUM(bonus_nominal) AS total", 
        key: "bonus_user_id = '$userAds'"
    )['data'][0]['total'];
    
    $getBonusMatching = $bonusMatchingTableClass->selectBonus(
        fields: "SUM(bonus_nominal) AS total", 
        key: "bonus_user_id = '$userAds'"
    )['data'][0]['total'];

    $totalMasuk = $getDeposit + $getProfit + $getBonusSponsor + $getBonusMatching;
    
    $getPaket = $paketTableClass->selectPaket(
        fields: "SUM(paket_nominal) AS total", 
        key: "paket_user_id = '$userAds'"
    )['data'][0]['total'];
    
    $getPaketBasic = $paketNonPremiumTableClass->selectPaket(
        fields: "SUM(paket_nominal) AS total", 
        key: "paket_user_id = '$userAds'"
    )['data'][0]['total'];
    
    $getWithdraw = $withdrawTableClass->selectWithdraw(
        fields: "SUM(withdraw_nominal) AS total", 
        key: "withdraw_user_id = '$userAds' AND withdraw_status = 'Success'"
    )['data'][0]['total'];

    $totalKeluar = $getPaket + $getPaketBasic + $getWithdraw;

    $result = $totalMasuk - $totalKeluar;

    return $result;

}

function getWallet(){
    global $walletUserTableClass;
    $userAds = $_SESSION['user_ads'];

    $data = $walletUserTableClass->selectWalletUser(
        fields:"user_saldo, user_saldo_token",
        key:"user_refferal = '$userAds'"
    );
    if($data['row'] > 0){
        return $data['data'][0];
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

function bonusUpline($userAds, $userAdsUpline, $lvl, $nominal, $dateNow){
    // global $paketTableClass;
    global $settingsMatchingTableClass;
    global $walletUserTableClass;
    global $bonusMatchingTableClass;
    global $paketTableClass;
    if($nominal > 0){
        if($userAdsUpline != "NONE"){
            if($lvl <= 20){
                if(paketBasicUserIsActive($userAdsUpline, $dateNow)){
                    $paketPremiumUpline = $paketTableClass->selectPaket(
                        fields:"paket_name, paket_date",
                        key:"paket_user_id = '$userAdsUpline' ORDER BY paket_date DESC LIMIT 1"
                    );
                    if($paketPremiumUpline['row'] == 0){
                        return true;
                    }else{
                        $pakeLvl = "Level " . $lvl;
                        $getPercentBonus = $settingsMatchingTableClass->selectBonus(
                            fields:"bonus_persen AS percen", 
                            key:"bonus_lvl = '$pakeLvl'"
                        );
                        $percenBonus = $getPercentBonus['data'][0]['percen'] / 100;
                        $totalBonus = ($nominal * 0.2) * $percenBonus;
                        // $dataWalletUpline = $walletUserTableClass->selectWalletUser(
                        //     fields:"user_saldo",
                        //     key:"user_refferal = '$userAdsUpline'"
                        // );
                        // $saldoUpline = $dataWalletUpline['data'][0]['user_saldo'] + $totalBonus;
                        $updateWalletUpline = true;
                        if($updateWalletUpline){
                            $bonusId = generateUniqueBonusId();
                            $strLVL = "LEVEL " . $lvl; 
                            $insertReport = $bonusMatchingTableClass->insertBonus(
                                fields:"
                                        bonus_id, 
                                        bonus_user_id, 
                                        bonus_nominal, 
                                        bonus_persen, 
                                        bonus_user_downline,
                                        bonus_level,
                                        bonus_date
                                    ",
                                value:"
                                        '$bonusId',
                                        '$userAdsUpline',
                                        '$totalBonus',
                                        '$percenBonus',
                                        '$userAds',
                                        '$strLVL',
                                        '$dateNow'
                                    "
                            );
                            if($insertReport){
                                $uplineTwo = getUser($userAdsUpline)['data'][0]['user_upline'];
                                return bonusUpline($userAdsUpline, $uplineTwo, $lvl+1, $nominal, $dateNow);
                            }
                        }
                    }
                }else{
                    return true;
                }
            }else{
                return true;
            }
        }else{
            return true;
        }
    }else{
        return true;
    }

}

function paketBasicUserIsActive($userAds, $dateNowUTC){
    global $paketNonPremiumTableClass;
    $paketUser = $paketNonPremiumTableClass->selectPaket(
        fields:"paket_name, paket_date",
        key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
    );
    $result = true;
    if($paketUser['data'][0]['paket_name'] == "Free"){
        $result = false;
    }elseif($paketUser['data'][0]['paket_name'] == "Membership"){
        $dateEnd = $paketUser['data'][0]['paket_date'] + (37*24*60*60*1000);
        if($dateEnd < $dateNowUTC){
            $result = false;
        }
    }
    return $result;
}

function getUser($userAds){
    global $userTableClass;
    $data = $userTableClass->selectUser(
        fields:"user_upline",
        key:"user_refferal = '$userAds' LIMIT 1"
    );
    return $data;
}

function generateUniqueBonusId(){
    global $bonusMatchingTableClass;
    $bonusId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $data = $bonusMatchingTableClass->selectBonus(
        fields:"bonus_id",
        key:"bonus_id = '$bonusId'"
    );
    if ($data['row'] > 0) {
        return generateUniqueBonusId();
    } else {
        return $bonusId;
    }
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
    global $paketNonPremiumTableClass;
    global $lastDayOfMonth;
    global $firstDayOfMonth;
    $paketUser = $paketTableClass->selectPaket(
        fields:"SUM(paket_nominal) total",
        key:"
            paket_estimasi = 'Berbayar' 
            AND (
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$firstDayOfMonth 00:00:00' AND 
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$lastDayOfMonth 23:59:59'
            )
        "
    );
    $paketBasicUser = $paketNonPremiumTableClass->selectPaket(
        fields:"SUM(paket_nominal) total",
        key:"
            paket_name <> 'Free' 
            AND (
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$firstDayOfMonth 00:00:00' AND 
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$lastDayOfMonth 23:59:59'
            )
        "
    );

    return $paketUser['data'][0]['total'] + $paketBasicUser['data'][0]['total'];
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
    global $adsBasicUserTableClass;
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
    }else{
        $paket = getPaketBasicUser();
        foreach($paket['data'] as $row){
            $date = $row['paket_ads_stop_date'] - (1*24*60*60*1000);
            $paketId = $row['paket_id'];
            $jumlahAds = $row['paket_jumlah_tugas'];
            $dataAktif = $adsBasicUserTableClass->selectAds("COUNT(ads_id) AS total", "ads_status = 'Aktif' AND ads_paket_id = '$paketId' AND ads_user_id = '$userAds' AND ads_date = '$date' ORDER BY ads_date DESC LIMIT $jumlahAds");
            if($dataAktif['row'] > 0){
                $totalAdsAktif += $dataAktif['data'][0]['total'];
            }
            $data = $adsBasicUserTableClass->selectAds("COUNT(ads_id) AS total", "ads_paket_id = '$paketId' AND ads_user_id = '$userAds' AND ads_date = '$date' ORDER BY ads_date DESC LIMIT $jumlahAds");
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

function getPaketBasicUser(){
    global $paketNonPremiumTableClass;
    $userAds = $_SESSION['user_ads'];
    $paketUser = $paketNonPremiumTableClass->selectPaket(
        fields:"paket_id, paket_user_id, paket_nominal, paket_name, paket_reward_tugas_satu, paket_reward_tugas_dua, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
        key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
    );
    return $paketUser;
}

function createdAdsBasicPaket(){
    $paket = getPaketBasicUser();
    if($paket['row'] > 0){
        foreach($paket['data'] as $row){
            // tgl pengisian
            $paketId = $row['paket_id'];
            $paketNominal = $row['paket_nominal'];
            $namePaket = $row['paket_name'];
            $reward_satu = $row['paket_reward_tugas_satu'];
            $reward_dua = $row['paket_reward_tugas_dua'];
            $jumlahAds = $row['paket_jumlah_tugas'];
            $endDay = $row['paket_ads_stop_date'];
            $dateBuy = $row['paket_date'];
            $inputAds = inputAdsBasic($paketId, $paketNominal, $namePaket, $reward_satu, $reward_dua, $jumlahAds, $endDay, $dateBuy);
        }
    }
}

function inputAdsBasic($paketId, $paketNominal, $namePaket, $reward_satu, $reward_dua, $jumlahAds, $endDay, $dateBuy){
    global $adsBasicUserTableClass;
    global $paketNonPremiumTableClass;
    // tgl hari ini
    $thisDay = round(microtime(true) * 1000);
    if($thisDay >= $endDay){
        $process = true;
        // check stqtus user 30 hari untuk membership dan 7 hari untuk free
        // $roleUser = userDataa();
        // if($roleUser == "Free"){
        //     $stopads = 7*24*60*60*1000;
        // }elseif($roleUser == "Membership"){
        //     $stopads = 30*24*60*60*1000;
        // }
        // if($endDay - $dateBuy > $stopads){
        //     $process = false;
        // }

        if($process){
            $userAds = $_SESSION['user_ads'];
            $nameAds = "YouTube";
            $linkAds = "tes";
            for ($i = 1; $i <= $jumlahAds; $i++) {
                $idAds = generateUniqueAdsBasicId();
                if($i == 1){
                    $rewardPerAds = $reward_satu;
                }elseif($i == 2){
                    $rewardPerAds = $reward_dua;
                }
                $adsBasicUserTableClass->insertAds(
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
            $updateEndDay = $paketNonPremiumTableClass->updatePaket(dataSet:"paket_ads_stop_date = '$nextDay'",key:"paket_id = '$paketId'");
            if($updateEndDay){
                return inputAdsBasic($paketId, $paketNominal, $namePaket, $reward_satu, $reward_dua, $jumlahAds, $nextDay, $dateBuy);
            }
        }else{
            return true;
        }
    }else{
        return true;
    }

}

function getPaketUser(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];
    $paketUser = $paketTableClass->selectPaket(
        fields:"paket_id, paket_user_id, paket_nominal, paket_name, paket_reward_tugas_satu, paket_reward_tugas_dua, paket_reward_tugas_tiga, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
        key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
    );
    return $paketUser;
}

function createdAds(){
    $paket = getPaketUser();
    if($paket['row'] > 0){
        foreach($paket['data'] as $row){
            // tgl pengisian
            $paketId = $row['paket_id'];
            $paketNominal = $row['paket_nominal'];
            $namePaket = $row['paket_name'];
            $reward_satu = $row['paket_reward_tugas_satu'];
            $reward_dua = $row['paket_reward_tugas_dua'];
            $reward_tiga = $row['paket_reward_tugas_tiga'];
            $jumlahAds = $row['paket_jumlah_tugas'];
            $endDay = $row['paket_ads_stop_date'];
            $dateBuy = $row['paket_date'];
            $inputAds = inputAds($paketId, $paketNominal, $namePaket, $reward_satu, $reward_dua, $reward_tiga, $jumlahAds, $endDay, $dateBuy);
        }
    }
}

function inputAds($paketId, $paketNominal, $namePaket, $reward_satu, $reward_dua, $reward_tiga, $jumlahAds, $endDay, $dateBuy){
    global $adsUserTableClass;
    global $paketTableClass;
    // tgl hari ini
    $thisDay = round(microtime(true) * 1000);
    if($thisDay >= $endDay){
        $process = true;
        // check stqtus user 30 hari untuk membership dan 7 hari untuk free
        // $roleUser = userDataa();
        // if($roleUser == "Free"){
        //     $stopaads = 7*24*60*60*1000;
        // }elseif($roleUser == "Membership"){
        //     $stopaads = 30*24*60*60*1000;
        // }
        // if($endDay - $dateBuy > $stopaads){
        //     $process = false;
        // }

        if($process){
            $userAds = $_SESSION['user_ads'];
            $nameAds = "YouTube";
            $linkAds = "tes";
            $rewardSatu = ($reward_satu/100)*$paketNominal;
            $rewardDua = ($reward_dua/100)*$paketNominal;
            $rewardTiga = ($reward_tiga/100)*$paketNominal;

            for ($i = 1; $i <= $jumlahAds; $i++) {
                $idAds = generateUniqueAdsId();
                if($i == 1){
                    $rewardPerAds = $rewardSatu;
                }elseif($i == 2){
                    $rewardPerAds = $rewardDua;
                }else{
                    $rewardPerAds = $rewardTiga;
                }
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
                return inputAds($paketId, $paketNominal, $namePaket, $reward_satu, $reward_dua, $reward_tiga, $jumlahAds, $nextDay, $dateBuy);
            }
        }else{
            return true;
        }
    }else{
        return true;
    }

}

function userDataa(){
    global $userTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $userTableClass->selectUser(
        fields:"user_role",
        key:"user_refferal = '$userAds' LIMIT 1"
    );
    return $data['data'][0]['user_role'];
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

function generateUniqueAdsBasicId() {
    global $adsBasicUserTableClass;
    $adsId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $check = $adsBasicUserTableClass->selectAds("ads_id", "ads_id = '$adsId' LIMIT 1");
    
    if ($check['row'] > 0) {
        return generateUniqueAdsBasicId();
    } else {
        return $adsId;
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

function getSumBonus(){
    global $bonusTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $bonusTableClass->selectBonus(
        fields:"SUM(bonus_nominal) total",
        key:"bonus_user_id = '$userAds'"
    );
    return number_format($data['data'][0]['total'],2);
}

function getSumBonusMatching(){
    global $bonusMatchingTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $bonusMatchingTableClass->selectBonus(
        fields:"SUM(bonus_nominal) total",
        key:"bonus_user_id = '$userAds'"
    );
    return number_format($data['data'][0]['total'],2);
}

function getSumProfit(){
    global $profitTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $profitTableClass->selectProfit(
        fields:"SUM(profit_nominal) total",
        key:"profit_user_id = '$userAds' AND profit_type = 'Premium'"
    );
    return number_format($data['data'][0]['total'],2);
}
function getSumProfitBasic(){
    global $profitTableClass;
    $userAds = $_SESSION['user_ads'];
    $data = $profitTableClass->selectProfit(
        fields:"SUM(profit_nominal) total",
        key:"profit_user_id = '$userAds' AND profit_type = 'Basic'"
    );
    return number_format($data['data'][0]['total'],2);
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