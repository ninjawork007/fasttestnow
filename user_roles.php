<?php
include('includes/head.php');
include('includes/css.php');
require("includes/loader.php");
$con = dbCon();

$q = mysqli_query($con, "SELECT * FROM tbl_users");
$fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);
$rows = mysqli_num_rows($q);
$tr = '';
$i = 1;
$role = '';
$arr = array("Admin", "Nurse");

foreach($fetch as $f) {
    $role = '<option value="0" ></option>';
    for ($x = 1; $x <= 2; $x++) {
        if($x == $f['role'])
            $selected = 'selected="selected"';
        else
            $selected = '';

        $role .= '<option '.$selected.' value='.$x.' >'.$arr[$x-1].'</option>';
    }

    $tr .= '<form id="saveEmployeeForm'.$i.'" method="post"><input type="hidden" name="formAction" value="saveEmployee" /><tr>';
    $tr .= '<input type="hidden" name="ID" value='.$f['id'].' />';
    $tr .= '<td>'.$i.'</td>';
    $tr .= '<td><span class="fieldTxt'.$i.'">'.$f['name'].'</span></td>';
    $tr .= '<td><span class="fieldTxt'.$i.'">'.$f['email'].'</span></td>';
    $tr .= '<td><div class="form-group"><select name="role" class="">'.$role.'</select></div></td>';
    $tr .= '<td><input type="button" value="Set role" class="btn btn-success" onclick="save_role('.$f['id'].', '.($i-1).')"  /></td>';
    $tr .= '</tr></form>';
    $i++;
}
?>

<body>
<div id="main-wrapper">
    <?php
    include('includes/topbar.php');
    include('includes/sidebar.php');
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
            <?php include('includes/footer.php'); ?>
    </div>
    <?php include('includes/script.php'); ?>

    <script type="text/javascript">
        function save_role(id, nth) {
            var role = $("select").eq(nth).find("option:selected").val();
            $.ajax({
                type: "POST",
                url: 'users.php',
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
