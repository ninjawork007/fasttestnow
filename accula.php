<?php
include('includes/head.php');
include('includes/css.php');
if(!hasPermission('history_report')){
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
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title text-primary">Accula Rt-PCR</h4>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="btn-block" style="padding: 10px;">
                        <a href="create_accula_report.php?type=3" class="btn btn-outline-primary waves-effect waves-light"><i class="fa fa-plus"></i> Add Accula</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table responsive mb-20 display" id="example" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col"></th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Birthday</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Passport</th>
                                    <th scope="col">Results</th>
                                    <th scope="col">Sample taken Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>
    <?php include('includes/script.php'); ?>
    <script type="text/javascript">
        function sendEmail(id) {
            showLoadingBar();
            $.ajax({
                type: "POST",
                url: 'queryAdmin.php',
                data: {
                    method: 'downloadPDF',
                    id: id
                },
                success: function(data) {
                    $.ajax({
                        type: "POST",
                        url: 'queryAdmin.php',
                        dataType: "json",
                        data: {
                            method: 'sendEmail',
                            id: id
                        },
                        success: function(data) {
                            if (data === true) {
                                hideLoadingBar();
                                Swal.fire("Sent an email successfully!")
                                //location.reload(true);
                            }
                        }

                    });
                }

            });

        }

        function deleteReport(id) {
            showLoadingBar();
            $.ajax({
                type: "POST",
                url: 'queryAdmin.php',
                data: {
                    method: 'deleteReport',
                    id: id
                },
                success: function(data) {
                    hideLoadingBar();
                    console.log('REPORT DELETE SUCCESS!!!' + data);
                    location.reload(true);
                }

            });

        }


        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: 'data.php',
                data: {
                    method: 'accula'
                },
                success: function(data) {
                    var tData = [];
                    console.log(data);
                    data = JSON.parse(data); // Parse the JSON string
                    for (let i = 0; i < data.length; i++) {
                        var obj = {};
                        const element = data[i];
                        obj.report_id = element.report_id;
                        obj.type_id = element.type_id;
                        obj.patient_firstname = element.patient_firstname;
                        obj.patient_lastname = element.patient_lastname;
                        obj.patient_phone = element.patient_phone;
                        obj.patient_email = element.patient_email;
                        obj.patient_birth = element.patient_birth;
                        obj.patient_gender = (element.patient_gender == 0) ? "Male" : "Female";
                        obj.patient_passport = element.patient_passport;
                        obj.report_results = (element.report_results == 0) ? "Negative" : "Positive";
                        obj.sample_taken = element.sample_taken;
                        obj.pdf_file_url = element.pdf_file_url;
                        obj.pdf_file_name = element.pdf_file_name;
                        obj.handled_at = element.handled_at;
                        obj.report_created_at = element.report_created_at;
                        obj.report_updated_at = element.report_updated_at;
                        obj.name = element.name;

                        tData.push(obj);
                    }
                    var t = $('#example').DataTable({
                        responsive: true,
                        "data": tData,
                        "columns": [{
                                "orderable": false,
                                "data": null,
                                "defaultContent": '',
                                "render": function(data, type, full, meta) {
                                    return (meta.row + 1);
                                }
                            },
                            {
                                "className": 'details-control',
                                "orderable": false,
                                "data": null,
                                "defaultContent": '',
                                "render": function(data, type, full, meta) {
                                    return '<div class="options btn-group">' +
                                        '<button type="button" class="btn tp-btn-light btn-primary dropdown-toggle" data-toggle="dropdown">' +
                                        '<i class="fa fa-cog" style="font-size:30px"></i>' +
                                        '</button>' +
                                        '<div class="dropdown-menu">' +
                                        '<a class="dropdown-item d-block" href="create_accula_report.php?id=' + data.report_id + '"><span  class="btn-icon-left text-primary"><i class="fa fa-edit fa-margin"></i></span> Edit </a>' +
                                        '<a class="dropdown-item d-block"  href="downloadPDF.php?id=' + data.report_id + '&report_type=D"><span class="btn-icon-left text-primary"><i class="fa fa-print fa-margin"></i></span> Download PDF </a>' +
                                        '<a class="dropdown-item text-danger" href="#" onclick="deleteReport(' + data.report_id + ')"><span class="btn-icon-left text-primary"><i class="mdi mdi-eraser"></i></span> Delete </a>' +
                                        '</div>';
                                },
                                // width:"15px"
                            },
                            {
                                "data": "patient_firstname"
                            },
                            {
                                "data": "patient_lastname"
                            },
                            {
                                "data": "patient_phone"
                            },
                            {
                                "data": "patient_email"
                            },
                            {
                                "data": "patient_birth"
                            },
                            {
                                "data": "patient_gender"
                            },
                            {
                                "data": "patient_passport"
                            },
                            {
                                "data": "report_results"
                            },
                            {
                                "data": "sample_taken"
                            }
                        ],

                    });
                }

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

            function addCatalog(table) {
                showLoadingBar();
                var FD = new FormData(document.getElementById(table + 'AddForm'));
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
                            hideLoadingBar();
                            var e = JSON.parse(xhrU.responseText);
                            if (e[0] == 'success') {
                                alert("Successfully added new catalog");
                                //successMsg('Successfully updated info');
                                location.reload(true);
                                return;
                            } else {
                                alert("Error adding new catalog");
                                // errorMsg('Error updating info');
                                return;
                            }
                        }
                    }
                }

            }
            $(".dashboard_bar").html("Report History");
        });
    </script>
    <?php include('includes/script-bottom.php'); ?>
</body>

</html>
