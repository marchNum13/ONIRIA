<?php  
session_start();
error_reporting(0);
include "config/forgotConfig.php"
?>
<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Forgot Password</title>
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
            <a href="index" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">
            <img src="assets/img/logo.png" alt="logo" class="logo">
        </div>
        <div class="right">
        </div>
    </div>
    <!-- * App Header -->

    <!-- App Capsule -->
    <div id="appCapsule">

        <?php if($alert_success == ""){ ?>
        <div class="section mt-2 text-center">
            <h1>Forgot Password</h1>
            <h4>Type your email to reset your password</h4>
        </div>
        <div class="section mb-5 p-2">
            <form action="" method="post">
                <div class="card">
                    <div class="card-body pb-1">
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?= $email ?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="g-recaptcha" data-sitekey="6LfEie0pAAAAAF2QpQ_VH1uLyohZfiefSShLQvL2"></div>
                </div>
                
                <script>
                    function loadingForm() {
                        // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                        document.getElementById("loader").style.display  = "";
                    }
                </script>
                <div class="form-button-group transparent">
                    <button type="submit" class="btn btn-success btn-block btn-lg" name="reset" onclick="loadingForm()">Reset Password</button>
                </div>

            </form>
        </div>
        <?php }else{ ?>
        <div class="section mt-2 text-center">
            <h1>Check Your Email!</h1>
            <h4>Buka email anda untuk mengubah password.</h4>
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
                            <?= $alert_success ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * ios style 16 -->

    </div>
    <!-- * App Capsule -->



    <!-- ========= JS Files =========  -->
    <!-- Bootstrap -->
    <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="assets/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="assets/js/base.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <!-- JavaScript untuk menampilkan password -->
    <script>
        document.getElementById('showpass').addEventListener('change', function() {
            var passwordInput = document.getElementById('password1');
            if (this.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });
    </script>
    <?php if($alert_error != ""){ ?>
    <script>
        notification('alertdanger', 3000)
    </script>
    <?php } ?>
    <?php if($alert_success != ""){ ?>
    <script>
        notification('alertSuccess', 3000)
    </script>
    <?php } ?>

</body>

</html>