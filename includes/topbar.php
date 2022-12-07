<!-- Top Bar Start -->
<?php

$user_role_q ="SELECT * FROM tbl_roles WHERE id='".$_SESSION["role"]."'";
$user_role_result = mysqli_query($mysqli, $user_role_q);
$user_role_row = mysqli_fetch_array($user_role_result);

$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
$host = $_SERVER['HTTP_HOST'] . '/';
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . $project) : ($protocole . $host);

$root = $_SERVER["DOCUMENT_ROOT"];
$myRoot = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($root . "/" . $project) : $root;
?>
 <!--**********************************
    Nav header start
***********************************-->
<div class="nav-header">
    <a href="<?php echo $url; ?>/view/dashboard/index.php" class="brand-logo">
        <img class="brand-title" src="<?php echo $url; ?>/images/logo.png" alt="">
    </a>

    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>
<!--**********************************
    Nav header end
***********************************-->

<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        Dashboard
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-toggle="dropdown">
                            <img src="<?php echo $url; ?>/assets/images/profile/17.jpg" width="20" alt=""/>
                            <div class="header-info">
                              <span class="text-black"><strong><?php echo $_SESSION['admin_name']; ?></strong></span>
                              <p class="fs-12 mb-0"><?php echo ($_SESSION['role'] == 0 ) ? 'Super Admin' : $user_role_row['name']; ?></p>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if($_SESSION["role"] == 1) : ?>
                            <a href="<?php echo $url; ?>/view/management/staff/list.php" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="ml-2">Users </span>
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo $url; ?>/view/login/logout.php" class="dropdown-item ai-icon">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                <span class="ml-2">Logout </span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
    Header end ti-comment-alt
***********************************-->

