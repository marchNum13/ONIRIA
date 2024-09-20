<?php  
session_start();
error_reporting(0);
include "config/transaksiConfig.php"
?>
<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Transaction</title>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
    <?php $timestamp = time(); ?>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $timestamp ?>">
    <link rel="manifest" href="__manifest.json"><style>
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

    <!-- App Header -->
    <div class="appHeader">
        <div class="left">
            <a href="home" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">
            Transaction
        </div>
    </div>
    <!-- * App Header -->


    <!-- App Capsule -->
    <div id="appCapsule">

        <!-- Transactions -->
        <div class="section mt-2 mb-3">
            <div class="section-title">This Month</div>
            <div class="transactions">
                <?php  
                    $userAds = $_SESSION['user_ads'];
                    $dataTr = $depositTableClass->selectAllTr($firstDayOfMonth, $lastDayOfMonth, $userAds);
                    foreach($dataTr['data'] as $row){
                ?>
                <!-- item -->
                <a href="" class="item">
                    <div class="detail">
                        <div class="image-block imaged">
                        <?php  
                        $plus = array('profit basic', 'profit premium', 'bonus provider', 'bonus matching', 'deposit');
                        $minus = array('paket premium', 'paket membership', 'withdraw');
                        $class = "";
                        $icon = "";
                        if(in_array($row['keterangan'], $plus)){
                            $class = "success";
                            $icon = "+"
                        ?>
                            <ion-icon name="arrow-up-outline"></ion-icon>
                        <?php  
                        }else{
                            $class = "danger";
                            $icon = "-"
                        ?>
                            <ion-icon name="arrow-down-outline"></ion-icon>
                        <?php  
                        }
                        ?>
                        </div>
                        <div>
                            <strong><?= ucwords($row['keterangan']) ?></strong>
                            <p><?= $row['date'] ?></p>
                        </div>
                    </div>
                    <div class="right">
                        <div class="price text-<?= $class ?>"><?= $icon ?><?= number_format($row['nominal'],2) ?> <?= $row['keterangan'] == "profit basic" ? "Nexx" : "USDT" ?></div>
                    </div>
                </a>
                <!-- * item -->
                <?php  
                    }
                ?>
            </div>
        </div>
        <!-- * Transactions -->


    </div>
    <!-- * App Capsule -->


    <!-- App Bottom Menu -->
    <div class="appBottomMenu">
        <a href="home" class="item">
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
        <a href="transaction" class="item active">
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


    <!-- ========= JS Files =========  -->
    <!-- Bootstrap -->
    <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="assets/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="assets/js/base.js"></script>


</body>

</html>