<?php
include("../../global/variables.php");

include(__ROOT . '/includes/head.php');
include(__ROOT . '/includes/css.php');
if (!hasPermission('rx_order_history')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
require(__ROOT . "/includes/loader.php");
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
                    <!-- <div class="col-xl-6">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title text-primary">Report History</h4>
                        </div>
                    </div>
                </div> -->
                <div class="btn-block" style="padding: 10px;">
                        <a href="<?php echo _HOST_LINK; ?>/view/rx_order/create.php?type=6" class="btn btn-outline-primary waves-effect waves-light"><i class="fa fa-plus"></i> Add Rx Order Test</a>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">

                    <div class="table-responsive">
                        <table class="table card-table display dataTablesCard" id="server-side-reports-table">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Shipping Service</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Semaglutide</th>
                                    <th scope="col">Quantity </th>
                                    <th scope="col">Insulin Qty</th>
                                    <th scope="col">Notes</th>
                                    <th scope="col">Handled at</th>
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
                <?php include(__ROOT . '/includes/footer.php'); ?>
            </div>
            <?php include(__ROOT . '/includes/script.php'); ?>
            <script type="text/javascript">
                function deleteReport(id) {
                    showLoadingBar();
                    $.ajax({
                        type: "POST",
                        url: './controller/rxPrescription.php',
                        data: {
                            method: 'deleteRxOrder',
                            id: id
                        },
                        success: function(data) {
                            hideLoadingBar();
                            console.log('REPORT DELETE SUCCESS!!!' + data);
                            location.reload(true);
                        }

                    });

                }

                function sendEmail(id) {
                    showLoadingBar();
                    $.ajax({
                        type: "POST",
                        url: $host + '/controller/rxPrescription.php',
                        data: {
                            method: 'generatePDF',
                            id: id
                        },
                        success: function(data) {
                            if (data === true) {
                                hideLoadingBar();
                                console.log('send email!!!' + data);
                                //location.reload(true);
                            }
                        }
                    });
                }
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
                    
                    $(".dashboard_bar").html("Report History");

                    $('#server-side-reports-table').DataTable({
                        "processing": true,
                        "language": {
                            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Processing...'
                        },
                        "serverSide": true,
                        "ajax": $host + "/datatables/rxPrescription.php",
                        "order": [
                            [0, "desc"]
                        ],
                        "columnDefs": [{
                            "targets": [0],
                            "render": function(data, type, full, meta) {
                                console.log(full);
                                return '<div class="options btn-group">' +
                                    '<button type="button" class="btn tp-btn-light btn-primary dropdown-toggle" data-toggle="dropdown">' +
                                    '<i class="fa fa-cog" style="font-size:30px"></i>' +
                                    '</button>' +
                                    '<div class="dropdown-menu">' +
                                    //'<a class="dropdown-item d-block" href="' + data + '&all=1"><span  class="btn-icon-left text-primary"><i class="fa fa-edit fa-margin"></i></span> Edit </a>' +
                                    '<a class="dropdown-item d-block" target="_blank"><span class="btn-icon-left text-primary"><i class="fa fa-print fa-margin"></i></span> Download PDF </a>' +
                                    '<a class="dropdown-item text-danger" href="#" onclick="deleteReport(' + data + ')"><span class="btn-icon-left text-primary"><i class="mdi mdi-eraser"></i></span> Delete </a>' +
                                    '</div>';
                            }
                        },{
                            "targets": [1],
                            "render": function(data, type, full, meta) {
                                const firstName = data;
                                const lastName = full[11];
                                return firstName + " " + lastName;
                            }
                        },{
                            "targets": [2],
                            "render": function(data, type, full, meta) {
                                const service = (data == "patient")? "Send to patient": "Send to office";
                                return service;
                            }
                        },{
                            "targets": [5],
                            "render": function(data, type, full, meta) {
                                const semaglutide = (data == 0)? "No": "Yes";
                                return semaglutide;
                            }
                        }],
                    });
                   
                });
            </script>
</body>

</html>

