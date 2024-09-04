<?php  
session_start();
error_reporting(0);
include "config/paketConfig.php"
?>
<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>CuanTube - Paket</title>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
    <?php $timestamp = time(); ?>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $timestamp ?>">
    <link rel="manifest" href="__manifest.json">
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
            <a href="home" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">
            Paket
        </div>
    </div>
    <!-- * App Header -->


    <!-- App Capsule -->
    <div id="appCapsule">
        <?php  
            $checkPaketBerbayar = checkPaketBerbayar();
            if($checkPaketBerbayar){
        ?>
            <?php 
                foreach($dataPaket as $row){
                    if(!$checkTrial && $row['settings_nama_paket'] == "Magang"){
                        continue;
                    }
                    $lvlReff = 3; 
                    $upLevel = array('Paket 4', 'Paket 5', 'Paket 6', 'Paket 7');
                    if(in_array($row['settings_nama_paket'], $upLevel)){
                        $lvlReff = 4;
                    }
            ?>
            <div class="section mt-2">
                <div class="card mb-2 bg-success">
                    <div class="card-header text-white"><?= $row['settings_nama_paket'] ?> <span class="badge badge-warning"><?= $row['settings_jumlah_tugas'] ?> Ads</span></div>
                    <div class="card-body">
                        <h3 class="card-title text-white">Rp<?= number_format($row['settings_harga_paket']) ?></h3>
                        <p class="card-text text-white">
                            Reward: Rp <?= number_format($row['settings_reward_tugas']) ?> / Ads <br>
                            Referral <?= $lvlReff ?> Lvl
                        </p>
                    </div>
                    <div class="card-footer text-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#buy<?= $row['id'] ?>" class="btn btn-danger">
                            <?= $row['settings_nama_paket'] == "Magang" ? "Trial" : "Buy" ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal fade action-sheet" id="buy<?= $row['id'] ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $row['settings_nama_paket'] ?></h5>
                        </div>
                        <div class="modal-body">
                            <div class="action-sheet-content">
                                <form method="post" action="">
                                    <input type="hidden" name="namePaket" value="<?= $row['settings_nama_paket'] ?>">                                
                                    <h3>Rp<?= number_format($row['settings_harga_paket']) ?></h3>
                                    Reward: Rp<?= number_format($row['settings_reward_tugas']) ?> / Ads <br>
                                    Referral <?= $lvlReff ?> Lvl
                                    <?php if($row['settings_nama_paket'] == "Magang"){ ?>
                                        <div class="form-group basic">
                                            <label class="label" for="code">Enter Code</label>
                                            <div class="mb-2">
                                                <input type="text" class="form-control" placeholder="Enter your code"
                                                    name="code" id="code">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <script>
                                        function loadingForm() {
                                            // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                            document.getElementById("loader").style.display  = "";
                                        }
                                    </script>
                                    <div class="form-group basic">
                                        <button type="submit" name="buyPaket" onclick="loadingForm()" class="btn btn-success btn-block btn-lg"
                                            data-bs-dismiss="modal"><?= $row['settings_nama_paket'] == "Magang" ? "Trial" : "Buy" ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        <?php  
            }else{
        ?>
            <!-- * Stats -->
            <div class="section mt-3">
                <div class="card card-with-icon">
                    <div class="card-body pt-3 pb-3 text-center">
                        <div class="card-icon bg-danger mb-2">
                            <ion-icon name="trash-bin-outline"></ion-icon>
                        </div>
                        <h3 class="card-titlde mb-1">Anda Sudah Membeli Paket</h3>
                    </div>
                </div>
            </div>
        <?php } ?>

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
        <a href="paket" class="item active">
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