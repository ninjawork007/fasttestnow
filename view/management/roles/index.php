<?php
include("../../../global/variables.php");

include(__ROOT . '/includes/head.php');
include(__ROOT . '/includes/css.php');
if(!hasPermission('user_roles')){
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
require(__ROOT . "/includes/loader.php");
$con = dbCon();

$q = mysqli_query($con, "SELECT * FROM tbl_users");
$fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);
$rows = mysqli_num_rows($q);
$tr = '';
$i = 1;
$role = '';
$roles = getData("SELECT * FROM tbl_roles");

foreach($fetch as $f) {
    $role = '<option disabled>Select Role</option>';
    foreach($roles as $dbrole) {
        if($dbrole['id'] == $f['role'])
            $selected = 'selected="selected"';
        else
            $selected = '';

        $role .= '<option '.$selected.' value='.$dbrole['id'].' >'.$dbrole['name'].'</option>';
    }

    $tr .= '<form id="saveEmployeeForm'.$i.'" method="post"><input type="hidden" name="formAction" value="saveEmployee" /><tr>';
    $tr .= '<input type="hidden" name="ID" value='.$f['id'].' />';
    $tr .= '<td>'.$i.'</td>';
    $tr .= '<td><span class="fieldTxt'.$i.'">'.$f['name'].'</span></td>';
    $tr .= '<td><span class="fieldTxt'.$i.'">'.$f['email'].'</span></td>';
    $tr .= '<td><div class="form-group"><select name="role" id="roleSel'.$i.'" placeholder="select role">'.$role.'</select></div></td>';
    $tr .= '<td><input type="button" value="Set role" class="btn btn-success" onclick="save_role('.$f['id'].', '.$i.')"  /></td>';
    $tr .= '</tr></form>';
    $i++;
}
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
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title text-primary">User roles</h4>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row" >
                    <div class="table-responsive">
                    <table id="example2" class="table card-table display dataTablesCard">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        echo $tr;
                        ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                
            </div>
            <?php include(__ROOT . '/includes/footer.php'); ?>
    </div>
    <?php include(__ROOT . '/includes/script.php'); ?>

    <script type="text/javascript">
        function save_role(id, nth) {
            //var role = $("select").eq(nth).find("option:selected").val();
            var role = $("#roleSel"+nth+"").val();
            $.ajax({
                type: "POST",
                url: $host + '/model/staff.php',
                data:{method:'setUserRole', id:id, role: role},
                success:function(data) {
                    hideLoadingBar();
                    console.log('SUCCESS Set role!!!' + data);
                    location.reload();
                }

            });
        }
        (function($) {
			"use strict"
            //example 1
            var table = $('#example2').DataTable({
                createdRow: function ( row, data, index ) {
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
        $(".dashboard_bar").html("Manage Users");
    </script>
</body>
</html>
