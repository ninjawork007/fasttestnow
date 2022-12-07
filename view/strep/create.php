<?php
include("../../global/variables.php");
include(__ROOT . '/includes/head.php');
include(__ROOT . '/includes/css.php');
require(__ROOT . "/includes/function.php");
require(__ROOT . "/includes/loader.php");
include(__ROOT . '/includes/script.php');
if (!hasPermission('create_mono_test')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}
?>
<?php
$con = dbCon();

$id = 0;
$action = "insert";
$sendType = "insert";
//href from list in edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM tbl_strep WHERE id=" . $id;

    if ($result = mysqli_query($con, $query)) {
        // Return the number of rows in result set
        $q = mysqli_query($con, $query);
        $r = mysqli_fetch_array_n($q, MYSQLI_ASSOC);
        $type = (int)$r['id'];
        $appointment_id = $r['appointment_id'];
        $firstname = $r['firstname'];
        $lastname = $r['lastname'];
        $email = $r['email'];
        $gender = $r['gender'];
        $dob = $r['dob'];
        $results = (int)$r['results'];
        $released = $r['handled_at'];
        // status variables
        $sendType = "update";
        $action = "update";
        // Free result set
        mysqli_free_result($result);
    }
}

$all = 0;
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
        include(__ROOT . '/includes/topbar.php');
        include(__ROOT . '/includes/sidebar.php');
        ?>
        <div class="content-body">
            <!-- Start content -->
            <div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="page-title-box">
                                <h4 class="page-title text-primary"><?php echo ($id == 0) ? "Create" : "Edit"; ?> Strep Screening Test Results</h4>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <select id="single-select"></select>
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
                                        <input type='hidden' name='type' value="8" />
                                        <input type='hidden' name='appointment_id' id='appointment_id' value="<?php echo $appointment_id; ?>"/>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="type">
                                                Type :</label>
                                            <div class="col-md-9">
                                                Strep Screening
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="handled_at">
                                                Date & Time Released <sup style="color: red;">*</sup> :</label>
                                            <div class="col-md-9">
                                                <input name="handled_at" id="handled_at" size="16" type="text" value="<?php echo $released; ?>" class="form-control" required autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="firstname">First Name<sup style="color: red;">*</sup>
                                                :</label>
                                            <div class="col-md-9">
                                                <input name="firstname" id="firstname" type="text" value="<?php echo $firstname; ?>" placeholder="First Name" class="form-control " required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="lastname">Last Name<sup style="color: red;">*</sup>
                                                :</label>
                                            <div class="col-md-9">
                                                <input name="lastname" id="lastname" type="text" value="<?php echo $lastname; ?>" placeholder="Last Name" class="form-control " required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="email">Email<sup style="color: red;">*</sup> :</label>
                                            <div class="col-md-9">
                                                <input name="email" id="email" type="email" value="<?php echo $email; ?>" class="form-control " required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="dob">Date
                                                of Birth :</label>
                                            <div class="col-md-9">
                                                <input name="dob" id="dob" type="text" value="<?php echo $dob; ?>" class="form-control" required autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label">
                                                Gender<sup style="color: red;">*</sup> :</label>
                                            <div class="col-md-9">
                                                <select name="gender" id="gender" class="form-control" required>
                                                    <option value="">--Select Gender--</option>
                                                    <option value="0" <?php echo ($gender == 0) ? "selected" : ""; ?>>
                                                        Male
                                                    </option>
                                                    <option value="1" <?php echo ($gender == 1) ? "selected" : ""; ?>>
                                                        Female
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label form-control-label" for="results">
                                                Results<sup style="color: red;">*</sup> :</label>
                                            <div class="col-md-9">
                                                <select name="results" id="results" class="form-control" required>
                                                    <option value="">--Select Results--</option>
                                                    <option value="0" <?php echo ($results == 0) ? "selected" : ""; ?>>
                                                        Negative
                                                    </option>
                                                    <option value="1" <?php echo ($results == 1) ? "selected" : ""; ?>>
                                                        Positive
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <button class="btn btn-primary btn-lg waves-effect waves-light" type="submit" name="submit" style="position: absolute; right: 0;">Save
                                                </button>
                                            </div>
                                        </div>
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
            jQuery(document).ready(function() {
                var currentTime = '<?php echo $currenttime; ?>';
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
                            $('#patient_phone').val(data.phone);
                            $('#email').val(data.email);
                            $('#dob').val(data.dob);
                            $('#appointment_id').val(data.id);
                            $('#gender').val((data.gender === 'Female') ? 1 : 0);
                            var genderVal = (data.gender === 'Female') ? 1 : 0;
                            $("#gender option").each(function(i) {
                                var $this = $("#gender option").eq(i)
                                if ($this.val() == genderVal) {
                                    $this.css("selected", "selected");
                                }
                            })
                        },

                    });
                });
                
                $("#handled_at").datetimepicker({
                    format: 'D, dd MM yyyy HH:ii:ss P',
                    autoclose: true
                });
                $('#dob').datepicker({
                    format: "mm/dd/yyyy",
                    autoclose: true
                });
                $(".dashboard_bar").html("Create Report");
            });
            var action = '<?php echo $action; ?>';
            var currentTime = '<?php echo $currenttime; ?>';
            if (action == 'insert') {
                $("#handled_at").val(currentTime);
            }
            var all = '<?php echo $all; ?>';
            $("#add_form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                showLoadingBar();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    url: $host + '/controller/strep.php',
                    data: form.serialize(), // serializes the form's elements.
                    dataType: "json",
                    success: function(data) {
                        if (data.result === true) {
                            $.ajax({
                                type: "POST",
                                url: $host + '/controller/strep.php',
                                data: {
                                    method: 'generatePDF',
                                    id: data.id,
                                    action: action
                                },
                                dataType: "json",
                                success: function(data) {
                                    if (data === true) {
                                        hideLoadingBar();
                                        if (all == 0) {
                                            window.location.href = $host + '/view/strep/history.php';
                                        } else {
                                            window.location.href = $host + '/view/strep/history.php';
                                        }
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