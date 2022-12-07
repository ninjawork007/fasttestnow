<?php

include("../../global/variables.php");

include(__ROOT . '/includes/head.php');

include(__ROOT . '/includes/css.php');



//require("includes/loader.php");


if ($_SESSION['role'] == 1 || $_SESSION['role'] == 0) {
    $query1 = "SELECT * FROM tbl_report WHERE type_id=1";
    $query2 = "SELECT * FROM tbl_report WHERE type_id=2";
    $query3 = "SELECT * FROM tbl_report WHERE type_id=3";
    $query4 = "SELECT * FROM tbl_report WHERE type_id=4";
}
if ($_SESSION['role'] == 2) {
    $user_id = $_SESSION['id'];
    $query1 = "SELECT * FROM tbl_report WHERE type_id=1 AND user_id=$user_id";
    $query2 = "SELECT * FROM tbl_report WHERE type_id=2 AND user_id=$user_id";
    $query3 = "SELECT * FROM tbl_report WHERE type_id=3 AND user_id=$user_id";
    $query4 = "SELECT * FROM tbl_report WHERE type_id=4 AND user_id=$user_id";
}

$table1 = 0;
$table2 = 0;
$table3 = 0;
$table4 = 0;

if ($result = mysqli_query($mysqli, $query1)) {
    // Return the number of rows in result set
    $table1 = mysqli_num_rows($result);
    // Free result set
    mysqli_free_result($result);
}

if ($result = mysqli_query($mysqli, $query2)) {
    // Return the number of rows in result set
    $table2 = mysqli_num_rows($result);
    // Free result set
    mysqli_free_result($result);
}

if ($result = mysqli_query($mysqli, $query3)) {
    // Return the number of rows in result set
    $table3 = mysqli_num_rows($result);
    // Free result set
    mysqli_free_result($result);
}

if ($result = mysqli_query($mysqli, $query4)) {
    // Return the number of rows in result set
    $table4 = mysqli_num_rows($result);
    // Free result set
    mysqli_free_result($result);
}

date_default_timezone_set('American/Los_Angeles');
$today = date('F d Y', time());

?>

<body>
    <div id="main-wrapper">
        <?php
        include(__ROOT . '/includes/topbar.php');
        include(__ROOT . '/includes/sidebar.php');
        ?>
        <div class="content-body">
            <!-- Start content -->
            <div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title text-primary">Dashboard</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                    <div class="row">
                        <?php if (hasPermission('create_pcr_report')) : ?>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
                                            <div class="media-body">
                                                <p class="mb-1 text-primary">Visby PCR</p>
                                                <h3 class="mb-0"></h3>
                                                <a href="<?php echo _HOST_LINK; ?>/view/covid_19/pcr/create.php?type=1" class="btn btn-outline-primary">Create Report</a>
                                            </div>
                                            <span class="mr-3 bgl-primary text-primary" id="dashboard_visby" style="cursor: pointer;">
                                                <i class="mdi mdi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (hasPermission('create_antigen_report')) : ?>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
                                            <div class="media-body">
                                                <p class="mb-1 text-primary">Antigen</p>
                                                <h3 class="mb-0"></h3>
                                                <a href="<?php echo _HOST_LINK; ?>/view/covid_19/antigen/create.php?type=2" class="btn btn-outline-primary">Create Report</a>
                                            </div>
                                            <span class="mr-3 bgl-primary text-primary" id="dashboard_antigen" style="cursor: pointer;">
                                                <i class="mdi mdi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (hasPermission('create_accula_report')) : ?>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
                                            <div class="media-body">
                                                <p class="mb-1 text-primary">Accula Rt-PCR</p>
                                                <h3 class="mb-0"></h3>
                                                <a href="<?php echo _HOST_LINK; ?>/view/covid_19/accula/create.php?type=3" class="btn btn-outline-primary">Create Report</a>
                                            </div>
                                            <span class="mr-3 bgl-primary text-primary" id="dashboard_accula" style="cursor: pointer;">
                                                <i class="mdi mdi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (hasPermission('create_antibody_report')) : ?>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
                                            <div class="media-body">
                                                <p class="mb-1 text-primary">Antibody</p>
                                                <h3 class="mb-0"></h3>
                                                <a href="<?php echo _HOST_LINK; ?>/view/covid_19/antibody/create.php?type=4" class="btn btn-outline-primary">Create Report</a>
                                            </div>
                                            <span class="mr-3 bgl-primary text-primary" id="dashboard_antibody" style="cursor: pointer;">
                                                <i class="mdi mdi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (hasPermission('create_flu_report')) : ?>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
                                            <div class="media-body">
                                                <p class="mb-1 text-primary">FLU</p>
                                                <h3 class="mb-0"></h3>
                                                <a href="<?php echo _HOST_LINK; ?>/view/flu/create.php?type=5" class="btn btn-outline-primary">Create Report</a>
                                            </div>
                                            <span class="mr-3 bgl-primary text-primary" id="dashboard_flu" style="cursor: pointer;">
                                                <i class="mdi mdi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                    <?php include('includes/footer.php'); ?>
                </div>
            </div>

            <?php 
            
                include(__ROOT . '/includes/script.php');

            ?>
            <script type="text/javascript">
                var _host = '<?php echo _HOST_LINK; ?>'
                $(document).ready(function() {

                    $('#dashboard_visby').click(function() {
                        window.location = _host + "/view/covid_19/pcr/history.php";
                        return false;
                    });

                    $('#dashboard_antigen').click(function() {
                        window.location = _host + "/view/covid_19/antigen/history.php";
                        return false;
                    });

                    $('#dashboard_accula').click(function() {
                        window.location = _host + "/view/covid_19/accula/history.php";
                        return false;
                    });

                    $('#dashboard_antibody').click(function() {
                        window.location = _host + "/view/covid_19/antibody/history.php";
                        return false;
                    });
                    $(".dashboard_bar").html("Dashboard");
                });
            </script>
            <?php include(__ROOT . '/includes/script-bottom.php'); ?>
</body>

</html>