<?php  
session_start();
error_reporting(0);
include "config/refferalConfig.php"
?>
<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Referral</title>
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
    <script>
        function copyToClipboard() {
            var url = "https://oniria.click/app-register?reff=<?= $_SESSION['user_ads'] ?>";
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = url;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            // alert("URL telah disalin ke clipboard!");
            notification('alertCopy', 3000)
        }
    </script>
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
            Referral
        </div>
    </div>
    <!-- * App Header -->


    <!-- App Capsule -->
    <div id="appCapsule">

        <div class="section mt-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="card-titlde mb-1">Refer a Friend</h3>
                    <p>Referral Code: <span class="badge badge-danger"><?= $_SESSION['user_ads'] ?></span></p>
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-secondary" onclick="copyToClipboard()">
                                <ion-icon name="copy-outline"></ion-icon>
                                Invite now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <!-- LVL 1 -->
        <div class="section mb-3">
            <div class="section-title text-white">Level 1</div>
            <div class="transactions">
                <?php  
                    $userAds = $_SESSION['user_ads'];
                    $downline_satu = $userTableClass->selectDownlines($userAds);
                    foreach($downline_satu['data'] as $satuRow){
                ?>
                <!-- item -->
                <a href="#" class="item">
                    <div class="detail">
                        <div>
                            <strong><?= $satuRow['user_username'] ?></strong>
                            <p><?= $satuRow['user_email'] ?></p>
                        </div>
                    </div>
                </a>
                <!-- * item -->
                <?php  
                    }
                ?>
            </div>
        </div>
        <hr>
        <!-- * LVL 1 -->
        <!-- LVL 2 -->
        <div class="section mb-3">
            <div class="section-title text-white">Level 2</div>
            <div class="transactions">
                <?php  
                    $downline_satu = $userTableClass->selectDownlines($userAds);
                    foreach($downline_satu['data'] as $satuRow){
                        $downline_dua = $userTableClass->selectDownlines($satuRow['user_refferal']);
                        foreach($downline_dua['data'] as $duaRow){
                ?>
                <!-- item -->
                <a href="" class="item">
                    <div class="detail">
                        <div>
                            <strong><?= $duaRow['user_username'] ?></strong>
                            <p><?= $duaRow['user_email'] ?></p>
                        </div>
                    </div>
                </a>
                <!-- * item -->
                <?php  
                        }
                    }
                ?>
            </div>
        </div>
        <hr>
        <!-- * LVL 2 -->
        <!-- LVL 3 -->
        <div class="section mb-3">
            <div class="section-title text-white">Level 3</div>
            <div class="transactions">
                <?php  
                    $downline_satu = $userTableClass->selectDownlines($userAds);
                    foreach($downline_satu['data'] as $satuRow){
                        $downline_dua = $userTableClass->selectDownlines($satuRow['user_refferal']);
                        foreach($downline_dua['data'] as $duaRow){
                            $downline_tiga = $userTableClass->selectDownlines($duaRow['user_refferal']);
                            foreach($downline_tiga['data'] as $tigaRow){
                ?>
                <!-- item -->
                <a href="" class="item">
                    <div class="detail">
                        <div>
                            <strong><?= $tigaRow['user_username'] ?></strong>
                            <p><?= $tigaRow['user_email'] ?></p>
                        </div>
                    </div>
                </a>
                <!-- * item -->
                <?php  
                            }
                        }
                    }
                ?>
            </div>
        </div>
        <hr>
        <!-- * LVL 3 -->

        <?php  
            $highPaket = array('Paket 4', 'Paket 5', 'Paket 6', 'Paket 7');
            $paketUser = getPaketUser()['data'][0]['paket_name'];
            if(in_array($paketUser, $highPaket)){
        ?>
        <!-- LVL 4 -->
        <div class="section mb-3">
            <div class="section-title text-white">Level 4</div>
            <div class="transactions">
                <?php  
                    $downline_satu = $userTableClass->selectDownlines($userAds);
                    foreach($downline_satu['data'] as $satuRow){
                        $downline_dua = $userTableClass->selectDownlines($satuRow['user_refferal']);
                        foreach($downline_dua['data'] as $duaRow){
                            $downline_tiga = $userTableClass->selectDownlines($duaRow['user_refferal']);
                            foreach($downline_tiga['data'] as $tigaRow){
                                $downline_empat = $userTableClass->selectDownlines($tigaRow['user_refferal']);
                                foreach($downline_empat['data'] as $empatRow){
                ?>
                <!-- item -->
                <a href="" class="item">
                    <div class="detail">
                        <div>
                            <strong><?= $empatRow['user_username'] ?></strong>
                            <p><?= $empatRow['user_email'] ?></p>
                        </div>
                    </div>
                </a>
                <!-- * item -->
                <?php  
                                }
                            }
                        }
                    }
                ?>
            </div>
        </div>
        <!-- * LVL 4 -->
        <hr>
        <?php  
            }
        ?>

        <div id="alertCopy" class="notification-box">
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
                            The link has been copied to your clipboard.
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <a href="transaction" class="item">
            <div class="col">
                <ion-icon name="swap-horizontal"></ion-icon>
                <strong>Transaction</strong>
            </div>
        </a>
        <a href="referral" class="item active">
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