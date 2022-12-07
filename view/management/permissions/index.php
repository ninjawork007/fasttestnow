<?php
include("../../../global/variables.php");

include('../../../includes/head.php');
include('../../../includes/css.php');
if (!hasPermission('permissions')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
//require("includes/loader.php");
$con = dbCon();

$q = mysqli_query($con, "SELECT * FROM role_has_permissions ");
$fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);
$rows = mysqli_num_rows($q);
$result = array();
if ($rows > 0) {
    foreach ($fetch as $permRec) {
        $result[$permRec['role_id']][] = $permRec['permission'];
    }
}

//echo '<pre>';
//print_r($result);

$roles = getData("SELECT * FROM tbl_roles");
?>

<body>
    <div id="main-wrapper">
        <?php
        include(__ROOT . '/includes/topbar.php');
        include(__ROOT . '/includes/sidebar.php');
        
        ?>
        <div class="content-body">
            <!-- Start content -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <div class="card border border-dark">
                            <h5 class="card-title text-center m-3">
                                Add new Role
                            </h5>
                            <div class="card-body">
                                <form method="post" class="row align-items-center">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="name">Role Name</label>
                                            <input type="text" required placeholder="Enter role name" name="new_role_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-success mt-2" name="new_role">Add Role</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border border-dark">
                            <h5 class="card-title text-center m-3">
                                Manage Role
                            </h5>
                            <div class="card-body">

                                <?php
                                if (isset($_POST['type'])) {
                                    $roleName = $_POST['new_role_name'];
                                    $rolId    = $_POST['role_id'];

                                    if ($_POST['type'] == 0) {
                                        mysqli_query($con, "UPDATE tbl_roles SET name = '" . $roleName . "' WHERE id = $rolId");
                                    } else {

                                        mysqli_query($con, "DELETE FROM tbl_roles WHERE id = $rolId");
                                    }
                                    //  header("Refresh:0");
                                }
                                ?>

                                <?php foreach ($roles as $delete_role) { ?>
                                    <form action="" id="roleform<?= $delete_role['id'] ?>" method="post">
                                        <div class="row m-1">
                                            <div class="col-6">
                                                <input type="hidden" name="role_id" value="<?php echo $delete_role['id'] ?>">
                                                <input type="text" required placeholder="Enter role name" name="new_role_name" class="form-control" value="<?php echo $delete_role['name']; ?>">
                                            </div>
                                            <div class="col-6">
                                                <i class="fa fa-edit fa-2x p-1" role="button" onclick="editDelete('0', '<?= $delete_role['id'] ?>')"></i>
                                                <i class="text-danger fa fa-trash fa-2x p-1" role="button" onclick="editDelete('1', '<?= $delete_role['id'] ?>')"></i>
                                            </div>

                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>



                    <div class="col-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <?php foreach ($roles as $navrole) { ?>
                                <a class="nav-link <?php echo ($navrole['id'] == 1) ? 'active' : ''; ?>" id="v-pills-<?php echo $navrole['id']; ?>-tab" data-toggle="pill" href="#v-pills-<?php echo $navrole['id']; ?>" role="tab" aria-controls="v-pills-<?php echo $navrole['id']; ?>" aria-selected="true"><?php echo $navrole['name']; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <?php $roleIds = "";
                            foreach ($roles as $role) {
                                $rolename = $role['name'];
                                $roleid = $role['id'];
                                $roleIds .= $role['id'] . ",";
                            ?>
                                <div class="tab-pane fade <?php echo ($role['id'] == 1) ? 'show active' : ''; ?>" id="v-pills-<?php echo $roleid ?>" role="tabpanel" aria-labelledby="v-pills-<?php echo $roleid ?>-tab">
                                    <div class="card border border-danger">
                                        <div class="card-header bg-danger">
                                            <h5 class="card-title text-white">Accessible Pages</h5>
                                        </div>
                                        <?php
                                        if (isset($_POST['submit'])) {
                                            
                                            $roleId = $_POST['submit'];
                                            unset($_POST['submit']);
                                            mysqli_query($con, "DELETE FROM role_has_permissions WHERE role_id = $roleId");
                                            unset($_SESSION['permissions']);
                                            foreach ($_POST as $key => $postValues) {
                                                $q = mysqli_query($con, "INSERT INTO role_has_permissions (permission, role_id, name)
                                    VALUES ('" . $key . "', $roleId, '" . $postValues . "')");
                                            }
                                            //  header("Refresh:0");
                                        }
                                        if (isset($_POST['new_role']) && isset($_POST['new_role_name'])) {
                                            
                                            $new_role_name = $_POST['new_role_name'];
                                            unset($_POST['new_role_name']);
                                            $q = mysqli_query($con, "INSERT INTO tbl_roles (name)
                                    VALUES ('" . $new_role_name . "')");
                                            if($q) {
                                                header("Refresh:0");
                                            }
                                        }
                                        ?>

                                        <form id=<?php echo strtolower($rolename) . "_permission"; ?> role="form" action="" method="post">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <input type="checkbox" name="create_report" id="create_report<?= $role['id'] ?>" onclick="checkAll('create_report<?= $role['id'] ?>','cr_report')">
                                                    <label for="create_report">Create Report</label>
                                                </div>


                                                <div class="row ml-4 create_report<?php echo $role['id'] ?>">
                                                    <div class="form-group w-100">
                                                        <?php if($result[$roleid] == NULL) { $result[$roleid] = array(); } ?>
                                                        <input type="checkbox" name="create_antigen_report" value="Antigen" id="create_antigen_report" <?php echo (in_array('create_antigen_report', $result[$roleid])? 'checked': ""); ?> class="cr_report">
                                                        <label for="create_antigen_report">Antigen</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_pcr_report" value="Visby PCR" id="create_pcr_report" <?php echo (in_array('create_pcr_report', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_pcr_report">Visby PCR</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_accula_report" value="Accula RT-PCR" id="create_accula_report" <?php echo (in_array('create_accula_report', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_accula_report">Accula RT-PCR</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_antibody_report" value="Antibody Screening" id="create_antibody_report" <?php echo (in_array('create_antibody_report', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_antibody_report">Antibody Screening</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_flu_report" value="FLU" id="create_flu_report" <?php echo (in_array('create_flu_report', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_flu_report">FLU</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_rx_order" value="Rx Order" id="create_rx_order" <?php echo (in_array('create_rx_order', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_rx_order">Rx Order</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_mono_test" value="Mono Test" id="create_mono_test" <?php echo (in_array('create_mono_test', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_mono_test">MONO TEST </label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_syphilis_test" value="Syphilis Test" id="create_syphilis_test" <?php echo (in_array('create_syphilis_test', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_syphilis_test">SYPHILIS TEST </label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_hiv_test" value="Hiv Test" id="create_hiv_test" <?php echo (in_array('create_hiv_test', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_hiv_test">HIV TEST </label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_strep_test" value="Strep Test" id="create_strep_test" <?php echo (in_array('create_strep_test', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_strep_test">STREP TEST</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_hemoglobin_screening" value="Hemoglobin Screening" id="create_hemoglobin_screening" <?php echo (in_array('create_hemoglobin_screening', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_hemoglobin_screening">HEMOGLOBIN SCREENING</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_rsv_test" value="Rsv Test" id="create_rsv_test" <?php echo (in_array('create_rsv_test', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_rsv_test">RSV TEST</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="create_rapid_thyroid" value="Rapid Thyroid Test" id="create_rapid_thyroid" <?php echo (in_array('create_rapid_thyroid', $result[$roleid]) ? 'checked' : '') ?> class="cr_report">
                                                        <label for="create_rapid_thyroid">RAPID THYROID TEST </label>
                                                    </div>

                                                </div>
                                                <div class="form-group">
                                                    <input type="checkbox" name="history" id="history<?= $role['id'] ?>" onclick="checkAll('history<?= $role['id'] ?>','history')">
                                                    <label for="history">History</label>
                                                </div>

                                                <div class="row ml-4 history<?= $role['id'] ?>">    
                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="report_history" value="Report History" id="report_history" <?php echo (in_array('report_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="report_history">Covid Report</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="appointment_history" value="appointment History" id="report_history" <?php echo (in_array('appointment_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="appointment_history">Appointment</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="rx_order_history" value="Rx Order History" id="rx_order_history" <?php echo (in_array('rx_order_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="rx_order_history">Rx Order</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="mono_history" value="Mono History" id="mono_history" <?php echo (in_array('mono_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="mono_history">Mono</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="syphilis_history" value="Syphilis History" id="syphilis_history" <?php echo (in_array('syphilis_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="syphilis_history">Syphilis</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="hiv_history" value="Hiv History" id="hiv_history" <?php echo (in_array('hiv_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="hiv_history">Hiv</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="strep_history" value="Strep History" id="strep_history" <?php echo (in_array('strep_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="strep_history">Strep</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="hemoglobin_history" value="Hemoglobin History" id="hemoglobin_history" <?php echo (in_array('hemoglobin_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="hemoglobin_history">Hemoglobin</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="rsv_history" value="Rsv History" id="rsv_history" <?php echo (in_array('rsv_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="rsv_history">Rsv</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="rapid_thyroid_history" value="Rapid Thyroid History" id="rapid_thyroid_history" <?php echo (in_array('rapid_thyroid_history', $result[$roleid]) ? 'checked' : '') ?> class="history">
                                                        <label for="rapid_thyroid_history">Rapid Thyroid</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <input type="checkbox" name="user_management" id="user_management<?= $role['id'] ?>" onclick="checkAll('user_management<?= $role['id'] ?>','us_manage')">
                                                    <label for="user_management">User Management</label>
                                                </div>

                                                <div class="row ml-4 user_management<?= $role['id'] ?>">
                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="management_users" value="Account Management" id="create_report" <?php echo (in_array('management_users', $result[$roleid]) ? 'checked' : '') ?> class="us_manage">
                                                        <label for="management_users">Account Management</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="user_roles" value="Role Management" id="create_report" <?php echo (in_array('user_roles', $result[$roleid]) ? 'checked' : '') ?> class="us_manage">
                                                        <label for="create_report">Role Management</label>
                                                    </div>

                                                    <div class="form-group w-100">
                                                        <input type="checkbox" name="permissions" value="Permission Management" id="create_report" <?php echo (in_array('permissions', $result[$roleid]) ? 'checked' : '') ?> class="us_manage">
                                                        <label for="create_report">Permission Management</label>
                                                    </div>
                                                </div>

                                                
                                                <div class="form-group">
                                                    <input type="checkbox" name="requisition_form" value="Requisition Form" id="requisition_form" <?php echo (in_array('requisition_form', $result[$roleid]) ? 'checked' : '') ?>>
                                                    <label for="requisition_form">Requisition Forms</label>
                                                </div>
                                                <div class="form-group w-100 text-center">
                                                    <button type="submit" name="submit" value="<?php echo $roleid; ?>" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php }
                            ?>
                            <input type="hidden" id="allRoleId" value="<?= substr($roleIds, 0, -1) ?>">
                        </div>
                    </div>

                </div>
                <!--row end-->

                <!-- <div class="col-md-6 col-12">
                        <div class="card border border-danger">
                             <div class="card-header bg-danger">
                            <h5 class="card-title text-white">Nurse</h5>
                             </div>
                            <form id="nurse_permission" role="form" action="" method="post">
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="checkbox" name="user_management" value="User Management" id="name" <?php echo (in_array('user_management', $result[2]) ? 'checked' : '') ?>>
                                        <label for="name">User Management</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" name="create_report" value="Create Report" id="name" <?php echo (in_array('create_report', $result[2]) ? 'checked' : '') ?>>
                                        <label for="name">Create Report</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" name="report_history" value="Report History" id="name" <?php echo (in_array('report_history', $result[2]) ? 'checked' : '') ?>>
                                        <label for="name">Report History</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" name="history_report" value="History Report" id="name" <?php echo (in_array('history_report', $result[2]) ? 'checked' : '') ?>>
                                        <label for="name">History Report</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" name="requisition_form" value="Requisition Form" id="name" <?php echo (in_array('requisition_form', $result[2]) ? 'checked' : '') ?>>
                                        <label for="name">Requisition Forms</label>
                                    </div>
                                    <div class="form-group w-100 text-center">
                                        <button type="submit" name="submit" value="2" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> -->


            </div>
        </div>


    </div>
    <?php include('../../../includes/footer.php'); ?>
    </div>
    <?php include('../../../includes/script.php'); ?>

    <script type="text/javascript">
        
        function checkAll(elemId, childId) {
            ($("#" + elemId).is(':checked')) ? $('.' + elemId + ' .' + childId + '').prop('checked', true): $('.' + elemId + ' .' + childId + '').prop('checked', false);
        }

        function editDelete(type, id) {
            var form = $('#roleform' + id);
            $("<input type='hidden' value='" + type + "' />")
                .attr("name", "type")
                .prependTo(form);
            form.submit();

        }

        (function($) {

            $.each($("#allRoleId").val().split(","), function(i, val) {
                var crReport = $('.create_report' + val + ' input.cr_report:checked').length;
                (crReport == 13) ? $("#create_report" + val + "").prop("checked", true): $("#create_report" + val + "").prop("checked", false);

                var history = $('.history' + val + ' input.history:checked').length;
                (history == 10) ? $("#history" + val + "").prop("checked", true): $("#history" + val + "").prop("checked", false);

                var usReport = $('.user_management' + val + ' input.us_manage:checked').length;
                (usReport == 3) ? $("#user_management" + val + "").prop("checked", true): $("#user_management" + val + "").prop("checked", false);
            });
            "use strict"
            //example 1
            var table = $('#example2').DataTable({
                createdRow: function(row, data, index) {
                    $(row).addClass('selected')
                }
            });

            table.on('click', 'tbody tr', function() {
                var $row = table.row(this).nodes().to$();
                var hasClass = $row.hasClass('selected');
                if (hasClass) {
                    $row.removeClass('selected')
                } else {
                    $row.addClass('selected')
                }
            })

            table.rows().every(function() {
                this.nodes().to$().removeClass('selected')
            });


        })(jQuery);
        $(".dashboard_bar").html("Manage Permissions");
    </script>
</body>

</html>