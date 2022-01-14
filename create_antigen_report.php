<?php
include('includes/head.php');
include('includes/css.php');
require("includes/function.php");
require("includes/loader.php");
include('includes/script.php');
?>
<?php
$con = dbCon();

$id = 0;
$action = 1;
//href from list in edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM tbl_report WHERE report_id=" . $id;

    if ($result = mysqli_query($con, $query)) {
        // Return the number of rows in result set
        $q = mysqli_query($con, $query);
        $r = mysqli_fetch_array_n($q, MYSQLI_ASSOC);
        $type = (int)$r['type_id'];
        $patient_firstname = $r['patient_firstname'];
        $patient_lastname = $r['patient_lastname'];
        $patient_phone = $r['patient_phone'];
        $patient_email = $r['patient_email'];
        $patient_birth = $r['patient_birth'];
        $patient_gender = (int)$r['patient_gender'];
        $patient_passport = $r['patient_passport'];
        $patient_test_brand = $r['antigen_test_brand'];
        $report_results = (int)$r['report_results'];
        $sample_taken = $r['sample_taken'];
        $action = 2;

        // Free result set
        mysqli_free_result($result);
    }

}
$all = 0;
if ($_SESSION['role'] == 1) {
    $all = 1;
}
if ($_SESSION['role'] == 1) {
    $all = 1;
}
if (isset($_GET['all'])) {
    $all = $_GET['all'];
}
//href from dashboard
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

date_default_timezone_set('US/Eastern');

$currenttime = date('D, d F Y h:i:s A');
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
                <div class="col-sm-6">
                    <div class="page-title-box">
                        <h4 class="page-title text-primary"><?php echo ($id == 0) ? "Create" : "Edit"; ?> Antigen
                            Results</h4>
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

                            <form action="" id="add_form" name="add_form" method="post" class="form form-horizontal"
                                  enctype="multipart/form-data">
                                <?php echo "<input type='hidden' name='ID' value=" . $id . " />"; ?>
                                <input type='hidden' name='type' value="2"/>
                                <input type='hidden' name='method' value="addReport"/>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label" for="type">
                                        Type :</label>
                                    <div class="col-md-9">
                                        Antigen
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label" for="sample_taken">
                                        Date & Time Test Taken :</label>
                                    <div class="col-md-9">
                                        <input name="sample_taken" id="sample_taken" size="16" type="text"
                                               value="<?php echo $sample_taken; ?>"
                                               class="form_datetime form-control" required autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label"
                                           for="patient_firstname">First Name<sup style="color: red;">*</sup>
                                        :</label>
                                    <div class="col-md-9">
                                        <input name="patient_firstname" id="patient_firstname" type="text"
                                               value="<?php echo $patient_firstname; ?>" placeholder="First Name"
                                               class="form-control " required/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label"
                                           for="patient_lastname">Last Name<sup style="color: red;">*</sup>
                                        :</label>
                                    <div class="col-md-9">
                                        <input name="patient_lastname" id="patient_lastname" type="text"
                                               value="<?php echo $patient_lastname; ?>" placeholder="Last Name"
                                               class="form-control " required/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label">
                                        Test Brand<sup style="color: red;">*</sup> :</label>
                                    <div class="col-md-9">
                                        <select name="antigen_test_brand" id="antigen_test_brand" class="form-control"
                                                required>
                                            <option value="">--Select Test Brand--</option>
                                            <option value="Abbot BinaxNOW POC Covid-19 Antigen Test" <?php echo ($patient_test_brand == "Abbot BinaxNOW POC Covid-19 Antigen Test") ? "selected" : ""; ?>>
                                                Abbot BinaxNOW POC Covid-19 Antigen Test
                                            </option>
                                            <option value="CareSTART POC Covid-19 Antigen Test" <?php echo ($patient_test_brand == "CareSTART POC Covid-19 Antigen Test") ? "selected" : ""; ?>>
                                                CareSTART POC Covid-19 Antigen Test
                                            </option>
                                            <option value="QuickVue POC Covid-19 SARS Antigen Test" <?php echo ($patient_test_brand == "QuickVue POC Covid-19 SARS Antigen Test") ? "selected" : ""; ?>>
                                                QuickVue POC Covid-19 SARS Antigen Test
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label" for="patient_phone">Phone
                                        :</label>
                                    <div class="col-md-9">
                                        <input name="patient_phone" id="patient_phone" type="tel"
                                               value="<?php echo $patient_phone; ?>"
                                               class="form-control "/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label"
                                           for="patient_email">Email<sup
                                                style="color: red;">*</sup> :</label>
                                    <div class="col-md-9">
                                        <input name="patient_email" id="patient_email" type="email"
                                               value="<?php echo $patient_email; ?>"
                                               class="form-control " required/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label" for="patient_birth">Date
                                        of Birth :</label>
                                    <div class="col-md-9">
                                        <input name="patient_birth" id="patient_birth" type="text"
                                               value="<?php echo $patient_birth; ?>" class="form-control" required
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label">
                                        Gender<sup style="color: red;">*</sup> :</label>
                                    <div class="col-md-9">
                                        <select name="patient_gender" id="patient_gender" class="form-control"
                                                required>
                                            <option value="">--Select Gender--</option>
                                            <option value="0" <?php echo ($patient_gender == 0) ? "selected" : ""; ?>>
                                                Male
                                            </option>
                                            <option value="1" <?php echo ($patient_gender == 1) ? "selected" : ""; ?>>
                                                Female
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label"
                                           for="patient_passport">
                                        Passport Number:</label>
                                    <div class="col-md-9">
                                        <input name="patient_passport" id="patient_passport" type="text"
                                               value="<?php echo $patient_passport; ?>"
                                               class="form-control "/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label form-control-label" for="report_results">
                                        Results<sup style="color: red;">*</sup> :</label>
                                    <div class="col-md-9">
                                        <select name="report_results" id="report_results" class="form-control"
                                                required>
                                            <option value="">--Select Results--</option>
                                            <option value="0" <?php echo ($report_results == 0) ? "selected" : ""; ?>>
                                                Negative
                                            </option>
                                            <option value="1" <?php echo ($report_results == 1) ? "selected" : ""; ?>>
                                                Positive
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary btn-lg waves-effect waves-light"
                                                type="submit" name="submit"
                                                style="position: absolute; right: 0;">Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
</div>
<script>
    var action = '<?php echo $action; ?>';
    jQuery(document).ready(function () {
        var currentTime = '<?php echo $currenttime; ?>';
        if (action == 1) {
            $("#sample_taken").val(currentTime);
        }
        $('#single-select').select2({
            placeholder: "Select client",
            ajax: {
                url: "queryAdmin.php",
                type: "post",
                dataType: 'json',
                data: function (params) {
                    return {
                        method: 'searchClient',
                        searchTerm: params.term
                    };
                }, processResults: function (response) {
                    return {results: response};
                }, cache: true
            }
        });
        $('#single-select').on('select2:select', function (e) {
            var data = e.params.data;
            $.ajax({
                type: "POST",
                url: 'queryAdmin.php',
                data: {method: 'getClientAppointmentInfo', id: data.id},
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#patient_firstname').val(data.firstName);
                    $('#patient_lastname').val(data.lastName);
                    $('#patient_phone').val(data.phone);
                    $('#patient_email').val(data.email);
                    $('#patient_birth').val(data.dob);
                    $('#patient_gender').val((data.gender === 'Female') ? 1 : 0);
                    // $('#patient_passport').val(data.passport_no);
                    if (data.sample_collected_date == null || data.sample_collected_date == '') {
                        $("#sample_taken").val(currentTime);
                    } else {
                        $('#sample_taken').val(data.sample_collected_date);
                    }

                    var genderVal = (data.gender === 'Female') ? 1 : 0;
                    $("#patient_gender option").each(function (i) {
                        var $this = $("#patient_gender option").eq(i)
                        if ($this.val() == genderVal) {
                            $this.css("selected", "selected");
                        }
                    })
                }
            });
        });
        $("#sample_taken").datetimepicker({format: 'D, dd MM yyyy HH:ii:ss P', autoclose: true});
        $('#patient_birth').datepicker({
            format: "mm/dd/yyyy",
            autoclose: true
        });

        $(".dashboard_bar").html("Create Report");
    });
    var all = '<?php echo $all; ?>';
    $("#add_form").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.
        showLoadingBar();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: 'queryAdmin.php',
            data: form.serialize(), // serializes the form's elements.
            dataType: "json",
            success: function (data) {
                if (data.result === true) {
                    $.ajax({
                        type: "POST",
                        url: 'queryAdmin.php',
                        data: {method: 'downloadPDF', id: data.id, action: action},
                        dataType: "json",
                        success: function (data) {
                            if (data === true) {
                                hideLoadingBar();
                                if (all == 0) {
                                    window.location.href = 'antigen.php';
                                } else {
                                    window.location.href = 'report_history.php';
                                }
                            }
                        }
                    });
                }
            }

        });
    });
</script>
<?php include('includes/script-bottom.php'); ?>
</body>
</html>
