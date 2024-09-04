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
include "databaseClass/walletUserTableClass.php";
include "databaseClass/settingsBonusTableClass.php";
include "databaseClass/bonusTableClass.php";
include "databaseClass/klimBonusTableClass.php";
include "apiTele.php";

$userTableClass = new userTableClass();
$settingsPaketTableClass = new settingsPaketTableClass();
$paketTableClass = new paketTableClass();
$walletUserTableClass = new walletUserTableClass();
$settingsBonusTableClass = new settingsBonusTableClass();
$bonusTableClass = new bonusTableClass();
$klimBonusTableClass = new klimBonusTableClass();

$dataPaket = dataPaket();
$checkTrial = checkTrial();

$alert_error = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userAds = $_SESSION['user_ads'];
    if(isset($_POST['buyPaket'])){
        $namePaket = $_POST["namePaket"];
        $checkPaket = $settingsPaketTableClass->selectPaket(
            fields:"settings_nama_paket, settings_harga_paket, settings_reward_tugas, settings_jumlah_tugas",
            key:"settings_nama_paket = '$namePaket' LIMIT 1"
        );
        if($checkPaket['row'] > 0){
            $dateNow = round(microtime(true) * 1000);
            $nominal = $checkPaket['data'][0]['settings_harga_paket'];
            $paket = $checkPaket['data'][0]['settings_nama_paket'];
            $reward = $checkPaket['data'][0]['settings_reward_tugas'];
            $jumlah = $checkPaket['data'][0]['settings_jumlah_tugas'];
            $estimasi = $checkPaket['data'][0]['settings_nama_paket'] == "Magang" ? "Trial" : "Berbayar";
            $saldo = getWallet();
            if($saldo >= $nominal){
                $inputPembelian = true;
                if($namePaket == "Magang"){
                    $inputPembelian = false;
                    $codeVC = $_POST["code"];
                    if($codeVC != ""){
                        $checkKlim = $klimBonusTableClass->selectKlimBonus(
                            fields: "klim_user_id",
                            key: "klim_user_id = '$userAds' AND klim_status = 'Pending'"
                        );
                        if($checkKlim['row'] == 0){
                            $insertKlim = $klimBonusTableClass->insertKlimBonus(
                                fields: "
                                    klim_user_id,
                                    klim_code,
                                    klim_date
                                ",
                                value: "
                                    '$userAds',
                                    '$codeVC',
                                    '$dateNow'
                                "
                            );
                            if($insertKlim){
                                sleep(2);
                                $userBuy = memberName($userAds);
                                sendMessage("klaimBonus", $userBuy, "", "", $paket, $codeVC);
                                $_SESSION['alert_success'] = "Klaim berhasil.";
                                header("Location: paket");
                                exit();
                            }
                        }else{
                            sleep(2);
                            $alert_error = "Klaim pending.";
                        }
                    }else{
                        sleep(2);
                        $alert_error = "Code tidak boleh kosong.";
                    }
                }
                if($inputPembelian){
                    $idPaketBuy = generateUniquePaketBuyId();
                    // harga cuan
                    $priceCuan = getPriceCuan() == "error" ? 100 : getPriceCuan();
                    // jumlah cuan
                    $jumlahCuan = $nominal/$priceCuan;
                    $oneYear = 12*30*24*60*60*1000;
                    $dateWd = $dateNow+$oneYear;
                    $insertBuyPaket = $paketTableClass->insertPaket(
                        fields:"paket_id, paket_user_id, paket_nominal, paket_nominal_cuan, paket_estimasi, paket_name, paket_reward_tugas, paket_jumlah_tugas, paket_ads_stop_date, paket_date_capitalback, paket_date",
                        value:"'$idPaketBuy', '$userAds', '$nominal', '$jumlahCuan', '$estimasi', '$paket', '$reward', '$jumlah', '$dateNow', '$dateWd', '$dateNow'"
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

function getPriceCuan(){
    // URL untuk mendapatkan harga terbaru token
    $priceUrl = 'https://openapi.bittime.com/api/v1/ticker/price?symbol=CUANIDR';

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $priceUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if($err){
        return "error";
    }else{
        $array = json_decode($response,TRUE);
        return $array['price'];
    }
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

function checkPaketBerbayar(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];
    $showPaket = true;
    $data = $paketTableClass->selectPaket(
        fields:"paket_user_id",
        key:"paket_user_id = '$userAds' AND paket_name <> 'Magang'"
    );
    if($data['row'] > 0){
        $showPaket = false;
    }

    return $showPaket;
}

function bonusUpline($userAds, $userAdsUpline, $lvl, $nominal, $dateNow){
    global $paketTableClass;
    global $settingsBonusTableClass;
    global $walletUserTableClass;
    global $bonusTableClass;
    if($nominal > 0){
        if($userAdsUpline != "NONE"){
            if($lvl <= 4){
                $checkPaketUpline = $paketTableClass->selectPaket(
                    fields:"paket_name, paket_date",
                    key:"paket_user_id = '$userAdsUpline' ORDER BY paket_name DESC LIMIT 1"
                );
                if($checkPaketUpline['row'] > 0){
                    $skip = false;
                    if($checkPaketUpline['data'][0]['paket_name'] == "Magang"){
                        $dateBuyPaketUpline = $checkPaketUpline['data'][0]['paket_date'];
                        $trialEstimasih = 4*24*60*60*1000;
                        $ends = $dateBuyPaketUpline + $trialEstimasih;
                        if($dateNow > $ends){
                            $skip = true;
                        }else{
                            $skip = false;
                        }
                    }
                    if(!$skip){
                        $fields = "";
                        $pakeUpline = $checkPaketUpline['data'][0]['paket_name'];
                        switch ($lvl) {
                            case '1':
                                $fields = "bonus_level_satu";
                                break;
                            case '2':
                                $fields = "bonus_level_dua";
                                break;
                            case '3':
                                $fields = "bonus_level_tiga";
                                break;
                            case '4':
                                $fields = "bonus_level_empat";
                                break;
                        }
                        $getPercentBonus = $settingsBonusTableClass->selectBonus(
                            fields:"$fields AS percen",
                            key:"bonus_nama_paket = '$pakeUpline'"
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
        fields:"id, settings_nama_paket, settings_harga_paket, settings_reward_tugas, settings_jumlah_tugas",
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