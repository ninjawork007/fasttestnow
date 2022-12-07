<?php
include("../../global/variables.php");

include(__ROOT . '/includes/head.php');
include(__ROOT . '/includes/css.php');
require(__ROOT . "/includes/function.php");
require(__ROOT . "/includes/loader.php");
include(__ROOT . '/includes/script.php');
if (!hasPermission('create_rx_order')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
?>
<?php
    $id = 0;
    $action = "insert";
    $sendType = "insertRxOrder";
    
         
    if (isset($_GET['id'])) {
        $sendType = "updateRxOrder";
        $action = "update";
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
                    <div class="col-sm-6">
                        <div class="page-title-box">
                            <h4 class="page-title text-primary"><?php echo ($id == 0) ? "Create" : "Edit"; ?> 
                                COMPOUNDED semaglutide RX ORDER
                            </h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="col-md-12">
                                <select id="single-select" name="appointment_id"></select>
                                <span class="fs-12 text-primary">You can get the client data by search</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card m-b-20">
                            <div class="card-body" style="padding:50px">
                                <form action="" id="add_form" name="add_form" method="post" class="form form-horizontal" enctype="multipart/form-data">
                                    <?php echo "<input type='hidden' name='ID' value=" . $id . " />"; ?>
                                    <?php echo "<input type='hidden' name='method' value=" . $sendType . " />"; ?>
                                    <input type='hidden' name='type' value="6" />
                                    <input type='hidden' name='appointment_id' id='appointment_id'/>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Shipping Service<sup style="color: red;">*</sup></label><br/>
                                            <label class="radio-inline mr-2"><input type="radio" name="shipping_service" id="sendToOffice" value="office"> Send to office</label>
                                            <label class="radio-inline mr-2"><input type="radio" name="shipping_service" id="sendToPatient" checked value="patient"> Send to patient</label>
                                        </div>
                                    </div>
                                    <div class="form-row mt-5">
                                        <label class="font-weight-bold">Patient Name <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        
                                        <div class="form-group col-md-4 row col-auto">
                                            <div class="col-sm-7">
                                                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-5 my-auto">First Name</label>
                                        </div>
                                        <div class="form-group col-md-4 row">
                                            <div class="col-sm-7">
                                                <input type="text" name="lastname" id="lastname" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-5 my-auto">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="form-row mt-5">
                                        <label class="font-weight-bold">Patient Email <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-8 row">
                                            <div class="col-sm-7">
                                                <input type="email" name="email" id="email" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-5 my-auto">example@example.com</label>
                                        </div>
                                    </div>
                                    <div class="form-row mt-5">
                                        <label class="font-weight-bold">Patient Address <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-5 row">
                                            <div class="col-sm-6">
                                                <input type="text" name="address" id="address" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-6 my-auto">Street Address</label>
                                        </div>
                                        <div class="form-group col-md-7 row">
                                            <div class="col-sm-6">
                                                <input type="text" name="address2" id="address2" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-6 my-auto">Street Address Line 2</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4 row">
                                            <div class="col-sm-8">
                                                <input type="text" name="city" id="city" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-4 my-auto">City </label>
                                        </div>
                                        <div class="form-group col-md-3 row">
                                            <div class="col-sm-6">
                                                <input type="text" name="state" id="state" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-6 my-auto">State / Province</label>
                                        </div>
                                        <div class="form-group col-md-4 row">
                                            <div class="col-sm-6">
                                                <input type="number" name="zipcode" id="zipcode" class="form-control" placeholder="">
                                            </div>
                                            <label class="col-sm-6 my-auto">Postal / Zip Code</label>
                                        </div>
                                    </div>
                                    <div class="form-row mt-5">
                                        <label class="font-weight-bold">Patient Date Of Birth <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 row">
                                            <div class="col-sm-3">
                                                <input type="text" name="dob" id="dob" class="form-control"  class="form_datetime form-control" required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row mt-5">
                                        <label class="font-weight-bold">Patient Phone Number <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 row">
                                            <div class="col-sm-3">
                                                <input type="tel" name="phone" id="phone" class="form-control" placeholder="(000) 000-0000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row mt-5">
                                        <label class="font-weight-bold">Description </label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-10 row">
                                            <div class="form-check">
                                                <input type="hidden"  name="semaglutide_check" value="0">
                                                <input class="form-check-input" type="checkbox"  name="semaglutide_check" id="semaglutide_check" value="1">
                                                <label class="form-check-label">
                                                    Semaglutide 2.5mg / L-Carnitine Injection, 1mL MDV (Semaglutide Sodium 2.5mg/mL, L-Carnitine 25mg/mL) SIG: Inject 0.1 mL once a week for 4 weeks, then 0.2mL for 3 weeks.  Semaglutide 5mg / L-Carnitine Injection, 2mL MDV (Semaglutide Sodium 5mg/mL, L-Carnitine 25mg/mL) SIG: Inject 0.2 mL for 4 weeks, then 0.4mL for remainder of vial
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="font-weight-bold">Quantity <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 row">
                                            <div class="col-sm-3">
                                                <input type="number" class="form-control" id="quantity" name="quantity" value="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="font-weight-bold">Insulin syringes 31G, 5/16, 1 ml Qty: <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 row">
                                            <div class="col-sm-3">
                                                <input type="number" class="form-control" id="insulin_qty" name="insulin_qty" placeholder="ex: 23">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="font-weight-bold">Notes <sup style="color: red;">*</sup></label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 row">
                                            <div class="col-sm-12">
                                                <textarea type="number" class="form-control" id="notes" name="notes" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Rx Order</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include(__ROOT . '/includes/footer.php'); ?>
        </div>
    </div>
    <script>
        var action = '<?php echo $action; ?>';
        var all = '<?php echo $all; ?>';
        jQuery(document).ready(function() {

            // get dropdown menu for appointmented users
            $('#single-select').select2({
                placeholder: "Select client",
                ajax: {
                    url: $host + "/model/queryAdmin.php",
                    type: "post",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            method: 'searchClient',
                            searchTerm: params.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            // add values to the form
            $('#single-select').on('select2:select', function(e) {
                    var data = e.params.data;
                    $.ajax({
                        type: "POST",
                        url: $host + '/model/queryAdmin.php',
                        data: {
                            method: 'getClientAppointmentInfo',
                            id: data.id
                        },
                        dataType: "json",
                        success: function(data) {
                            // console.log(data);
                            $('#firstname').val(data.firstName);
                            $('#lastname').val(data.lastName);
                            $('#phone').val(data.phone);
                            $('#email').val(data.email);
                            $('#address').val(data.address);
                            $('#address2').val(data.address2);
                            $('#city').val(data.city);
                            $('#state').val(data.state);
                            $('#zipcode').val(data.zipcode);
                            $('#dob').val(data.dob);
                            $('#appointment_id').val(data.id);
                        },

                    });
            });
            // set the date of birth or customer
            $('#dob').datepicker({
                format: "mm/dd/yyyy",
                autoclose: true
            });
        })
        // submit the form data for Rx Order
            
            $("#add_form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                showLoadingBar();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    url: $host + '/controller/rxPrescription.php',
                    data: form.serialize(), // serializes the form's elements.
                    dataType: "json",
                    success: function(data) {
                        if (data.result === true) {
                            $.ajax({
                                type: "POST",
                                url: $host + '/controller/rxPrescription.php',
                                data: {
                                    method: 'generatePDF',
                                    id: data.id,
                                    action: action
                                },
                                dataType: "json",
                                success: function(data) {
                                    if (data === true) {
                                        hideLoadingBar();
                                        window.location.reload();
                                    }
                                }
                            });
                        }
                    }

                });
            });
    </script>
    <?php include(__ROOT . '/includes/script-bottom.php'); ?>
</body>
</html>