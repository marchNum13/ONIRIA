<?php  
// check login status
if($_SESSION['login_ads'] != true){
    header('Location: index');
    exit();
}

include "databaseClass/connMySQLClass.php";

include "databaseClass/depositTableClass.php";
$depositTableClass = new depositTableClass();

$dateNowMilis = round(microtime(true) * 1000); // UTC currentmillis
$date = dateTimeNow(currentmillisdate: $dateNowMilis); // localtime
$firstDayOfMonth = $date['firstDayOfMonth'];
$lastDayOfMonth = $date['lastDayOfMonth'];


// UTC TO LOCAL TIME FORMAT
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
?>