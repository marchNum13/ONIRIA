<?php  
session_start();
error_reporting(0);
include "config/homeConfig.php"
?>

<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Home</title>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
    <?php $timestamp = time(); ?>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $timestamp ?>">
    <link rel="manifest" href="__manifest.json">
    <!-- <script src="https://www.youtube.com/iframe_api"></script> -->
    <style>
        body {
            background-image: url('assets/adVenture.png') !important;
            background-size: cover !important; /* Menyesuaikan gambar dengan ukuran layar */
            background-position: center !important; /* Menempatkan gambar di tengah */
            height: 100vh;
        }
    </style>
</head>

<body class="dark-mode">

    <!-- loader -->
    <div id="loader">
        <img src="assets/img/loading-icon.png" alt="icon" class="loading-icon">
    </div>
    <!-- * loader -->

    <?php include "partial/headerhome.php" ?>

    <?php  
        if($_SESSION['user_role'] != "Admin"){
    ?>
        <!-- App Capsule -->
        <div id="appCapsule">

            <!-- Wallet Card -->
            <div class="section">
                <div class="carousel-full splide">
                    <div class="splide__track">
                        <ul class="splide__list">

                            <li class="splide__slide">
                                <div class="wallet-card" style="background-image: linear-gradient(to right top, #00c2a0, #02b492, #02a685, #019878, #008b6c);">
                                    <!-- Balance -->
                                    <div class="balance">
                                        <div class="left">
                                            <span class="title text-white">Total Balance</span>
                                            <h1 class="total text-white"><?= number_format($totalBalance,2) ?> USDT</h1>
                                        </div>
                                    </div>
                                    <!-- * Balance -->
                                    <!-- Wallet Footer -->
                                    <div class="wallet-footer">
                                        <div class="item">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#depositActionSheet">
                                                <div class="icon-wrapper">
                                                    <ion-icon name="arrow-up-outline"></ion-icon>
                                                </div>
                                                <strong class="text-white">Deposit</strong>
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#withdrawActionSheet">
                                                <div class="icon-wrapper bg-danger">
                                                    <ion-icon name="arrow-down-outline"></ion-icon>
                                                </div>
                                                <strong class="text-white">Withdraw</strong>
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="package">
                                                <div class="icon-wrapper bg-success">
                                                    <ion-icon name="gift-outline"></ion-icon>
                                                </div>
                                                <strong class="text-white">Package</strong>
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="transaksi">
                                                <div class="icon-wrapper bg-warning">
                                                    <ion-icon name="swap-vertical"></ion-icon>
                                                </div>
                                                <strong class="text-white">Transaction</strong>
                                            </a>
                                        </div>
                
                                    </div>
                                    <!-- * Wallet Footer -->
                                </div>
                            </li>

                            <li class="splide__slide">
                                <div class="wallet-card" style="background-image: linear-gradient(to right top, #e72b4a, #e8006a, #db0091, #b700bc, #6a2ce6);">
                                    <!-- Balance -->
                                    <div class="balance">
                                        <div class="left">
                                            <span class="title text-white">Total Balance</span>
                                            <h1 class="total text-white"><?= number_format($getWallet['user_saldo_token']) ?> Nexx</h1>
                                        </div>
                                        <div class="right">
                                            <!-- <a href="#" class="button" data-bs-toggle="modal" data-bs-target="#">
                                                <ion-icon name="arrow-down-outline"></ion-icon>
                                            </a> -->
                                        </div>
                                    </div>
                                    <!-- * Balance -->
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>


            </div>
            <!-- Wallet Card -->

            <!-- Deposit Action Sheet -->
            <div class="modal fade action-sheet" id="depositActionSheet" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Balance</h5>
                        </div>
                        <div class="modal-body">
                            <div class="action-sheet-content">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="form-group basic">
                                        <div class="input-wrapper">
                                            <label class="label" for="bankAdminDepo">Admin Wallet Address (BEP-20)</label>
                                            <h5><?= $bankAdminDepo ?></h5>
                                            <input type="hidden" class="form-control" placeholder="Enter an amount"
                                                value="<?= $bankAdminDepo ?>" name="bankAdminDepo" id="bankAdminDepo" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group basic">
                                        <div class="input-wrapper">
                                            <label class="label" for="bankUserDepo">Your Wallet Address (BEP-20)</label>
                                            <h5><?= $bankUserDepo ?></h5>
                                            <input type="hidden" class="form-control" placeholder="Enter an amount"
                                                value="<?= $bankUserDepo ?>" name="bankUserDepo" id="bankUserDepo" readonly>
                                        </div>
                                    </div>                                

                                    <div class="form-group basic">
                                        <label class="label" for="bukti_tf">Transaction Number</label>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="bukti_tf" id="bukti_tf" placeholder="Enter transaction number">
                                        </div>
                                    </div>

                                    <div class="form-group basic">
                                        <label class="label" for="jumlahDepo">Enter Amount</label>
                                        <div class="input-group mb-2">
                                            <input type="number" class="form-control" placeholder="Enter an amount"
                                                value="0" name="jumlahDepo" id="jumlahDepo">
                                            <span class="input-group-text" id="basic-addona1">USDT</span>
                                        </div>
                                    </div>

                                    <div class="text-left" style="font-size: smaller;">
                                        Before confirming your transaction, please ensure that your wallet address matches the recipient address of your transaction. Incorrect addresses may result in the loss of funds.
                                    </div>

                                    <script>
                                        function loadingForm() {
                                            // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                            document.getElementById("loader").style.display  = "";
                                        }
                                    </script>
                                    <div class="form-group basic">
                                        <button type="submit" name="deposit" onclick="loadingForm()" class="btn btn-success btn-block btn-lg"
                                            data-bs-dismiss="modal">Deposit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * Deposit Action Sheet -->

            <!-- Withdraw Action Sheet -->
            <div class="modal fade action-sheet" id="withdrawActionSheet" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Withdraw Balance</h5>
                        </div>
                        <div class="modal-body">
                            <div class="action-sheet-content">
                                <form method="post" action="">

                                    <div class="form-group basic">
                                        <div class="input-wrapper">
                                            <label class="label" for="bankUserWd">Your Wallet Address (BEP-20)</label>
                                            <input type="text" class="form-control" placeholder="Enter an amount"
                                                value="<?= $bankUserWd ?>" name="bankUserWd" id="bankUserWd" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group basic">
                                        <label class="label" for="mount">Enter Amount</label>
                                        <div class="input-group mb-2">
                                            <input type="number" class="form-control" placeholder="Enter an amount"
                                                value="0" name="mount" id="mount" onkeyup="calculate(this.value)">
                                            <span class="input-group-text" id="basic-addonb1">USDT</span>
                                        </div>
                                        <div class="input-info">Balance: <?= number_format($totalBalance,2) ?> USDT</div>
                                        <div class="input-info">Min WD: <?= number_format($minWD) ?> USDT</div>
                                    </div>

                                    <div class="form-group basic">
                                        <label class="label" for="admin">Fee Admin</label>
                                        <div class="input-group mb-2">
                                            <input type="number" readonly step="0.0001" class="form-control" placeholder="Enter an amount"
                                                value="<?= $feeWD ?>" name="admin" id="admin">
                                            <span class="input-group-text" id="basic-addonb1">USDT</span>
                                        </div>
                                    </div>

                                    <div class="form-group basic">
                                        <label class="label" for="payout">Total Payout</label>
                                        <div class="input-group mb-2">
                                            <input type="text" readonly class="form-control" placeholder="Enter an amount"
                                                value="0" name="payout" id="payout">
                                            <span class="input-group-text" id="basic-addonb1">USDT</span>
                                        </div>
                                    </div>

                                    <script>
                                        function calculate(amount){
                                            if(amount == "" || amount <= 0){
                                                document.getElementById("payout").value = "0";
                                            }else{
                                                const biayaAdmin = parseFloat("<?= $feeWD ?>");
                                                const payout = amount - biayaAdmin;

                                                // Format Rupiah
                                                const formatter = new Intl.NumberFormat('id-ID', {
                                                    style: 'currency',
                                                    currency: 'USD',
                                                    minimumFractionDigits: 0 // Opsional: tidak menampilkan desimal
                                                });

                                                document.getElementById("payout").value = formatter.format(payout).replace("US$", "");
                                            }
                                        }
                                        function loadingForm() {
                                            // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                            document.getElementById("loader").style.display  = "";
                                        }
                                    </script>

                                    <div class="form-group basic">
                                        <button type="submit" name="withdraw" onclick="loadingForm()" class="btn btn-success btn-block btn-lg"
                                            data-bs-dismiss="modal">Withdraw</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * Withdraw Action Sheet -->



            <!-- Stats -->
            <div class="section">
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #2bcf3a, #19ca4b, #05c459, #00be65, #00b86e);">
                            <div class="title text-white">Reward Basic</div>
                            <div class="value text-white" style="font-size: large;"><?= getSumProfitBasic() ?> Nexx</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #009ccc, #0097d2, #0092d8, #008cdd, #0085e0);">
                            <div class="title text-white">Reward Premium</div>
                            <div class="value text-white" style="font-size: large;"><?= getSumProfit() ?> USDT</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #581ae9, #0050e4, #0060c8, #00659f, #2f6578);">
                            <div class="title text-white">Reward Provider</div>
                            <div class="value text-white" style="font-size: large;"><?= getSumBonus() ?> USDT</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #f28d0a, #f78028, #f9743a, #f8694a, #f46059);">
                            <div class="title text-white">Reward Matching</div>
                            <div class="value text-white" style="font-size: large;"><?= getSumBonusMatching() ?> USDT</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <?php $adsTotal = totalAds() ?>
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #fd731a, #ff6732, #fd5c44, #fa5354, #f34d62);">
                            <div class="title text-white">Daily Video</div>
                            <div class="value text-white"><?= $adsTotal['total'] ?> Video</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #fd1a1a, #fc0032, #f70045, #f10056, #e80065);">
                            <div class="title text-white">Unwatched Video</div>
                            <div class="value text-white"><?= $adsTotal['sisa'] ?> Video</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * Stats -->
            <!-- <div class="section mt-3">
                <div class="card card-with-icon">
                    <div class="card-body pt-3 pb-3 text-center">
                        <div class="card-icon bg-danger mb-2">
                            <ion-icon name="link"></ion-icon>
                        </div>
                        <h3 class="card-titlde mb-1">M-PLAN CuanTube</h3>
                        <div class="row">
                            <div class="col">
                                <a href="mplen" class="btn btn-danger">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <?php  
            if(!isset($_GET['iklan'])){
            ?>
            <!-- Transactions -->
            <div class="section mt-4">
                <?php  
                    // $expiredMembership = 30*24*60*60*1000;
                    // if($dateNowMilis <= $expiredMembership){
                    $paket = getPaketUser();
                    $userAds = $_SESSION['user_ads'];
                    if($paket['row'] > 0){
                        if(!isLimitProfit($userAds)){
                            foreach($paket['data'] as $row){
                                // tgl pengisian
                                $PaketName = $row['paket_name'];
                                $date = $row['paket_ads_stop_date'] - (1*24*60*60*1000);
                                $paketId = $row['paket_id'];
                                $jumlahAds = $row['paket_jumlah_tugas'];
                ?>
                <div class="section-heading">
                    <h2 class="title">Task <?= $PaketName ?></h2>
                    <!-- <a href="app-transactions.html" class="link">View All</a> -->
                </div>
                <div class="transactions mb-3">
                    <?php  
                        $data = $adsUserTableClass->selectAds("ads_id, ads_name, ads_reward", "ads_status = 'Aktif' AND ads_paket_id = '$paketId' AND ads_user_id = '$userAds' AND ads_date = '$date' ORDER BY ads_date DESC LIMIT $jumlahAds");
                        if($data['row'] > 0){
                            foreach($data['data'] as $adsDataUser){
                                
                    ?>
                    <!-- item -->
                    <a href="?iklan=<?= $adsDataUser['ads_id'] ?>&type=premium" class="item">
                        <div class="detail">
                            <img src="assets/img/sample/brand/yt.png" alt="img" class="image-block imaged w48">
                            <div>
                                <strong><?= $adsDataUser['ads_name'] ?></strong>
                                <p>Click</p>
                            </div>
                        </div>
                        <div class="right">
                            <div class="price text-success"><?= number_format($adsDataUser['ads_reward'],2) ?> USDT</div>
                        </div>
                    </a>
                    <!-- * item -->
                    
                    <?php  
                            }
                        
                        }else{
                            echo '<div class="text-center">Video not found</div>';
                        }
                    ?>
                </div>
                <hr>
                
                <?php  
                            }
                        }else{
                            echo '
                                <div class="card card-with-icon mb-3">
                                    <div class="card-body pt-3 pb-3 text-center">
                                        <div class="card-icon bg-danger mb-2">
                                            <ion-icon name="trash-bin-outline"></ion-icon>
                                        </div>
                                        <h3 class="card-titlde mb-1">Your Premium Package has Reached Its Limit</h3>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="paket" class="btn btn-danger">
                                            Buy Now
                                        </a>
                                    </div>
                                </div>
                            ';
                        }
                    }else{
                        $paketBaic = getPaketBasicUser();
                        $PaketName = $paketBaic['data'][0]['paket_name'];
                        $date = $paketBaic['data'][0]['paket_ads_stop_date'] - (1*24*60*60*1000);
                        $paketId = $paketBaic['data'][0]['paket_id'];
                        $jumlahAds = $paketBaic['data'][0]['paket_jumlah_tugas'];
                ?>
                        <div class="section-heading">
                            <h2 class="title">Task <?= $PaketName ?></h2>
                            <!-- <a href="app-transactions.html" class="link">View All</a> -->
                        </div>
                        <div class="transactions mb-3">
                            <?php  
                                $data = $adsBasicUserTableClass->selectAds("ads_id, ads_name, ads_reward", "ads_status = 'Aktif' AND ads_paket_id = '$paketId' AND ads_user_id = '$userAds' AND ads_date = '$date' ORDER BY ads_date DESC LIMIT $jumlahAds");
                                if($data['row'] > 0){
                                    foreach($data['data'] as $adsDataUser){
                                        
                            ?>
                            <!-- item -->
                            <a href="?iklan=<?= $adsDataUser['ads_id'] ?>&type=basic" class="item">
                                <div class="detail">
                                    <img src="assets/img/sample/brand/yt.png" alt="img" class="image-block imaged w48">
                                    <div>
                                        <strong><?= $adsDataUser['ads_name'] ?></strong>
                                        <p>Click</p>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="price text-success"><?= number_format($adsDataUser['ads_reward']) ?> Nexx</div>
                                </div>
                            </a>
                            <!-- * item -->
                            
                            <?php  
                                    }
                                }else{
                                    echo '<div class="text-center">Video not found</div>';
                                }
                            ?>
                        </div>
                <hr>
                <?php } ?>
            </div>
            <!-- * Transactions -->
            <?php 
            }else{ 
                $array = array("uXlWYZ022zU", "LRJP140fv3E", "FzaS0V_FCrI", "S87tldWtXMc", "Y1P-UfaaVfo", "_oI_B0OBgVw", "XhP3Xh4LMA8", "QdBZY2fkU-0");

                $indeks_acak = array_rand($array);

                $get_array = $array[$indeks_acak];
            ?>
            <div class="section mt-2">
                <form method="post" action="" class="card">
                    <div class="card-header">
                        Klaim Profit
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="adsID" value="<?= $_GET['iklan'] ?>">
                        <input type="hidden" name="adsType" value="<?= $_GET['type'] ?>">
        
                        <!-- YouTube Video Embed -->
                        <div class="video-wrapper">
                            <iframe id="ytplayer" type="text/html" width="100%" height="360" src="https://www.youtube.com/embed/<?= $get_array ?>?enablejsapi=1&controls=0&rel=0&modestbranding=1&iv_load_policy=3&disablekb=1" frameborder="0"></iframe>
                        </div>
                        <script>
                            var player;
        
                            function onYouTubeIframeAPIReady() {
                                player = new YT.Player('ytplayer', {
                                    events: {
                                        'onStateChange': onPlayerStateChange
                                    }
                                });
                            }
        
                            function onPlayerStateChange(event) {
                                if (event.data == YT.PlayerState.ENDED) {
                                    // Aktifkan tombol klaim setelah video selesai diputar
                                    document.getElementById("klaim").disabled = false;
                                }
                            }
        
                            function loadingIklan() {
                                if (player.getPlayerState() == YT.PlayerState.ENDED) {
                                    // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                    document.getElementById("loader").style.display = "";
                                } else {
                                    // Cegah tombol klaim di-klik jika video belum selesai
                                    document.getElementById("klaim").disabled = true;
                                }
                            }
                        </script>
        
                        <div class="form-group basic">
                            <button type="submit" name="klaim" onclick="loadingIklan()" id="klaim" class="btn btn-success btn-block btn-lg" data-bs-dismiss="modal" disabled>Klaim</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- YouTube IFrame API Script -->
            <script src="https://www.youtube.com/iframe_api"></script>
            <?php } ?>



            <!-- ios style 16 -->
            <div id="alertdanger" class="notification-box">
                <div class="notification-dialog ios-style bg-danger">
                    <div class="notification-header">
                        <div class="in">
                            <!-- <img src="assets/img/sample/avatar/avatar3.jpg" alt="image" class="imaged w24 rounded"> -->
                            <strong>Error</strong>
                        </div>
                        <!-- <div class="right">
                            <span>5 mins ago</span>
                            <a href="#" class="close-button">
                                <ion-icon name="close-circle"></ion-icon>
                            </a>
                        </div> -->
                    </div>
                    <div class="notification-content">
                        <div class="in">
                            <h3 class="subtitle">Messange</h3>
                            <div class="text">
                                <?= $alert_error ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="alertSuccess" class="notification-box">
                <div class="notification-dialog ios-style bg-success">
                    <div class="notification-header">
                        <div class="in">
                            <!-- <img src="assets/img/sample/avatar/avatar3.jpg" alt="image" class="imaged w24 rounded"> -->
                            <strong>Success</strong>
                        </div>
                        <!-- <div class="right">
                            <span>5 mins ago</span>
                            <a href="#" class="close-button">
                                <ion-icon name="close-circle"></ion-icon>
                            </a>
                        </div> -->
                    </div>
                    <div class="notification-content">
                        <div class="in">
                            <h3 class="subtitle">Messange</h3>
                            <div class="text">
                                <?= $_SESSION['alert_success'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * ios style 16 -->

        </div>
        <!-- * App Capsule -->
        <!-- App Bottom Menu -->
        <div class="appBottomMenu">
            <a href="home" class="item active">
                <div class="col">
                    <ion-icon name="home-outline"></ion-icon>
                    <strong>Home</strong>
                </div>
            </a>
            <a href="package" class="item">
                <div class="col">
                    <ion-icon name="file-tray-full-outline"></ion-icon>
                    <strong>Package</strong>
                </div>
            </a>
            <a href="transaction" class="item">
                <div class="col">
                    <ion-icon name="swap-horizontal"></ion-icon>
                    <strong>Transaction</strong>
                </div>
            </a>
            <a href="referral" class="item">
                <div class="col">
                    <ion-icon name="share-social-outline"></ion-icon>
                    <strong>Refferal</strong>
                </div>
            </a>
            <a href="app-settings" class="item">
                <div class="col">
                    <ion-icon name="person-outline"></ion-icon>
                    <strong>Profile</strong>
                </div>
            </a>
        </div>
        <!-- * App Bottom Menu -->
    <?php }else{ ?>
        <!-- App Capsule -->
        <div id="appCapsule">

            <!-- Wallet Card -->
            <div class="section pt-1">
                <div class="wallet-card" style="background-image: linear-gradient(to right top, #00c2a0, #02b492, #02a685, #019878, #008b6c);">
                    <!-- Balance -->
                    <div class="balance">
                        <div class="left">
                            <span class="title text-white">Omset Bulan Ini</span>
                            <h1 class="total text-white"><?= number_format(omsetMounth()) ?> USDT</h1>
                        </div>
                    </div>
                    <!-- * Balance -->
                </div>
            </div>
            <!-- Wallet Card -->

            <!-- Stats -->
            <div class="section">
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #2bcf3a, #19ca4b, #05c459, #00be65, #00b86e);">
                            <div class="title text-white">Deposit</div>
                            <div class="value text-white" style="font-size: large;"><?= number_format(depoMounth()['data'][0]['total']) ?> USDT</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box" style="background-image: linear-gradient(to right top, #009ccc, #0097d2, #0092d8, #008cdd, #0085e0);">
                            <div class="title text-white">Withdraw</div>
                            <div class="value text-white" style="font-size: large;"><?= number_format(wdMounth()['data'][0]['total']) ?> USDT</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * Stats -->

            <div class="section mt-2">
                <div class="section-title">Pembelian Paket Premium</div>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">Date</th>
                                    <th>Member</th>
                                    <th>Paket</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    $tablePaket = tablePaket($page);
                                    if($tablePaket['row'] == 0){
                                        echo '<tr><td colspan="4" align="center">Data tidak ditemukan.</td></tr>';
                                    }else{
                                        foreach($tablePaket['data'] as $row){
                                ?>
                                <tr>
                                    <th><?= $row['date'] ?></th>
                                    <td><?= memberName($row['paket_user_id']) ?></td>
                                    <td><?= $row['paket_name'] ?></td>
                                    <td class="text-end text-primary"><?= number_format($row['paket_nominal']) ?> USDT</td>
                                </tr>
                                <?php 
                                        }
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <?php  
                        $hrefAdd = "";
                        $limit = 10;
                        $total = tableCount();
                        $total_pages = ceil($total / $limit);
                        $prev = max(1, $page - 1);
                        $next = min($total_pages, $page + 1);
                        if($total_pages > 0 && $tablePaket['row'] > 0){
                    ?>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $prev.$hrefAdd ?>" aria-label="Previous">
                                        <span aria-hidden="true">«</span>
                                    </a>
                                </li>
                                <?php  
                                            if($total_pages <= 6){
                                                for($i = 1; $i <= $total_pages; $i++){
                                        ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i.$hrefAdd ?>"><?= $i ?></a>
                                </li>
                                <?php  
                                                }
                                            }else{
                                                if($page < 5){
                                                    for($i = 1; $i <= 5; $i++){
                                        ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i.$hrefAdd ?>"><?= $i ?></a>
                                </li>
                                <?php
                                                    }
                                        ?>
                                <li class="page-item disabled">
                                    <a class="page-link">...</a>
                                </li>
                                <li class="page-item <?= $page == $total_pages ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?page=<?= $total_pages.$hrefAdd ?>"><?= $total_pages; ?></a>
                                </li>
                                <?php
                                                }elseif($page == $total_pages || $total_pages-$page < 4){
                                        ?>
                                <li class="page-item <?= $page == 1 ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=1<?= $hrefAdd ?>">1</a>
                                </li>
                                <li class="page-item disabled">
                                    <a class="page-link">...</a>
                                </li>
                                <?php  
                                                    for($i = $total_pages-4; $i <= $total_pages; $i++){
                                        ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i.$hrefAdd ?>"><?= $i ?></a>
                                </li>
                                <?php
                                                    }
                                                }else{
                                        ?>
                                <li class="page-item <?= $page == 1 ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=1<?= $hrefAdd ?>">1</a>
                                </li>
                                <li class="page-item disabled">
                                    <a class="page-link">...</a>
                                </li>
                                <?php  
                                                    for($i = $page-1; $i <= $page+1; $i++){
                                        ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i.$hrefAdd ?>"><?= $i; ?></a>
                                </li>
                                <?php  
                                                    }
                                        ?>
                                <li class="page-item disabled">
                                    <a class="page-link">...</a>
                                </li>
                                <li class="page-item <?= $page == $total_pages ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?page=<?= $total_pages.$hrefAdd ?>"><?= $total_pages; ?></a>
                                </li>
                                <?php
                                                }
                                            }
                                        ?>
                                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $next.$hrefAdd ?>" aria-label="Next">
                                        <span aria-hidden="true">»</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php  
                        }
                    ?>
                </div>
            </div>

            <!-- ios style 16 -->
            <div id="alertdanger" class="notification-box">
                <div class="notification-dialog ios-style bg-danger">
                    <div class="notification-header">
                        <div class="in">
                            <!-- <img src="assets/img/sample/avatar/avatar3.jpg" alt="image" class="imaged w24 rounded"> -->
                            <strong>Error</strong>
                        </div>
                        <!-- <div class="right">
                            <span>5 mins ago</span>
                            <a href="#" class="close-button">
                                <ion-icon name="close-circle"></ion-icon>
                            </a>
                        </div> -->
                    </div>
                    <div class="notification-content">
                        <div class="in">
                            <h3 class="subtitle">Messange</h3>
                            <div class="text">
                                <?= $alert_error ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="alertSuccess" class="notification-box">
                <div class="notification-dialog ios-style bg-success">
                    <div class="notification-header">
                        <div class="in">
                            <!-- <img src="assets/img/sample/avatar/avatar3.jpg" alt="image" class="imaged w24 rounded"> -->
                            <strong>Success</strong>
                        </div>
                        <!-- <div class="right">
                            <span>5 mins ago</span>
                            <a href="#" class="close-button">
                                <ion-icon name="close-circle"></ion-icon>
                            </a>
                        </div> -->
                    </div>
                    <div class="notification-content">
                        <div class="in">
                            <h3 class="subtitle">Messange</h3>
                            <div class="text">
                                <?= $_SESSION['alert_success'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * ios style 16 -->

        </div>
        <!-- * App Capsule -->
        <!-- App Bottom Menu -->
        <div class="appBottomMenu">
            <a href="home" class="item active">
                <div class="col">
                    <ion-icon name="home-outline"></ion-icon>
                    <strong>Home</strong>
                </div>
            </a>
            <a href="withdraw" class="item">
                <div class="col">
                    <ion-icon name="arrow-down-circle-outline"></ion-icon>
                    <strong>Withdraw</strong>
                </div>
            </a>
            <!-- <a href="klaim" class="item">
                <div class="col">
                    <ion-icon name="file-tray-full-outline"></ion-icon>
                    <strong>Klaim</strong>
                </div>
            </a> -->
            <a href="deposit" class="item">
                <div class="col">
                    <ion-icon name="arrow-up-circle-outline"></ion-icon>
                    <strong>Deposit</strong>
                </div>
            </a>
            <a href="app-settings" class="item">
                <div class="col">
                    <ion-icon name="person-outline"></ion-icon>
                    <strong>Profil</strong>
                </div>
            </a>
        </div>
        <!-- * App Bottom Menu -->
    <?php } ?>


    <!-- ========= JS Files =========  -->
    <!-- Bootstrap -->
    <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="assets/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="assets/js/base.js"></script>
    <?php if($alert_error != ""){ ?>
    <script>
        notification('alertdanger', 3000)
    </script>
    <?php } ?>
    <?php if($_SESSION['alert_success'] != ""){ ?>
    <script>
        notification('alertSuccess', 3000)
    </script>
    <?php } ?>

</body>

</html>
<?php $_SESSION['alert_success'] = "" ?>