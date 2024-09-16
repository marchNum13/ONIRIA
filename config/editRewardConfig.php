<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}
if($_SESSION['user_role'] != "Admin"){
    header('Location: home');
    exit();
}

include "databaseClass/connMySQLClass.php";
include "databaseClass/settingsPaketTableClass.php";

$settingsPaketTableClass = new settingsPaketTableClass();

$getPaketReward = getPaketReward();

$basicVideoSatu = $getPaketReward[0]["settings_reward_tugas_satu"];
$basicVideoDua = $getPaketReward[0]["settings_reward_tugas_dua"];

$premiumSatuVideoSatu = $getPaketReward[1]["settings_reward_tugas_satu"];
$premiumSatuVideoDua = $getPaketReward[1]["settings_reward_tugas_dua"];

$premiumDuaVideoSatu = $getPaketReward[2]["settings_reward_tugas_satu"];
$premiumDuaVideoDua = $getPaketReward[2]["settings_reward_tugas_dua"];
$premiumDuaVideoTiga = $getPaketReward[2]["settings_reward_tugas_tiga"];

if(isset($_POST['submit'])){
    $basicVideoSatu = trim($_POST["videoBasic1"]);
    $basicVideoDua = trim($_POST["videoBasic2"]);
    
    $premiumSatuVideoSatu = trim($_POST["videoPremiumSatu1"]);
    $premiumSatuVideoDua = trim($_POST["videoPremiumSatu2"]);
    
    $premiumDuaVideoSatu = trim($_POST["videoPremiumDua1"]);
    $premiumDuaVideoDua = trim($_POST["videoPremiumDua2"]);
    $premiumDuaVideoTiga = trim($_POST["videoPremiumDua3"]);

    $inputs = [
        'video 1 Paket Free / Membership' => $basicVideoSatu,
        'video 2 Paket Free / Membership' => $basicVideoDua,
        'video 1 Premium $100' => $premiumSatuVideoSatu,
        'video 2 Premium $100' => $premiumSatuVideoDua,
        'video 1 Premium $500' => $premiumDuaVideoSatu,
        'video 2 Premium $500' => $premiumDuaVideoDua,
        'video 3 Premium $500' => $premiumDuaVideoTiga
    ];
    
    $kosong = array_filter($inputs, function($value) {
        return $value === '' || $value === null || $value == 0; // Memeriksa string kosong atau null
    });
    
    if (!empty($kosong)) {
        sleep(2);
        $alert_error = "Terdapat input yang kosong: " . implode(', ', array_keys($kosong));
    } else{
        // update basic
        $updateBsic = $settingsPaketTableClass->updatePaket(
            dataSet: "settings_reward_tugas_satu = '$basicVideoSatu', settings_reward_tugas_dua = '$basicVideoDua'",
            key: "settings_nama_paket = 'Membership'"
        );
        // update Premium 1
        $updatePremiumSatu = $settingsPaketTableClass->updatePaket(
            dataSet: "settings_reward_tugas_satu = '$premiumSatuVideoSatu', settings_reward_tugas_dua = '$premiumSatuVideoDua'",
            key: "settings_nama_paket = 'Premium 1'"
        );
        // update Premium 2
        $updatePremiumDua = $settingsPaketTableClass->updatePaket(
            dataSet: "settings_reward_tugas_satu = '$premiumDuaVideoSatu', settings_reward_tugas_dua = '$premiumDuaVideoDua', settings_reward_tugas_tiga = '$premiumDuaVideoTiga'",
            key: "settings_nama_paket = 'Premium 2'"
        );
        sleep(2);
        $_SESSION['alert_success'] = "Data berhasil diubah.";
        header("Location: edit-rewards");
        exit();
    }
    
}

function getPaketReward(){
    global $settingsPaketTableClass;

    $data = $settingsPaketTableClass->selectPaket(
        fields: "settings_reward_tugas_satu, settings_reward_tugas_dua, settings_reward_tugas_tiga",
        key: "1"
    );

    return $data['data'];
}

?>