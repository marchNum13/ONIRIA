<?php  
// $dataArray = array(
//     "nama_paket" => [
//         "Magang",
//         "Paket 1",
//         "Paket 2",
//         "Paket 3",
//         "Paket 4",
//         "Paket 5",
//         "Paket 6",
//         "Paket 7"
//     ],
//     "harga_paket" => [
//         "0",
//         "270000",
//         "1100000",
//         "2500000",
//         "5500000",
//         "7500000",
//         "13500000",
//         "21000000"
//     ],
//     "reward_tugas" => [
//         "1800",
//         "1800",
//         "3700",
//         "4200",
//         "4700",
//         "5200",
//         "5700",
//         "6500"
//     ],
//     "jumlah_tugas" => [
//         "5",
//         "5",
//         "10",
//         "20",
//         "40",
//         "50",
//         "80",
//         "110"
//     ]
// );
// // var_dump($dataArray['nama_paket']);
// foreach($dataArray['nama_paket'] as $key => $paket){
//     echo $paket . ', ';
//     echo $dataArray['harga_paket'][$key] . ', <br>';
// }

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

?>