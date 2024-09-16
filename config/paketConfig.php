<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}

include "databaseClass/connMySQLClass.php";

include "databaseClass/userTableClass.php";
include "databaseClass/settingsPaketTableClass.php";
include "databaseClass/paketTableClass.php";
include "databaseClass/paketNonPremiumTableClass.php";
include "databaseClass/walletUserTableClass.php";
include "databaseClass/settingsBonusTableClass.php";
include "databaseClass/bonusTableClass.php";
include "databaseClass/bonusMatchingTableClass.php";
include "databaseClass/profitTableClass.php";
include "apiTele.php";

$userTableClass = new userTableClass();
$settingsPaketTableClass = new settingsPaketTableClass();
$paketTableClass = new paketTableClass();
$paketNonPremiumTableClass = new paketNonPremiumTableClass();
$walletUserTableClass = new walletUserTableClass();
$settingsBonusTableClass = new settingsBonusTableClass();
$bonusTableClass = new bonusTableClass();
$bonusMatchingTableClass = new bonusMatchingTableClass();
$profitTableClass = new profitTableClass();

$dataPaket = dataPaket();

$alert_error = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userAds = $_SESSION['user_ads'];
    if(isset($_POST['buyPaket'])){
        $namePaket = $_POST["namePaket"];
        $checkPaket = $settingsPaketTableClass->selectPaket(
            fields:"settings_nama_paket, settings_harga_paket, settings_reward_tugas_satu, settings_reward_tugas_dua, settings_reward_tugas_tiga, settings_jumlah_tugas",
            key:"settings_nama_paket = '$namePaket' LIMIT 1"
        );
        if($checkPaket['row'] > 0){
            $dateNow = round(microtime(true) * 1000);
            $nominal = $checkPaket['data'][0]['settings_harga_paket'];
            $paket = $checkPaket['data'][0]['settings_nama_paket'];
            $rewardSatu = $checkPaket['data'][0]['settings_reward_tugas_satu'];
            $rewardDua = $checkPaket['data'][0]['settings_reward_tugas_dua'];
            $rewardTiga = $checkPaket['data'][0]['settings_reward_tugas_tiga'];
            $jumlah = $checkPaket['data'][0]['settings_jumlah_tugas'];
            $estimasi = "Berbayar";
            $saldo = getWallet();
            if($saldo >= $nominal){
                if($namePaket == "Membership"){
                    $idPaketBuy = generateUniquePaketBasicId();
                    $insertBaicPaket = $paketNonPremiumTableClass->insertPaket(
                        fields:"paket_id, paket_user_id, paket_nominal, paket_name, paket_reward_tugas_satu, paket_reward_tugas_dua, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
                        value:"'$idPaketBuy', '$userAds', '$nominal', '$paket', '$rewardSatu', '$rewardDua', '$jumlah', '$dateNow', '$dateNow'"
                    );
                    if($insertBaicPaket){
                        $updateRole = $userTableClass->updateUser("user_role = 'Membership'", "user_refferal = '$userAds'");
                        if($updateRole){
                            $saldoNow = $saldo - $nominal;
                            $updateWallet = $walletUserTableClass->updateWalletUser(
                                dataSet:"user_saldo = '$saldoNow'",
                                key:"user_refferal = '$userAds'"
                            );
                            if($updateWallet){
                                sleep(2);
                                $userBuy = memberName($userAds);
                                sendMessage("buyPaket", $userBuy, "", $nominal, $paket);
                                $_SESSION['alert_success'] = "Membership anda telah aktif.";
                                header("Location: paket");
                                exit();
                            }
                        }
                    }
                }else{
                    $idPaketBuy = generateUniquePaketBuyId();
                    $insertBuyPaket = $paketTableClass->insertPaket(
                        fields:"paket_id, paket_user_id, paket_nominal, paket_estimasi, paket_name, paket_reward_tugas_satu, paket_reward_tugas_dua, paket_reward_tugas_tiga, paket_jumlah_tugas, paket_ads_stop_date, paket_date",
                        value:"'$idPaketBuy', '$userAds', '$nominal', '$estimasi', '$paket', '$rewardSatu', '$rewardDua', '$rewardTiga', '$jumlah', '$dateNow', '$dateNow'"
                    );
                    if($insertBuyPaket){
                        $saldoNow = $saldo - $nominal;
                        $updateWallet = $walletUserTableClass->updateWalletUser(
                            dataSet:"user_saldo = '$saldoNow'",
                            key:"user_refferal = '$userAds'"
                        );
                        if($updateWallet){
                            $userAdsUpline = getUser($userAds)['data'][0]['user_upline'];
                            $lvl = 1;
                            $giveBonus = bonusUpline($userAds, $userAdsUpline, $lvl, $nominal, $dateNow);
                            if($giveBonus){
                                sleep(2);
                                $userBuy = memberName($userAds);
                                sendMessage("buyPaket", $userBuy, "", $nominal, $paket);
                                $_SESSION['alert_success'] = "Pembelian berhasil.";
                                header("Location: paket");
                                exit();
                            }
                        }                    
                    }
                }
            }else{
                sleep(2);
                $alert_error = "Saldo tidak cukup.";
            }
        }else{
            sleep(2);
            $alert_error = "Gagal melakukan pembelian.";
        }
    }
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

// function getPriceCuan(){
//     // URL untuk mendapatkan harga terbaru token
//     $priceUrl = 'https://openapi.bittime.com/api/v1/ticker/price?symbol=CUANIDR';

//     $curl = curl_init();

//     curl_setopt_array($curl, array(
//         CURLOPT_URL => $priceUrl,
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => "",
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 30,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => "GET"
//     ));

//     $response = curl_exec($curl);
//     $err = curl_error($curl);

//     curl_close($curl);

//     if($err){
//         return "error";
//     }else{
//         $array = json_decode($response,TRUE);
//         return $array['price'];
//     }
// }

function memberName($id){
    global $userTableClass;
    $data = $userTableClass->selectUser("user_username", "user_refferal = '$id' LIMIT 1");
    if($data['row'] > 0){
        return $data['data'][0]['user_username'];
    }else{
        return "Unknown";
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

function checkPaketBerbayar(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];
    $showPaket = true;
    $data = $paketTableClass->selectPaket(
        fields:"paket_user_id",
        key:"paket_user_id = '$userAds'"
    );
    if($data['row'] > 0){
        if(isLimitProfit($userAds)){
            $showPaket = true;
        }else{
            $showPaket = false;
        }
    }

    return $showPaket;
}

function paketBasicUserIsActive(){
    global $paketNonPremiumTableClass;
    $userAds = $_SESSION['user_ads'];
    $paketUser = $paketNonPremiumTableClass->selectPaket(
        fields:"paket_name, paket_date",
        key:"paket_user_id = '$userAds' ORDER BY paket_date DESC LIMIT 1"
    );
    $result = true;
    if($paketUser['data'][0]['paket_name'] == "Free"){
        $result = false;
    }elseif($paketUser['data'][0]['paket_name'] == "Membership"){
        $dateEnd = $paketUser['data'][0]['paket_date'] + (30*24*60*60*1000);
        $dateNowUTC = round(microtime(true) * 1000);
        if($dateEnd < $dateNowUTC){
            $result = false;
        }
    }
    return $result;
}

function bonusUpline($userAds, $userAdsUpline, $lvl, $nominal, $dateNow){
    // global $paketTableClass;
    global $settingsBonusTableClass;
    global $walletUserTableClass;
    global $bonusTableClass;
    global $paketNonPremiumTableClass;
    if($nominal > 0){
        if($userAdsUpline != "NONE"){
            if($lvl <= 5){
                $paketBsicUpline = $paketNonPremiumTableClass->selectPaket(
                    fields:"paket_name, paket_date",
                    key:"paket_user_id = '$userAdsUpline' ORDER BY paket_date DESC LIMIT 1"
                );
                if($paketBsicUpline['data'][0]['paket_name'] == "Free"){
                    $dateEnd = $paketBsicUpline['data'][0]['paket_date'] + (7*24*60*60*1000);
                }elseif($paketBsicUpline['data'][0]['paket_name'] == "Membership"){
                    $dateEnd = $paketBsicUpline['data'][0]['paket_date'] + (30*24*60*60*1000);
                }
                if($dateEnd < $dateNow){
                    return true;
                }else{
                    $pakeLvl = "Level " . $lvl;
                    $getPercentBonus = $settingsBonusTableClass->selectBonus(
                        fields:"bonus_persen AS percen", 
                        key:"bonus_lvl = '$pakeLvl'"
                    );
                    $percenBonus = $getPercentBonus['data'][0]['percen'] / 100;
                    $totalBonus = $nominal * $percenBonus;
                    $dataWalletUpline = $walletUserTableClass->selectWalletUser(
                        fields:"user_saldo",
                        key:"user_refferal = '$userAdsUpline'"
                    );
                    $saldoUpline = $dataWalletUpline['data'][0]['user_saldo'] + $totalBonus;
                    $updateWalletUpline = $walletUserTableClass->updateWalletUser(
                        dataSet:"user_saldo = '$saldoUpline'",
                        key:"user_refferal = '$userAdsUpline'"
                    );
                    if($updateWalletUpline){
                        $bonusId = generateUniqueBonusId();
                        $strLVL = "LEVEL " . $lvl; 
                        $insertReport = $bonusTableClass->insertBonus(
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

}

function getUser($userAds){
    global $userTableClass;
    $data = $userTableClass->selectUser(
        fields:"user_upline",
        key:"user_refferal = '$userAds' LIMIT 1"
    );
    return $data;
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

function generateUniqueBonusId(){
    global $bonusTableClass;
    $bonusId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $data = $bonusTableClass->selectBonus(
        fields:"bonus_id",
        key:"bonus_id = '$bonusId'"
    );
    if ($data['row'] > 0) {
        return generateUniqueBonusId();
    } else {
        return $bonusId;
    }
}

function generateUniquePaketBuyId(){
    global $paketTableClass;
    $paketId = substr(uniqid(), -7); // Mengambil 7 karakter terakhir dari uniqid()
    $data = $paketTableClass->selectPaket(
        fields:"paket_id",
        key:"paket_id = '$paketId'"
    );
    if ($data['row'] > 0) {
        return generateUniquePaketBuyId();
    } else {
        return $paketId;
    }
}

function dataPaket(){
    global $settingsPaketTableClass;

    $data = $settingsPaketTableClass->selectPaket(
        fields:"id, settings_nama_paket, settings_harga_paket, settings_reward_tugas_satu, settings_reward_tugas_dua, settings_reward_tugas_tiga, settings_jumlah_tugas",
        key:"1"
    );

    return $data['data'];
}

function checkTrial(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];
    $trial = true;
    $checkTrial = $paketTableClass->selectPaket(
        fields:"paket_user_id",
        key:"paket_user_id = '$userAds'"
    );
    if($checkTrial['row'] > 0){
        $trial = false;
    }
    return $trial;
}

?>