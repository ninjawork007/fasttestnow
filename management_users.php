<?php
include('includes/head.php');
include('includes/css.php');
if(!hasPermission('management_users')){
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
require("includes/loader.php");
$con = dbCon();

$q = mysqli_query($con, "SELECT * FROM tbl_users where role!='0'");
$fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);
$rows = mysqli_num_rows($q);
$tr = '';
$i = 1;

$role = '';
$selected = '';
$txtRole = ($row['role']==2)? "Nurse":"Admin";
$arr = array("Admin", "Nurse");

foreach($fetch as $f) {
    $role = '<option value="0" >Select Role</option>';
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
    $tr .= '<td><span class="fieldTxt'.$i.'">'.$f['name'].'</span><input type="text" name="name" id="name'.$i.'" class="form-control form-control-sm editFieldTxt'.$i.'" value="'.$f['name'].'" style="display:none;" /></td>';
    $tr .= '<td title="'.$f['password'].'"><input type="text" name="password" id="password'.$i.'" class="form-control form-control-sm editFieldTxt'.$i.'" value="" style="display:none;" /></td>';
    $tr .= '<td><span class="fieldTxt'.$i.'">'.$f['email'].'</span><input type="text" name="email" id="email'.$i.'" class="form-control form-control-sm editFieldTxt'.$i.'" value="'.$f['email'].'" style="display:none;" /></td>';
    //$tr .= '<td><select onchange="setSalary(this, '.$i.', \'\')" name="salary" id="role'.$i.'" style="max-width:200px;">'.$role.'</select></td>';
    $tr .= '<td><input type="button" value="Edit" class="btn btn-success editButton'.$i.'" onclick="edit('.$i.')"  /><input type="button" value="Save" onclick="saveEmployee('.$i.')" name="updateUsers" class="btn btn-info saveButton'.$i.' editField'.$i.'" style="display:none;" /></td>';
    $tr .= '<td><input type="button" value="Delete" class="btn btn-danger" onclick="deleteUser(\'tbl_users\', '.$f['id'].');" /></td>';
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
                            <h4 class="page-title text-primary">Management Users</h4>
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
                            <th scope="col">Password</th>
                            <th scope="col">Email</th>
<!--                            <th scope="col">Role</th>-->
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
                <form id="employeeAddForm" action="/queryAdmin.php" name="add_form" method="post" class="form form-horizontal"
                          enctype="multipart/form-data">
                        <div class="row" >
                            <input type="hidden" name="formAction" value="addEmployee" />

                            <div class="col-xs-4 col-md-4" style="margin-top:10px;text-align: center;">
                                <label for="add_name">Name</label>
                                <input type="text" class="form-control" id="add_name" name="add_name">
                            </div>
                            <div class="col-xs-4 col-md-4" style="margin-top:10px;text-align: center;">
                                <label for="add_email">Email</label>
                                <input type="text" class="form-control" id="add_email" name="add_email">
                            </div>
                            <div class="col-xs-4 col-md-4" style="margin-top:10px;text-align: center;">
                                <label for="add_password">Password</label>
                                <input type="password" class="form-control" id="add_password" name="add_password">
                            </div>

                        </div>
                        <div class="row" >
                            <div class="col-xs-12 col-md-12" style="margin-top:10px;margin-bottom:10px;text-align: right;">
                                <button type="button" name="addEmployee" id="addEmployee" class="btn btn-primary">Add
                                </button>
                            </div>
                        </div>

                    </form>
            </div>
            <?php include('includes/footer.php'); ?>
    </div>
    <?php include('includes/script.php'); ?>

    <script type="text/javascript">
    
    $(document).ready(function () {
            $('#addEmployee').click(function(){
                var name = $.trim($('[name=add_name]').val());
                if (name  === '') {
                    alert('name field is empty.');
                    return false;
                }

                var email = $.trim($('[name=add_email]').val());
                if (email  === '') {
                    alert('email field is empty.');
                    return false;
                }

                var role = $.trim($('[name=add_role]').val());
                if (role  === '0') {
                    alert('role field is empty.');
                    return false;
                }

                var password = $.trim($('[name=add_password]').val());
                if (password  === '') {
                    alert('password field is empty.');
                    return false;
                }
                $.ajax({
  type: 'POST',
  url: '/queryAdmin.php',
  data: $('form#employeeAddForm').serialize(),
  success: function() {
    window.location.reload();
  },
  error: function() {
    console.log("Signup was unsuccessful");
  }
});

                //addRow('employee');

            });

            $(".dashboard_bar").html("Manage Users");
        });
         (function($) {
			var table = $('#example2').DataTable({
				searching: true,
				paging:true,
				select: false,
				//info: false,         
				lengthChange:false 
				
			});
			
		})(jQuery);
        function edit(i) {
            $('.fieldTxt'+i).hide();
            $('.editFieldTxt'+i).show();
            $('.saveButton'+i).show();
            $('.editButton'+i).hide();
        }

        function saveEmployee(i) {

            var FD = new FormData(document.getElementById('saveEmployeeForm'+i));
            if (window.XMLHttpRequest) {
                xhrU = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                xhrU = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xhrU.open("POST", "queryAdmin.php");
            //xhrU.setRequestHeader("Content-Type" ,"application/x-www-form-urlencoded");
            xhrU.onreadystatechange = display_datas;
            xhrU.send(FD);
            function display_datas() {
                if (xhrU.readyState == 4) {
                    if (xhrU.status == 200) {
                        var e = JSON.parse(xhrU.responseText);
                        if(e[0] == 'success') {
                            alert("Successfully saved user");
                            //successMsg('Successfully updated info');
                            location.reload(true);
                            return;
                        }else {
                            alert("Error saving user");
                            //errorMsg('Error updating info');
                            return;
                        }
                    }  } }
            return;
        }

        function setSalary(e, i, cIDName) {
            if($(e).find(':selected').val() == '0') {  alert('please select role value'); $(e).val('1'); }
            return;
        }

        function deleteUser(table, id) {
            var url = 'method=deleteRow&table='+table+'&id='+id;
            fetchu(url, deleteRowResponse);
        }

        function fetchu(link, callback) {
            var xhr;
            showLoadingBar();
            if (window.XMLHttpRequest) {
                xhr = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");  }

            let data = link;
            xhr.open("POST", "queryAdmin.php", true);
            xhr.setRequestHeader("Content-Type" ,"application/x-www-form-urlencoded");
            xhr.onreadystatechange = display_data;
            xhr.send(data);

            function display_data() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        hideLoadingBar();
                        var e = JSON.parse(xhr.responseText);
                        if(e[0] == 'success') {
                            alert("Successfully deleted user");
                            //successMsg('Successfully updated info');
                            location.reload(true);
                            return;
                        }else {
                            alert("Error deleting user");
                            // errorMsg('Error updating info');
                            return;
                        }

                    }
                }
            }
        }
       

        
    </script>
    <?php include('includes/script-bottom.php'); ?>
</body>
</html>
