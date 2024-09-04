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
include "databaseClass/walletUserTableClass.php";

$userTableClass = new userTableClass();
$walletUserTableClass = new walletUserTableClass();
$page = isset($_GET['page']) ? $_GET['page'] : '1'; // number page


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