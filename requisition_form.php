<?php
include('includes/head.php');
include('includes/report-css.php');
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

                <div class="table-responsive">
                    <table class="table card-table display dataTablesCard" id="server-side-reports-table">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">DOB</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Ethnicity</th>
                            <th scope="col">Address</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Email</th>
                            <th scope="col">Patient ID</th>
                            <th scope="col">Sample Type</th>
                            <th scope="col">Sample Collection Date</th>
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
                $(".dashboard_bar").html("Requisition Forms");
                $('#server-side-reports-table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": "request_form.php",
                    "columnDefs": [
                        {
                        "targets": [0],
                        "render": function (data, type, full, meta) {
                            var typeID=parseInt(full[10]);
                            return '<div class="options btn-group">' +
                                '<button type="button" class="btn tp-btn-light btn-primary dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="fa fa-cog" style="font-size:30px"></i>' +
                                '</button>' +
                                '<div class="dropdown-menu">' +
                                '<a class="dropdown-item d-block p-2" href="download_form.php?id=' + data +'&patient_id='+full[9]+'&report_type=D"><span class="btn-icon-left text-primary"><i class="fa fa-print fa-margin"></i></span> Download Form </a>' + 
                                '</div>';
                        },
                    },
                     {
                        "targets": [10],
                        "render": function (data, type, full, meta) {
                            var typeID=parseInt(full[10]);
                            return 'Nasopharyngeal';
                        },}
                    ],
                });
                $.ajax({
                    type: "POST",
                    url: 'data.php',
                    data: {
                        method: 'requisition_form'
                    },
                    success: function (data) {
                        var tData = [];

                        data = JSON.parse(data); // Parse the JSON string
                        for (let i = 0; i < data.length; i++) {
                            var obj = {};
                            const element = data[i];
                            obj.report_id = element.id;
                            obj.type_id = element.type_id;
                            obj.patient_firstname = element.firstname;
                            obj.patient_lastname = element.lastname;
                            obj.patient_phone = element.phone;
                            obj.patient_email = element.email;
                            obj.patient_birth = element.dob;
                            obj.patient_gender = (element.gender == 0) ? "Male" : "Female";
                            obj.patient_passport = element.passport_no;
                            obj.sample_taken = element.sample_collected_date_formatted;
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
