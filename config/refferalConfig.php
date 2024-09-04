<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}

include "databaseClass/connMySQLClass.php";

include "databaseClass/userTableClass.php";
include "databaseClass/paketTableClass.php";

$userTableClass = new userTableClass();
$paketTableClass = new paketTableClass();

function getPaketUser(){
    global $paketTableClass;
    $userAds = $_SESSION['user_ads'];
    $paketUser = $paketTableClass->selectPaket(
        fields:"paket_name",
        key:"paket_user_id = '$userAds' ORDER BY paket_name DESC LIMIT 1"
    );

    return $paketUser;
}

?>