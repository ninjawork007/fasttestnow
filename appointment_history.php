<?php
include('includes/head.php');
include('includes/css.php');
if (!hasPermission('appointment_history')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
require("includes/loader.php");
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
                    <!-- <div class="col-xl-6">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title text-primary">Report History</h4>
                        </div>
                    </div>
                </div> -->
                    
                </div>
                <!-- end row -->
                <div class="row">

                    <div class="table-responsive">
                        <table class="table card-table display dataTablesCard" id="server-side-reports-table">
                            <thead>
                                <tr>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">City</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Birthday</th>
                                    <th scope="col">Passport</th>
                                    <th scope="col">Appointment</th>
                                    <th scope="col">Sample taken Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <form id="sendingData" action="userDetails.php" style="display: none;" method="POST">
        
                        <input type="hidden" class="form-control" name="res" id="res">
                    
                        <input type="submit" id="submitData">Send Data</button>
                </form>
                <?php include('includes/footer.php'); ?>
            </div>
            <?php include('includes/script.php'); ?>
            <script type="text/javascript">
                
                // show User details
                function showUserDetails(fName, lName, email) {
                    showLoadingBar();
                    $.ajax({
                        type: "POST",
                        url: 'getUser.php',
                        data: {
                            method: 'getUser',
                            firstName: fName,
                            lastName: lName,
                            email: email
                        },
                        success: function(data) {
                            hideLoadingBar();
                            var result = JSON.stringify(data);
                            $("#res").val(result);
                            $("#sendingData").submit();
                        }

                    });

                }
                $(document).ready(function() {
                    
                    $(".dashboard_bar").html("Appointment History");

                    $('#server-side-reports-table').DataTable({
                        "processing": true,
                        "language": {
                            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Processing...'
                        },
                        "serverSide": true,
                        "ajax": "appointments_db.php",
                        "order": [
                            [0, "desc"]
                        ],
                        "columnDefs": [{
                            "targets": [0],
                            "render": function(data, type, full, meta) {
                                const email = full[4];
                                const firstName = full[0];
                                const lastName = full[1];
                                return '<a href="#"  onclick="showUserDetails(`'+firstName+'`,`'+lastName+'`, `'+email+'`)"> ' + data + ' </a>';
                            }
                        }],
                    });
                    
                    $('[name=addCatalog]').click(function() {
                        var catalog_name = $.trim($('[name=add_name]').val());
                        if (catalog_name === '') {
                            alert('catalog name field is empty.');
                            return false;
                        }

                        var add_points = $.trim($('[name=add_points]').val());
                        console.log(add_points);
                        if (add_points === '') {
                            alert('points field is empty.');
                            return false;
                        }

                        addCatalog('catalog');

                    });
                });
            </script>
</body>

</html>