<!-- ========== Left Sidebar Start ========== -->
<div class="deznav">
    <div class="deznav-scroll">
        <?php //var_dump($_SESSION['role']);die; ?>
            <!-- Left Menu Start -->
            <ul class="metismenu" id="menu">
                <?php if ($_SESSION['role'] == 1) : ?>
                    <li style="display: block;">
                        <a href="dashboard.php" class="ai-icon"><i class="mdi mdi-view-dashboard"></i><span class="nav-text"> Dashboard </span></a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="mdi mdi-new-box"></i></i><span class="nav-text"> Create Report  </span></a>
                        <ul aria-expanded="false">
                            <li><a href="create_antigen_report.php">Antigen</a></li>
                            <li><a href="create_pcr_report.php">Visby PCR</a></li>
                            <li><a href="create_accula_report.php">Accula Rt-PCR</a></li>
                            <li><a href="create_antibody_report.php">Antibody Screening</a></li>
                            <li><a href="create_flu_report.php">FLU</a></li>
                        </ul>
                    </li>
                    <li style="display: block;">
                        <a href="report_history.php" class="ai-icon" aria-expanded="false"><i class="mdi mdi-history"></i><span class="nav-text"> Report History </span></a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="mdi mdi-account-multiple"></i><span class="nav-text"> User management  </span></a>
                        <ul aria-expanded="false">
                            <li><a href="management_users.php">Account management</a></li>
                            <li><a href="user_roles.php">Role management</a></li>
                        </ul>
                    </li>
                <?php else : ?>

                    <li style="display: block;">
                        <a href="dashboard.php"  class="ai-icon" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="nav-text"> Dashboard </span></a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="mdi mdi-new-box"></i></i><span class="nav-text"> Create Report  </span></a>
                        <ul aria-expanded="false">
                            <li><a href="create_antigen_report.php">Antigen</a></li>
                            <li><a href="create_pcr_report.php">Visby PCR</a></li>
                            <li><a href="create_accula_report.php">Accula Rt-PCR</a></li>
                            <li><a href="create_antibody_report.php">Antibody Screening</a></li>
                            <li><a href="create_flu_report.php">FLU</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow ai-icon" aria-expanded="false"><i class="mdi mdi-history"></i><span class="nav-text"> History Report  </span></a>
                        <ul aria-expanded="false">
                            <!-- <li><a href="report_history.php">All</a></li> -->
                            <li><a href="visby.php">Visby PCR</a></li>
                            <li><a href="antigen.php">Antigen</a></li>
                            <li><a href="accula.php">Accula Rt-PCR</a></li>
                            <li><a href="antibody.php">Antibody Screening</a></li>
                        </ul>
                    </li>

                <?php endif; ?>
                    <li style="display: block;">
                        <a href="requisition_form.php"  class="ai-icon" aria-expanded="false"><i class="mdi mdi-format-list-numbers"></i><span class="nav-text"> Requisition Forms </span></a>
                    </li>
            </ul>

        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

<!-- Left Sidebar End -->

