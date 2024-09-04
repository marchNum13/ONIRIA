<?php  

function sendMessage(?string $typeText, ?string $member, ?string $upline, ?string $nominal, ?string $paket, ?string $CodePesanan = ""){
    $bot_token = "7388025466:AAHzTJLvKpS60_uWF1IAuqzopyVXxxpk-Bo";
    $apiUrl = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $text = "";

    if($typeText == "regis"){
        $text = "🎉 Selamat Datang!\n👤 Pengguna baru <strong>$member</strong> telah bergabung.\n🔗 Direkrut oleh: <strong>$upline</strong>";
    }elseif($typeText == "buyPaket"){
        $nominal = number_format($nominal);
        $text = "🎉 Transaksi Berhasil!\n👤 Pengguna: <strong>$member</strong>\n🎁 Paket: <strong>$paket</strong>\n💰 Harga: <strong>Rp$nominal</strong>";
    }elseif($typeText == "wd"){
        $nominal = number_format($nominal);
        $text = "⚠️ Perhatian!\n👤 Pengguna: <strong>$member</strong>\n💰 Jumlah Withdraw: <strong>Rp$nominal</strong>\n⏳ Status: <strong>Pending</strong>";
    }elseif($typeText == "depo"){
        $nominal = number_format($nominal);
        $text = "⚠️ Perhatian!\n👤 Pengguna: <strong>$member</strong>\n💰 Jumlah Deposit: <strong>Rp$nominal</strong>\n⏳ Status: <strong>Pending</strong>";
    }elseif($typeText == "klaimBonus"){
        $text = "⚠️ Perhatian!\n👤 Pengguna: <strong>$member</strong>\n💰 Code Voucher: <strong>$CodePesanan</strong>\n⏳ Status: <strong>Pending</strong>";
    }

    // Parameters
    $data = [
        'chat_id' => -4506742350,  // Replace with the actual chat ID
        'text' => $text,         // Replace with the message you want to send
        'parse_mode' => 'HTML'
    ];

    // Use cURL to send the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // Check for errors
    if ($response === false) {
        return false;
    } else {
        $responseData = json_decode($response, true);
        if ($responseData['ok']) {
            return true;
        } else {
            return false;
        }
    }
}

?>