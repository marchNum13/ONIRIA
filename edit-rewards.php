<?php  
session_start();
error_reporting(0);
include "config/editRewardConfig.php"
?>

<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Edit Rewards</title>
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

    <!-- App Header -->
    <div class="appHeader">
        <div class="left">
            <a href="app-settings" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">
            Edit Rewards
        </div>
    </div>
    <!-- * App Header -->

    <!-- App Capsule -->
    <div id="appCapsule">
        <form action="" method="post">
            <div class="section mt-2">
                <div class="section-title">Free / Membership</div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video1">Video 1 (Nexx)</label>
                                <input type="number" step="0.0001" class="form-control" id="video1" name="videoBasic1" placeholder="Enter Reward" autocomplete="off" value="<?= $basicVideoSatu ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video2">Video 2 (Nexx)</label>
                                <input type="number" step="0.0001" class="form-control" id="video2" name="videoBasic2" placeholder="Enter Reward" autocomplete="off" value="<?= $basicVideoDua ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section mt-2">
                <div class="section-title">Premium $100</div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video1">Video 1 (%)</label>
                                <input type="number" step="0.0001" class="form-control" id="video1" name="videoPremiumSatu1" placeholder="Enter Reward" autocomplete="off" value="<?= $premiumSatuVideoSatu ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video2">Video 2 (%)</label>
                                <input type="number" step="0.0001" class="form-control" id="video2" name="videoPremiumSatu2" placeholder="Enter Reward" autocomplete="off" value="<?= $premiumSatuVideoDua ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section mt-2">
                <div class="section-title">Premium $500</div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video1">Video 1 (%)</label>
                                <input type="number" step="0.0001" class="form-control" id="video1" name="videoPremiumDua1" placeholder="Enter Reward" autocomplete="off" value="<?= $premiumDuaVideoSatu ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video2">Video 2 (%)</label>
                                <input type="number" step="0.0001" class="form-control" id="video2" name="videoPremiumDua2" placeholder="Enter Reward" autocomplete="off" value="<?= $premiumDuaVideoDua ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="video3">Video 3 (%)</label>
                                <input type="number" step="0.0001" class="form-control" id="video3" name="videoPremiumDua3" placeholder="Enter Reward" autocomplete="off" value="<?= $premiumDuaVideoTiga ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function loadingForm() {
                    // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                    document.getElementById("loader").style.display  = "";
                }
            </script>
            <div class="row listview image-listview text mb-4 mt-4 inset">
                <button type="submit" onclick="loadingForm()" name="submit" class="btn btn-success">
                    Submit
                </button>
            </div>
        </form>

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
        <a href="home" class="item">
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
        <a href="deposit" class="item">
            <div class="col">
                <ion-icon name="arrow-up-circle-outline"></ion-icon>
                <strong>Deposit</strong>
            </div>
        </a>
        <a href="app-settings" class="item active">
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