<?php  
session_start();
error_reporting(0);
include "config/depositConfig.php"
?>

<!doctype html>
<html lang="en">

<head>
    <?php include "partial/meta.php" ?>
    <title>Oniria - Deposit</title>
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
            <a href="home" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">
            Deposit
        </div>
    </div>
    <!-- * App Header -->

    <!-- App Capsule -->
    <div id="appCapsule">

        <div class="section mt-2">
            <div class="section-title">Data Deposit</div>
            <div class="card">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="min-width: 150px;">Date</th>
                                <th >Member</th>
                                <th >Nominal</th>
                                <th >Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $tableDepo = tableDepo($page);
                                if($tableDepo['row'] == 0){
                                    echo '<tr><td colspan="4" align="center">Data tidak ditemukan.</td></tr>';
                                }else{
                                    foreach($tableDepo['data'] as $row){
                            ?>
                            <tr>
                                <th><?= $row['date'] ?></th>
                                <td><?= memberName($row['deposit_user_id']) ?></td>
                                <td><?= number_format($row['deposit_nominal']) ?> USDT</td>
                                <td><span class="text-<?= $row['deposit_status'] == "Pending" ? "warning" : ($row['deposit_status'] == "Success" ? "success" : "danger") ?>"><?= $row['deposit_status'] ?></span></td>
                                <td class="text-end">
                                    <a href="#depoActionSheet<?= $row['deposit_id'] ?>" data-bs-toggle="modal">
                                        <ion-icon name="ellipsis-vertical-outline" class="text-primary"></ion-icon>
                                    </a>
                                </td>
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
                if($total_pages > 0 && $tableDepo['row'] > 0){
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

        <?php  
            if($tableDepo['row'] > 0){
                foreach($tableDepo['data'] as $row){
        ?>
            <!-- Withdraw Action Sheet -->
            <div class="modal fade action-sheet" id="depoActionSheet<?= $row['deposit_id'] ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Deposit</h5>
                        </div>
                        <div class="modal-body">
                            <div class="action-sheet-content">
                                <form method="post" action="">
                                    <div class="form-group basic">
                                        <label class="label" for="mount">Admin Wallet Address</label>
                                        <span style="font-size: smaller;"><?= $row['deposit_bank_admin'] ?></span>
                                    </div>
                                    <div class="form-group basic">
                                        <label class="label" for="mount">Member Wallet Address</label>
                                        <span style="font-size: smaller;"><?= $row['deposit_bank_user'] ?></span>
                                    </div>
                                    <div class="form-group basic">
                                        <label class="label" for="mount">Transaction Number</label>
                                        <h4><?= $row['deposit_bukti'] ?></h4>
                                    </div>
                                    <div class="form-group basic">
                                        <label class="label" for="mount">Amount</label>
                                        <h4><?= number_format($row['deposit_nominal']) ?> USDT</h4>
                                    </div>
                                    <script>
                                        function loadingForm() {
                                            // Mengatur tombol menjadi tidak dapat di-klik selama proses loading
                                            document.getElementById("loader").style.display  = "";
                                        }
                                    </script>
                                    <input type="hidden" name="idDepo" value="<?= $row['deposit_id'] ?>">
                                    <?php if($row['deposit_status'] == "Pending"){ ?>
                                    <div class="form-group basic d-flex">
                                        <button type="submit" name="tolakDEPO" onclick="loadingForm()" class="btn btn-danger btn-block btn-lg"
                                            data-bs-dismiss="modal">Tolak</button>
                                        <button type="submit" name="konfirDEPO" onclick="loadingForm()" class="btn btn-success btn-block btn-lg"
                                            data-bs-dismiss="modal">Konfirmasi</button>
                                    </div>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * Withdraw Action Sheet -->
        <?php  
                }
            }
        ?>

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
        <!-- <a href="klaim" class="item">
            <div class="col">
                <ion-icon name="file-tray-full-outline"></ion-icon>
                <strong>Klaim</strong>
            </div>
        </a> -->
        <a href="deposit" class="item active">
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