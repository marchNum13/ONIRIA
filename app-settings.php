<?php  
session_start();
error_reporting(0);
include "config/settingConfig.php"
?>
<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Setting</title>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
    <?php $timestamp = time(); ?>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $timestamp ?>">
    <link rel="manifest" href="__manifest.json">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><style>
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
            Settings
        </div>
    </div>
    <!-- * App Header -->

    <!-- App Capsule -->
    <div id="appCapsule">

        <!-- <div class="section mt-3 text-center">
            <div class="avatar-section">
                <a href="#">
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w100 rounded">
                    <span class="button">
                        <ion-icon name="camera-outline"></ion-icon>
                    </span>
                </a>
            </div>
        </div> -->

        <!-- <div class="listview-title mt-1">Theme</div>
        <ul class="listview image-listview text inset no-line">
            <li>
                <div class="item">
                    <div class="in">
                        <div>
                            Dark Mode
                        </div>
                        <div class="form-check form-switch  ms-2">
                            <input class="form-check-input dark-mode-switch" type="checkbox" id="darkmodeSwitch">
                            <label class="form-check-label" for="darkmodeSwitch"></label>
                        </div>
                    </div>
                </div>
            </li>
        </ul> -->
        <?php  
            if($_SESSION['user_role'] == "Admin"){
        ?>
            <div class="listview-title mt-1">Admin Area</div>
            <ul class="listview image-listview text inset">
                <li>
                    <a href="data-member" class="item">
                        <div class="in">
                            <div>Data Member</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="edit-rewards" class="item">
                        <div class="in">
                            <div>Edit Rewards</div>
                        </div>
                    </a>
                </li>
                <!-- <li>
                    <a href="#" class="item">
                        <div class="in">
                            <div>Report Profit</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item">
                        <div class="in">
                            <div>Report Bonus</div>
                            
                        </div>
                    </a>
                </li> -->
            </ul>
        <?php } ?>

        <div class="listview-title mt-1">Profile Settings</div>
        <ul class="listview image-listview text inset">
            <li>
                <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#change-username">
                    <div class="in">
                        <div>Edit Username</div>
                        <span class="text-secondary" style="font-size: smaller;"><?= $settingData['data'][0]['user_username'] ?></span>
                    </div>
                </a>
            </li>
            <li>
                <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#change-email">
                    <div class="in">
                        <div>Update Email</div>
                        <span class="text-secondary" style="font-size: smaller;"><?= $settingData['data'][0]['user_email'] ?></span>
                    </div>
                </a>
            </li>
            <li>
                <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#bank-akun">
                    <div class="in">
                        <div>Wallet Address</div>
                        <span class="text-secondary" style="font-size: smaller;"> 
                            <?= $previewBank ?>
                        </span>
                    </div>
                </a>
            </li>
        </ul>

        <div class="listview-title mt-1">Security</div>
        <ul class="listview image-listview text mb-2 inset">
            <li>
                <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#change-password">
                    <div class="in">
                        <div>Update Password</div>
                    </div>
                </a>
            </li>
        </ul>

        <div class="row listview image-listview text mb-2 mt-4 inset">
            <a href="logout" class="btn btn-danger">
                <ion-icon name="log-out-outline"></ion-icon>
                Log Out
            </a>
        </div>

        <!-- email Action Sheet -->
        <div class="modal fade action-sheet" id="change-email" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Email</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="post" action="">

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="emailUbahEmail">Email</label>
                                        <input type="email" class="form-control" id="emailUbahEmail" name="emailUbahEmail" placeholder="Enter email" autocomplete="off" value="<?= $emailUbahEmail ?>">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="passwordUbahEmail">Password</label>
                                        <input type="password" class="form-control" id="passwordUbahEmail" name="passwordUbahEmail" placeholder="Enter password" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                                <script>
                                    function loadingForm() {
                                        // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                        document.getElementById("loader").style.display  = "";
                                    }
                                </script>
                                <div class="form-group basic">
                                    <button type="submit" name="simpanUbahEmail"  onclick="loadingForm()" class="btn  btn-success btn-block btn-lg"
                                        data-bs-dismiss="modal">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Username Action Sheet -->
        <!-- Username Action Sheet -->
        <div class="modal fade action-sheet" id="change-username" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Username</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="post" action="">

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="usernameUbahUsername">Username</label>
                                        <input type="text" class="form-control" id="usernameUbahUsername" name="usernameUbahUsername" placeholder="Enter username" autocomplete="off" value="<?= $usernameUbahUsername ?>">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="passwordUbahUsername">Password</label>
                                        <input type="password" class="form-control" id="passwordUbahUsername" name="passwordUbahUsername" placeholder="Enter password" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                                <script>
                                    function loadingForm() {
                                        // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                        document.getElementById("loader").style.display  = "";
                                    }
                                </script>
                                <div class="form-group basic">
                                    <button type="submit" name="simpanUbahUsername" onclick="loadingForm()" class="btn  btn-success btn-block btn-lg"
                                        data-bs-dismiss="modal">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Username Action Sheet -->
        <!-- bank Action Sheet -->
        <div class="modal fade action-sheet" id="bank-akun" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Wallet</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="post" action="">
                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="noBank">Wallet Address</label>
                                        <input type="text" class="form-control" id="noBank" name="noBank" placeholder="Enter Wallet Address" autocomplete="off" value="<?= $noBank ?>">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="passwordAkunBank">Password</label>
                                        <input type="password" class="form-control" id="passwordAkunBank" name="passwordAkunBank" placeholder="Enter password" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <script>
                                    function loadingForm() {
                                        // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                        document.getElementById("loader").style.display  = "";
                                    }
                                </script>
                                <div class="form-group basic">
                                    <button type="submit" name="simpanBank" onclick="loadingForm()" class="btn  btn-success btn-block btn-lg"
                                        data-bs-dismiss="modal">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * bank Action Sheet -->
        <!-- Username Action Sheet -->
        <div class="modal fade action-sheet" id="change-password" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Password</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="post" action="">

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="passwordbaru">Password Baru</label>
                                        <input type="password" class="form-control" id="passwordbaru" name="passwordbaru" placeholder="Enter password baru" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="passwordkonfirm">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" id="passwordkonfirm" name="passwordkonfirm" placeholder="Enter konfirmasi password baru" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                                <script>
                                    function loadingForm() {
                                        // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                        document.getElementById("loader").style.display  = "";
                                    }
                                </script>
                                <div class="form-group basic">
                                    <button type="submit" name="ubahPassword" onclick="loadingForm()" class="btn  btn-success btn-block btn-lg"
                                        data-bs-dismiss="modal">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Username Action Sheet -->

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

    <?php  
        if($_SESSION['user_role'] == "Member"){
    ?>
        <!-- App Bottom Menu -->
        <div class="appBottomMenu">
            <a href="home" class="item">
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
            <a href="app-settings" class="item active">
                <div class="col">
                    <ion-icon name="person-outline"></ion-icon>
                    <strong>Profil</strong>
                </div>
            </a>
        </div>
        <!-- * App Bottom Menu -->
    <?php }else{ ?>
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
            <a href="klaim" class="item">
                <div class="col">
                    <ion-icon name="file-tray-full-outline"></ion-icon>
                    <strong>Klaim</strong>
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
    <script>
        $(document).ready(function() {
            $('#usernameUbahUsername').on('input', function() {
                // Hapus spasi dari input
                $(this).val($(this).val().replace(/\s+/g, ''));
            });

            // Mencegah spasi pada keydown event
            $('#usernameUbahUsername').on('keydown', function(e) {
                if (e.key === ' ') {
                    e.preventDefault();
                }
            });
        });
    </script>


</body>

</html>
<?php $_SESSION['alert_success'] = ""; ?>
