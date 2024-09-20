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

include "databaseClass/userTableClass.php";
include "databaseClass/walletUserTableClass.php";
include "databaseClass/depositTableClass.php";
include "databaseClass/profitTableClass.php";
include "databaseClass/bonusTableClass.php";
include "databaseClass/bonusMatchingTableClass.php";
include "databaseClass/paketTableClass.php";
include "databaseClass/paketNonPremiumTableClass.php";
include "databaseClass/withdrawTableClass.php";

$userTableClass = new userTableClass();
$walletUserTableClass = new walletUserTableClass();
$page = isset($_GET['page']) ? $_GET['page'] : '1'; // number page

function totalBalance($userAds){

    $depositTableClass = new depositTableClass();
    $profitTableClass = new profitTableClass();
    $bonusTableClass = new bonusTableClass();
    $bonusMatchingTableClass = new bonusMatchingTableClass();
    $paketTableClass = new paketTableClass();
    $paketNonPremiumTableClass = new paketNonPremiumTableClass();
    $withdrawTableClass = new withdrawTableClass();

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

function tableData($page){
    global $userTableClass;
    $start = 10 * ($page - 1);
    $data = $userTableClass->selectUser(
        fields:"
            user_username,
            user_refferal,
            user_upline
        ",
        key:"user_role <> 'Admin' ORDER BY id DESC LIMIT $start, 10"
    );

    return $data;
}

function tableCount(){
    global $userTableClass;
    $data = $userTableClass->selectUser(
        fields:"
            COUNT(user_username) AS total
        ",
        key:"user_role <> 'Admin'"
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