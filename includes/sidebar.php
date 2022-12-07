<?php 

$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
// $host = $_SERVER['HTTP_HOST'] . '/';
$host = $_SERVER['HTTP_HOST'];
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . '/' . $project) : ($protocole . $host);

$root = $_SERVER["DOCUMENT_ROOT"];
$myRoot = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($root . "/" . $project) : $root;

?>
<!-- ========== Left Sidebar Start ========== -->
<div class="deznav">
    <div class="deznav-scroll">
        <?php //var_dump($_SESSION['role']);die; ?>
            <!-- Left Menu Start -->
            <ul class="metismenu" id="menu">
                    <li style="display: block;">
                        <a href="<?php echo $url; ?>/view/dashboard/index.php" class="ai-icon"><i class="flaticon-381-networking"></i><span class="nav-text"> Dashboard </span></a>
                    </li>
                    <?php if(hasPermission('appointment_history')) : ?>
                    <li style="display: block;">
                        <a href="<?php echo $url; ?>/view/appointment/history.php" class="ai-icon"><i class="flaticon-381-heart"></i><span class="nav-text"> Appointment </span></a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="flaticon-381-layer-1"></i></i><span class="nav-text"> Create Report  </span></a>
                        <ul aria-expanded="false">
                            <?php if(hasPermission('create_antigen_report') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/covid_19/antigen/create.php">Antigen</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_pcr_report') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/covid_19/pcr/create.php">Visby PCR</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_accula_report') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/covid_19/accula/create.php">Accula Rt-PCR</a></li>
                            <?php endif; ?>                            
                            <?php if(hasPermission('create_antibody_report') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/covid_19/antibody/create.php">Antibody Screening</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_flu_report') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/flu/create.php">FLU</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_rx_order') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/rx_order/create.php">Rx Order</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_mono_test') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/mono/create.php">Mono Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_syphilis_test') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/syphilis/create.php">Syphilis Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_hiv_test') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/hiv/create.php">Hiv Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_strep_test') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/strep/create.php">Strep Test</a></li>
                            <?php endif; ?>                            
                            <?php if(hasPermission('create_hemoglobin_screening') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/hemoglobin/create.php">Hemoglobin Screening</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_rsv_test') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/rsv/create.php">Rsv Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_rapid_thyroid') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/rapid_thyroid/create.php">Rapid Thyroid Test</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="flaticon-381-notepad"></i></i><span class="nav-text"> History  </span></a>
                        <ul aria-expanded="false">
                            <?php if(hasPermission('report_history')) : ?>
                            <li><a href="<?php echo $url; ?>/view/covid_19/covidFluHistory/index.php"> Covid/Flu Report </a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('rx_order_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/rx_order/history.php">Rx Order</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('mono_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/mono/history.php">Mono Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('syphilis_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/syphilis/history.php">Syphilis Test</a></li>
                            <?php endif; ?>     
                            <?php if(hasPermission('hiv_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/hiv/history.php">Hiv Test</a></li>
                            <?php endif; ?>  
                            <?php if(hasPermission('strep_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/strep/history.php">Strep Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('hemoglobin_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/hemoglobin/history.php">Hemoglobin Screening</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('rsv_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/rsv/history.php">RSV Test</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('rapid_thyroid_history') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/rapid_thyroid/history.php">Rapid Thyroid Test</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="flaticon-381-settings-2"></i><span class="nav-text"> Management  </span></a>
                        <ul aria-expanded="false">
                            <?php if(hasPermission('management_users') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/management/staff/index.php">User Account</a></li>
                            <?php endif; ?>
                             <?php if(hasPermission('user_roles') ) : ?>
                            <li><a href="<?php echo $url; ?>/view/management/roles/index.php">User Role</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('permissions') )  { ?>
                                <li><a href="<?php echo $url; ?>/view/management/permissions/index.php">User Permission</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php if(hasPermission('requisition_form')) : ?>
                    <li style="display: block;">
                        <a href="<?php echo $url; ?>/view/requisition/index.php" class="ai-icon" aria-expanded="false"><i class="flaticon-381-heart"></i><span class="nav-text"> Requistion form </span></a>
                    </li>
                    <?php endif; ?>
            </ul>

        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

<!-- Left Sidebar End -->

