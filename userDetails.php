<?php
include('includes/head.php');
include('includes/css.php');
if (!hasPermission('report_history')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}

?>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php require("includes/loader.php"); ?>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Header / Sidebar start
        ***********************************-->
        <?php
        include('includes/topbar.php');
        include('includes/sidebar.php');

        date_default_timezone_set('US/Eastern');
        $currentdate = date('m/d/Y');

        // parse URL parameter
        $result = json_decode($_GET['result']);
        $getInfo = json_decode($result, true);
        
        
        $generatedReports = $getInfo['GeneratedReports'];
        $userInfo = $getInfo['ToBeGeneratedReports'];

        $name = $userInfo['name'];
        $email = $userInfo['email'];
        $phone = $userInfo['phone'];
        $birthday = $userInfo['dob'];
        $gender = $userInfo['gender'];
        $ethnicity = $userInfo['ethnicity'];
        $height = $userInfo['Height'];
        $weight = $userInfo['Weight'];
        $BMI = $userInfo['BMI'];
        $address = $userInfo['address'];
        $country = $userInfo['passport_country'];
        $city = $userInfo['city'];
        $zipcode = $userInfo['zipcode'];
        $passport = $userInfo['passport_no'];
        
		?>
        <!--**********************************
            Header / Sidebar end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
				<!-- Add Order -->
				<div class="row">
                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-header"><strong> <?php echo $name; ?></strong> <strong><?php echo $currentdate;?></strong> <span class="float-right">
                            </div>
                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="mt-4 col-xl-3 col-lg-3 col-md-6 col-sm-12">
                                        <h6></h6>
                                        <div><strong>Email </strong> <?php echo $email; ?></div>
                                        <div><strong>Phone </strong> <?php echo $phone; ?></div>
                                        <div><strong>Birthday </strong> <?php echo $birthday; ?></div>
                                        <div><strong>Gender </strong> <?php echo $gender; ?></div>
                                        <div><strong>Ethnicity </strong> <?php echo $ethnicity; ?></div>
                                        <div><strong>Height </strong> <?php echo $height; ?></div>
                                        <div><strong>Weight </strong> <?php echo $weight; ?></div>
                                        <div><strong>BMI </strong> <?php echo $BMI; ?></div>
                                    </div>
                                    <div class="mt-4 col-xl-3 col-lg-3 col-md-6 col-sm-12">
                                        <h6></h6>
                                        <div><strong>Address </strong> <?php echo $address; ?></div>
                                        <div><strong>Country </strong> <?php echo $country; ?></div>
                                        <div><strong>City </strong> <?php echo $city; ?></div>
                                        <div><strong>Zipcode </strong> <?php echo $zipcode; ?></div>
                                        <div><strong>Passport </strong> <?php echo $passport; ?></div>
                                    </div>
                                    <div class="mt-4 col-xl-6 col-lg-6 col-md-12 col-sm-12 d-flex justify-content-lg-end justify-content-md-center justify-content-xs-start">
                                        <div class="row align-items-center">
											<div class="col-sm-9"> 
												<div class="brand-logo mb-3">
													<img class="logo-abbr mr-2" src="./images/logo.png" alt="">
												</div>
                                                <span>By simplifying lab tests and making them accessible for 
                                                    <strong>everyone to monitor and maintain a healthy lifestyle.</strong> <br>
                                                <small class="text-muted">Services provided by: Poseidon Diagnostics</small>
                                            </div>
                                            <div class="col-sm-3 mt-3"> <img src="assets/images/qr.png" class="img-fluid width110"> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-5"> <strong>Generated Reports</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($generatedReports as $report) :
                                                $i ++;
                                                $url = '';
                                                switch($report['type_id']) {
                                                    case 1:
                                                        $url = 'create_pcr_report.php?id='. $report['report_id'];
                                                        break;
                                                    case 2:
                                                        $url = 'create_antigen_report.php?id='. $report['report_id'];
                                                        break;
                                                    case 3:
                                                        $url = 'create_accula_report.php?id='. $report['report_id'];
                                                        break;
                                                    case 4:
                                                        $url = 'create_antibody_report.php?id='. $report['report_id'];
                                                        break;
                                                    case 5:
                                                        $url = 'create_flu_report.php?id='. $report['report_id'];
                                                        break;
                                                    default:
                                                        echo '<script> alert("No action"); </script>';
                                                        die;
                                                }
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left strong"><?php echo $report['name']; ?></td>
                                                <td class="left"><?php echo $report['report_results']; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo $url . '&all=1'; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo 'downloadPDF.php?id=' .$report['report_id']. '&report_type=D'?>" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="mt-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <p>
                                            <h4 class="card-title">
                                                <u><b>Appointments:</b></u>   
                                                &nbsp;&nbsp;&nbsp;&nbsp;List with previous appointments
                                            </h4> 
                                        </p>
                                        <span>
                                            <h4 class="pt-3"><b>Private SOAP Notes (internal use only)</b></h4>
                                        </span>
                                        <p class="text-lg-center text-height-1"><small>
                                            These are internal notes our nurse can write into their profile.
                                        </small></p>
                                        <div class="mt-6 col-xl-10 col-lg-10 col-md-10 col-sm-10">
                                            <hr class="mb-4">
                                            <form class="needs-validation" novalidate="">
                                                <div class="row">
                                                    <div class="col-lg-5 col-md-12 mb-3">
                                                        <label for="country" class="font-weight-bold">QUALIFIED</label>
                                                        <select class="d-block w-100 default-select" id="country" required="">
                                                            <option value=""> &nbsp;</option>
                                                            <option>Not qualified</option>
                                                            <option>Qualified</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mt-3">
                                                    <label for="address2" class="font-weight-bold">WHAT</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox" class="custom-control-input" id="save-info">
                                                    <label class="custom-control-label" for="save-info"></label>
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="address2" class="font-weight-bold">ANY ALLERGIES?</label>
                                                    <input type="text" class="form-control" id="address2" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="address2" class="font-weight-bold">SUBJECTIVE</label>
                                                    <input type="text" class="form-control" id="address2" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="address2" class="font-weight-bold">OBJECTIVE</label>
                                                    <input type="text" class="form-control" id="address2" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="address2" class="font-weight-bold">ASSESSMENT</label>
                                                    <input type="text" class="form-control" id="address2" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="address2" class="font-weight-bold">PLAN</label>
                                                    <textarea class="form-control" id="address2" placeholder="text box"></textarea>
                                                </div>

                                            
                                                <hr class="mb-4">
                                                <button class="btn btn-primary btn-lg btn-block" type="submit">Confirm</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="mt-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                            
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                            <a href="#" style="width:307px; margin-top: auto;" ><strong class="align-bottom h4"><u>Upload Report</u></strong> </a>
                                                <span class="mb-0">
                                                    <small>This will open a upload field where a Nurse can upload a report 
                                                    from a 3rd party lab/vendor. Once uploaded the Patient
                                                    will receive an email to download their report.</small>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex px-0">
                                                <a href="javascript:void(0);" style="width:159px; margin-top: auto;" data-toggle="dropdown"><strong class="align-bottom h4"><u>Create Report</u></strong> </a>
                                                <span><small>Drop down to choose to create a report</small></span>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item text-danger" href="#">Antigen</a>
                                                    <a class="dropdown-item text-danger" href="#">Visby PCR</a>
                                                    <a class="dropdown-item text-danger" href="#">Accula RT-PCR</a>
                                                    <a class="dropdown-item text-danger" href="#">Antibody Screening</a>
                                                    <a class="dropdown-item text-danger" href="#">Flu</a>
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <a href="#" style="width:200px; margin-top: auto;" ><strong class="align-bottom h4"><u>Schedule follow up</u></strong> </a>
                                                <span><small>This will send an email reminder to us ADMIN
                                                    to follow up with this patient. At a specific date</small></span>
                                            </li>
                                            <li class="list-group-item d-flex px-0">
                                                <a href="#" style="width:206px; margin-top: auto;" ><strong class="align-bottom h4"><u>Create Rx Prescription</u></strong> </a>
                                                <span class="mb-0"><small>Create a report/prescription link in email</small></span>
                                            </li>
                                            <li class="list-group-item px-0">
                                                <a href="javascript:void(0);" style="width:307px; margin-top: auto;" ><strong class="align-bottom h4"><u>Create New Note</u></strong> </a>
                                            </li>
                                        </ul>
                                        <div class="row pt-3">
                                            <div class="col-xl-1 col-lg-1 col-md-1"></div>
                                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                                <ul>
                                                    <li class="pt-2 h4">
                                                        <a href="javascript:void(0);" class="text-info"><u>notes 09/20/22 </u></a>
                                                    </li>
                                                    <li class="pt-2 h4">
                                                        <a href="javascript:void(0);" class="text-info"><u>notes 09/20/22 </u></a>
                                                    </li>
                                                    <li class="pt-2 h4">
                                                        <a href="javascript:void(0);" class="text-info"><u>notes 09/20/22 </u></a>
                                                    </li>
                                                    <li class="pt-2 h4">
                                                        <a href="javascript:void(0);" class="text-info"><u>notes 09/20/22 </u></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                <span>
                                                    <small>These are all notes created for this patient on 
                                                    different dates. You can click on the date and the 
                                                    note will appear.</small>
                                                </span>
                                            </div>
                                        </div>        
                                        <div class="row">
                                            
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 pt-5">
                                                <div class="card border rounded-0  border-info border-2">
                                                    <div class="card-header border-0">
                                                        <h4>Notes</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <form class="needs-validation" novalidate="">
                                                            <div class="mb-3">
                                                                <label for="address2" class="font-weight-bold">NOTES ABOUT THIS APPOINTMENT</label>
                                                                <input type="text" class="form-control" id="address2" placeholder="No notes">
                                                            </div>
                                                            <div class="mb-3 mt-3">
                                                                <label for="address2" class="font-weight-bold">NOTES ABOUT THIS CLIENT</label>
                                                                <textarea class="form-control" id="address2" placeholder="No notes"></textarea>
                                                            </div>

                                                        
                                                            <button class="btn btn-primary btn-lg btn-block" type="submit">Add Notes</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>   
                                    </div>
                                    
                                </div>
                                
                                <?php
                                            
                                            
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->
        <?php include('includes/footer.php'); ?>
        <!--**********************************
            Footer end
        ***********************************-->
        
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->
	<?php include('includes/script.php'); ?>
    <!--**********************************
        Scripts
    ***********************************-->
    <script>
        $(document).ready(function() {
            $(".dashboard_bar").html("User Detail");
        })
    </script>
    <!-- Circle progress -->



</body>

</html>