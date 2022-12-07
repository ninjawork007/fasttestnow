<?php
include("../../global/variables.php");
include(__ROOT .'/includes/head.php');
include(__ROOT .'/includes/css.php');
require(__ROOT ."/includes/language.php");
?>


<body>
    <div id="wrapper">
        <?php
        include(__ROOT .'/includes/topbar.php');
        include(__ROOT .'/includes/sidebar.php');
        ?>
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Clients</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Health</a></li>
                                    <li class="breadcrumb-item active">All Clients</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                    <div class="row" style="overflow: auto;background: #584a4a;">
                      

                        <table class="table mb-20 display" id="example" style="width:100%">
                            <thead> 
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Notes</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <?php include(__ROOT .'/includes/footer.php'); ?>
            </div>
        </div>
        <?php include(__ROOT .'/includes/script.php'); ?>
        <script src="<?php echo _HOST_LINK; ?>/assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script type="text/javascript">


            $(document).ready(function() {
                $.ajax({
                    type: "POST",
                    url: 'data.php',
                    data: {
                        method: 'clients'
                    },
                    success: function(data) {
                        var tData = [];
                        data = JSON.parse(data);
                        console.log(data); // Parse the JSON string
                        // for (let i = 0; i < data.length; i++) {
                        //     var obj = {};
                        //     const element = data[i];
                        //     obj.id = element.i;
                        //     obj.firstName = element.firstName;
                        //     obj.lastName = element.lastName;
                        //     obj.phone = element.phone;
                        //     obj.notes = element.notes;
                        //     tData.push(obj);
                        // }
                        var t = $('#example').DataTable({
                            "data": data,
                            "columns": [{
                                    "orderable": false,
                                    "data": null,
                                    "defaultContent": '',
                                    "render": function(data, type, full, meta) {
                                        return (meta.row + 1);
                                    }
                                },
                                {
                                    "data": "firstName"
                                },
                                {
                                    "data": "lastName"
                                },
                                {
                                    "data": "phone"
                                },
                                {
                                    "data": "notes"
                                },
                               
                                {
                                    "className": 'details-control',
                                    "orderable": false,
                                    "data": null,
                                    "defaultContent": '',
                                    "render": function(data, type, full, meta) {
                                        return '<a class="btn btn-primary btn-sm" href="javascript:void(0)"><i class="fa fa-calendar"></i> Appointment </a>';
                                    },
                                    // width:"15px"
                                }
                            ],

                        });
                    }

                })
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
        <?php include(__ROOT .'/includes/script-bottom.php'); ?>
</body>

</html>