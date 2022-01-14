<?php
include('includes/head.php');
include('includes/css.php');
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
                <div class="btn-block" style="padding: 10px;">
                    <div class="options btn-group">
                        <button type="button" class="btn btn-rounded btn-outline-primary dropdown-toggle"
                                data-toggle="dropdown"><span class="btn-icon-left text-primary"><i
                                        class="fa fa-plus"></i></span> Add Report
                        </button>
                        <div class="dropdown-menu">
                            <a href="create_pcr_report.php?type=1&all=1"
                               class="dropdown-item btn btn-rounded btn-primary"><span
                                        class="btn-icon-left text-primary"><i class="fa fa-plus"></i>
                                        </span>Visby PCR</a>
                            <a href="create_antigen_report.php?type=2&all=1"
                               class="dropdown-item btn btn-rounded btn-primary"><span
                                        class="btn-icon-left text-primary"><i class="fa fa-plus"></i>
                                        </span>Antigen</a>
                            <a href="create_accula_report.php?type=3&all=1"
                               class="dropdown-item btn btn-rounded btn-primary"><span
                                        class="btn-icon-left text-primary"><i class="fa fa-plus"></i>
                                        </span>Accula</a>
                            <a href="create_antibody_report.php?type=4&all=1"
                               class="dropdown-item btn btn-rounded btn-primary"><span
                                        class="btn-icon-left text-primary"><i class="fa fa-plus"></i>
                                        </span>Antibody</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row">

                <div class="table-responsive">
                    <table class="table card-table display dataTablesCard" id="server-side-reports-table">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Email</th>
                            <th scope="col">Birthday</th>
                            <th scope="col">Passport</th>
                            <th scope="col">Results</th>
                            <th scope="col">Sample taken Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/script.php'); ?>
        <script type="text/javascript">
            function deleteReport(id) {
                showLoadingBar();
                $.ajax({
                    type: "POST",
                    url: 'queryAdmin.php',
                    data: {
                        method: 'deleteReport',
                        id: id
                    },
                    success: function (data) {
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
                    url: 'queryAdmin.php',
                    data: {
                        method: 'downloadPDF',
                        id: id
                    },
                    success: function (data) {
                        $.ajax({
                            type: "POST",
                            url: 'queryAdmin.php',
                            data: {
                                method: 'sendEmail',
                                id: id
                            },
                            success: function (data) {
                                hideLoadingBar();
                                console.log('send email!!!' + data);
                                //location.reload(true);
                            }

                        });
                    }

                });

            }

            $(document).ready(function () {
                $(".dashboard_bar").html("Report History");
                $('#server-side-reports-table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": "serverside.php",
                    "columnDefs": [
                        {
                        "targets": [0],
                        "render": function (data, type, full, meta) {
                            var typeID=parseInt(full[10]);
                            var url = '';
                            if (typeID === 1) {
                                url = 'create_pcr_report.php?id=' + data;
                            }
                            if (typeID === 2) {
                                url = 'create_antigen_report.php?id=' + data;
                            }
                            if (typeID === 3) {
                                url = 'create_accula_report.php?id=' + data;
                            }
                            if (typeID === 4) {
                                    url = 'create_antibody_report.php?id=' + data;
                            }
                            return '<div class="options btn-group">' +
                                '<button type="button" class="btn tp-btn-light btn-primary dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="fa fa-cog" style="font-size:30px"></i>' +
                                '</button>' +
                                '<div class="dropdown-menu">' +
                                '<a class="dropdown-item d-block" href="' + url + '&all=1"><span  class="btn-icon-left text-primary"><i class="fa fa-edit fa-margin"></i></span> Edit </a>' +
                                '<a class="dropdown-item d-block" href="downloadPDF.php?id=' + data + '&report_type=D"><span class="btn-icon-left text-primary"><i class="fa fa-print fa-margin"></i></span> Download PDF </a>' +
                                '<a class="dropdown-item text-danger" href="#" onclick="deleteReport(' + data + ')"><span class="btn-icon-left text-primary"><i class="mdi mdi-eraser"></i></span> Delete </a>' +
                                '</div>';
                        }
                    }],
                });
                $.ajax({
                    type: "POST",
                    url: 'data.php',
                    data: {
                        method: 'all'
                    },
                    success: function (data) {
                        var tData = [];

                        data = JSON.parse(data); // Parse the JSON string
                        for (let i = 0; i < data.length; i++) {
                            var obj = {};
                            const element = data[i];
                            obj.report_id = element.report_id;
                            if (element.type_id == 1) {
                                obj.type = "Visby PCR"
                            }
                            if (element.type_id == 2) {
                                obj.type = "Antigen"
                            }
                            if (element.type_id == 3) {
                                obj.type = "Accula"
                            }
                            if (element.type_id == 4) {
                                obj.type = "Antibody"
                            }
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
                            obj.handled_at = element.handled_at;
                            obj.pdf_file_url = element.pdf_file_url;
                            obj.pdf_file_name = element.pdf_file_name;
                            obj.report_created_at = element.report_created_at;
                            obj.report_updated_at = element.report_updated_at;
                            obj.name = element.name;
                            // if(element.report_id==45){
                            //     console.log(obj);
                            // }
                            tData.push(obj);
                        }
                        var t = $('#example').DataTable({
                            "data": tData,
                            "columns": [{
                                "orderable": false,
                                "data": null,
                                "defaultContent": '',
                                "render": function (data, type, full, meta) {
                                    return (meta.row + 1);
                                }
                            },
                                {
                                    "className": 'details-control',
                                    "orderable": false,
                                    "data": null,
                                    "defaultContent": '',
                                    "render": function (data, type, full, meta) {
                                        var url = '';
                                        if (data.type_id == 1) {
                                            url = 'create_pcr_report.php?id=' + data.report_id;
                                        }
                                        if (data.type_id == 2) {
                                            url = 'create_antigen_report.php?id=' + data.report_id;
                                        }
                                        if (data.type_id == 3) {
                                            url = 'create_accula_report.php?id=' + data.report_id;
                                        }
                                        if (data.type_id == 4) {
                                            url = 'create_antibody_report.php?id=' + data.report_id;
                                        }
                                        return '<div class="options btn-group">' +
                                            '<button type="button" class="btn tp-btn-light btn-primary dropdown-toggle" data-toggle="dropdown">' +
                                            '<i class="fa fa-cog" style="font-size:30px"></i>' +
                                            '</button>' +
                                            '<div class="dropdown-menu">' +
                                            '<a class="dropdown-item d-block" href="' + url + '&all=1"><span  class="btn-icon-left text-primary"><i class="fa fa-edit fa-margin"></i></span> Edit </a>' +
                                            '<a class="dropdown-item d-block" href="downloadPDF.php?id=' + data.report_id + '&report_type=D"><span class="btn-icon-left text-primary"><i class="fa fa-print fa-margin"></i></span> Download PDF </a>' +
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
                                    "data": "type"
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

                })
                $('[name=addCatalog]').click(function () {
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
