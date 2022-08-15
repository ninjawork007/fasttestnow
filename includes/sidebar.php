<!-- ========== Left Sidebar Start ========== -->
<div class="deznav">
    <div class="deznav-scroll">
        <?php //var_dump($_SESSION['role']);die; ?>
            <!-- Left Menu Start -->
            <ul class="metismenu" id="menu">
                    <li style="display: block;">
                        <a href="dashboard.php" class="ai-icon"><i class="mdi mdi-view-dashboard"></i><span class="nav-text"> Dashboard </span></a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="mdi mdi-new-box"></i></i><span class="nav-text"> Create Report  </span></a>
                        <ul aria-expanded="false">
                            <?php if(hasPermission('create_antigen_report') ) : ?>
                            <li><a href="create_antigen_report.php">Antigen</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_pcr_report') ) : ?>
                            <li><a href="create_pcr_report.php">Visby PCR</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_accula_report') ) : ?>
                            <li><a href="create_accula_report.php">Accula Rt-PCR</a></li>
                            <?php endif; ?>                            
                            <?php if(hasPermission('create_antibody_report') ) : ?>
                            <li><a href="create_antibody_report.php">Antibody Screening</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('create_flu_report') ) : ?>
                            <li><a href="create_flu_report.php">FLU</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <?php if(hasPermission('report_history')) : ?>
                    <li style="display: block;">
                        <a href="report_history.php" class="ai-icon" aria-expanded="false"><i class="mdi mdi-history"></i><span class="nav-text"> Report History </span></a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="mdi mdi-account-multiple"></i><span class="nav-text"> User management  </span></a>
                        <ul aria-expanded="false">
                            <?php if(hasPermission('management_users') ) : ?>
                            <li><a href="management_users.php">Account management</a></li>
                            <?php endif; ?>
                             <?php if(hasPermission('user_roles') ) : ?>
                            <li><a href="user_roles.php">Role management</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('permissions') )  { ?>
                                <li><a href="permissions.php">Permission management</a></li>
                                <?php } ?>
                        </ul>
                    </li>
            
            </ul>

        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

<!-- Left Sidebar End -->

