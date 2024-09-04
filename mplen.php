<?php  
session_start();
error_reporting(0);
// include "config/homeConfig.php"
?>

<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>CuanTube - Home</title>
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

        <!-- App Capsule -->
        <div id="appCapsule">
            <img src="assets/1.png" width="100%" style="height: 100vh;"></img>
            <img src="assets/2.png" width="100%" style="height: 100vh;"></img>
            <img src="assets/3.png" width="100%" style="height: 100vh;"></img>
            <img src="assets/4.png" width="100%" style="height: 100vh;"></img>

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
            <a href="paket" class="item">
                <div class="col">
                    <ion-icon name="file-tray-full-outline"></ion-icon>
                    <strong>Paket</strong>
                </div>
            </a>
            <a href="transaksi" class="item">
                <div class="col">
                    <ion-icon name="swap-horizontal"></ion-icon>
                    <strong>Transaksi</strong>
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
                    <strong>Profil</strong>
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