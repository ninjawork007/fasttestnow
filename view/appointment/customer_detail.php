<?php
include("../../global/variables.php");

include(__ROOT . '/includes/head.php');
include(__ROOT . '/includes/css.php');

if (!hasPermission('report_history')) {
    echo '<h2 class="text-center">Access Denied. You Don\'t Have Permission To View This Page.</h2>';
    exit;
}

?>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php require(__ROOT . "/includes/loader.php"); ?>
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
        include(__ROOT . '/includes/topbar.php');
        include(__ROOT . '/includes/sidebar.php');

        date_default_timezone_set('US/Eastern');
        $currentdate = date('m/d/Y');

        // parse URL parameter
        $result = json_decode($_POST['res']);
        $getInfo = json_decode($result, true);
        
        
        $generatedReports = $getInfo['GeneratedCovidFluReports'];
        $userInfo = $getInfo['ToBeGeneratedCovidFluReports'][0];
        $types = $getInfo['types'];
        
        $id = $userInfo['id'];
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
													<img class="logo-abbr mr-2" src="<?php echo _HOST_LINK?>/images/logo.png" alt="">
												</div>
                                                <span>By simplifying lab tests and making them accessible for 
                                                    <strong>everyone to monitor and maintain a healthy lifestyle.</strong> <br>
                                                <small class="text-muted">Services provided by: Poseidon Diagnostics</small>
                                            </div>
                                            <div class="col-sm-3 mt-3"> <img src="<?php echo _HOST_LINK?>/assets/images/qr.png" class="img-fluid width110"> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Covid 19/Flu Reports</strong></div>
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
                                                        $url = _HOST_LINK.'/view/covid_19/pcr/create.php?id='. $report['report_id'];
                                                        break;
                                                    case 2:
                                                        $url = _HOST_LINK.'/view/covid_19/antigen/create.php?id='. $report['report_id'];
                                                        break;
                                                    case 3:
                                                        $url = _HOST_LINK.'/view/covid_19/accula/create.php?id='. $report['report_id'];
                                                        break;
                                                    case 4:
                                                        $url = _HOST_LINK.'/view/covid_19/antibody/create.php?id='. $report['report_id'];
                                                        break;
                                                    case 5:
                                                        $url = _HOST_LINK.'/view/flu/create.php?id='. $report['report_id'];
                                                        break;
                                                    default:
                                                        echo '<script> alert("No action"); </script>';
                                                        die;
                                                }
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left strong"><?php echo $report['name']; ?></td>
                                                <td class="left"><?php echo ($report['report_results'] == "Negative")? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
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
                                <?php if(count($getInfo['RxOrder']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Rx Prescription</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Shipping Service</th>
                                                <th scope="col">Semaglutide</th>
                                                <th scope="col">Quantity </th>
                                                <th scope="col">Insulin Qty</th>
                                                <th scope="col">Notes</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['RxOrder'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left strong"><?php echo ($report['shipping_service'] == "patient")? "<span class='badge badge-success light'>Send to Patient</span>": "<span class='badge badge-info light'>Send to Office</span>"; ?></td>
                                                <td class="left strong"><?php echo ($report['semaglutide_check'] == 1)? "<span class='badge badge-primary'>Yes</span>": "<span class='badge badge-secondary'>No</span>"; ?></td>
                                                <td class="left"><?php echo $report['quantity']; ?></td>
                                                <td class="right"><?php echo $report['insulin_qty']; ?></td>
                                                <td class="right"><?php echo $report['notes']; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/rx_order/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Mono']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Mono Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Mono'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Mono Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/mono/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Syphilis']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Syphilis Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Syphilis'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Syphilis Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/syphilis/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Hiv']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Hiv Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Hiv'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Hiv Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/hiv/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Strep']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Strep Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Strep'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Strep Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/strep/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Hemoglobin']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Hemoglobin Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Hemoglobin'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Hemoglobin Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/hemoglobin/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Rsv']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Rsv Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Rsv'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Rsv Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/rsv/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Thyroid']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Thyroid Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Thyroid'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left">Thyroid Screening</td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo _HOST_LINK;?>/view/thyroid/create.php?id=<?php echo $report['id']; ?>" class="btn btn-rounded btn-sm btn-outline-primary">Edit</a>
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <?php if(count($getInfo['Uploaded']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Uploaded Reports</strong></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Uploaded'] as $report) :
                                                $i ++;
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="left"><?php echo $report['name']; ?></td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right"><?php echo $report['handled_at']; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                <div class="row">
                                
                                    <!-- Begin File upload Modal -->
                                    <div class="modal fade" id="uploadFileModal" role="dialog">
                                    
                                        <div class="modal-dialog">
                                            
                                            <div class="modal-content">
                                                
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                <h6 class="modal-title">Multi file uploads</h6>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                
                                                <!-- Modal body -->
                                                <div class="modal-body">
                                                
                                                    <div class="form-group">
                                                        <label class="text-label text-primary">Report Type</label>
                                                        <select id="single-select" name="type">
                                                            <?php foreach($types as $type) :  ?>
                                                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="text-label text-primary">Choose Files</label>
                                                        <input type="file" name="files" id="myfiles">
                                                        <input type="hidden" class="userID" value="<?php echo $id; ?>">
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="upload">Upload</button>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- ended file upload -->
                                    <div class="mt-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <p>
                                            <h4 class="card-title">
                                                <u><b><a href="#" data-toggle="modal" data-target="#appointmentsModal" id="modalBtn2">Appointments:</a></b></u>   
                                                &nbsp;&nbsp;&nbsp;&nbsp;List with previous appointments
                                            </h4> 
                                            <!-- Begin Appointment Modal -->
                                            <div class="modal fade bd-example-modal-lg" id="appointmentsModal" role="dialog">
                                            
                                            <div class="modal-dialog modal-lg">
                                                
                                                <div class="modal-content">
                                                    
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h6 class="modal-title"><strong class="text-primary">List with previous appointments</strong></h6>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    
                                                    <!-- Modal body -->
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col" class="h6">No</th>
                                                                        <th scope="col">Name</th>
                                                                        <th scope="col">Address</th>
                                                                        <th scope="col">Type</th>
                                                                        <th scope="col">Create At</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                    $i = 0;
                                                                    foreach ($getInfo['ToBeGeneratedCovidFluReports'] as $report) :
                                                                        $i ++;
                                                                ?>
                                                                    <tr>
                                                                        <td class="center"><?php echo $i; ?></td>
                                                                        <td class="left"><?php echo $report['name']; ?></td>
                                                                        <td class="right"><?php echo $report['address']; ?></td>
                                                                        <td class="right"><?php echo $report['appointment_type']; ?></td>
                                                                        <td class="right"><?php echo $report['datetimeCreated']; ?></td>
                                                                    </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Modal footer -->
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                                                        <!-- <button type="button" class="btn btn-primary" id="upload">Upload</button> -->
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        </p>
                                        <span>
                                            <h4 class="pt-3"><b>Private SOAP Notes (internal use only)</b></h4>
                                        </span>
                                        <p class="text-lg-center text-height-1"><small>
                                            These are internal notes our nurse can write into their profile.
                                        </small></p>
                                        <div class="mt-6 col-xl-10 col-lg-10 col-md-10 col-sm-10">
                                            <hr class="mb-4">
                                            <form class="needs-validation" novalidate="" id="soapNote" name="soapNote">
                                                <input type='hidden' name='method' value="insert" />
                                                <?php echo "<input type='hidden' name='appointment_id' value=" . $id . " />"; ?>
                                                <div class="row">
                                                    <div class="col-lg-5 col-md-12 mb-3">
                                                        <label for="qualified" class="font-weight-bold">QUALIFIED</label>
                                                        <select class="d-block w-100 default-select" id="qualified" name="qualified" required="">
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
                                                <input type="hidden"  name="soap_check" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="soap_check" name="soap_check" >
                                                    <label class="custom-control-label" for="soap_check"></label>
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="allergies" class="font-weight-bold">ANY ALLERGIES?</label>
                                                    <input type="text" class="form-control" id="allergies" name="allergies" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="subjective" class="font-weight-bold">SUBJECTIVE</label>
                                                    <input type="text" class="form-control" id="subjective" name="subjective" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="objective" class="font-weight-bold">OBJECTIVE</label>
                                                    <input type="text" class="form-control" id="objective" name="objective" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="assessment" class="font-weight-bold">ASSESSMENT</label>
                                                    <input type="text" class="form-control" id="assessment" name="assessment" placeholder="text box">
                                                </div>
                                                <hr class="mb-4">
                                                <div class="mb-3 mt-3">
                                                    <label for="plan" class="font-weight-bold">PLAN</label>
                                                    <textarea class="form-control" id="plan" name="plan" placeholder="text box"></textarea>
                                                </div>

                                            
                                                <hr class="mb-4">
                                                <button class="btn btn-primary btn-lg btn-block" type="submit">Confirm</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="mt-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                            
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                            <a href="javascript:void(0);" style="width:307px; margin-top: auto;" data-toggle="modal" data-target="#uploadFileModal" id="modalBtn"><strong class="align-bottom h4"><u>Upload Report</u></strong> </a>
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
                                                    <?php 
                                                        $url = "";
                                                        foreach($types as $type) :  
                                                            switch($type['name']) :
                                                                case 'Visby PCR':
                                                                    $url = _HOST_LINK . '/view/covid_19/pcr/create.php';
                                                                    break;
                                                                case 'Antigen':
                                                                    $url = _HOST_LINK . '/view/covid_19/antigen/create.php';
                                                                    break;
                                                                case 'Accula':
                                                                    $url = _HOST_LINK . '/view/covid_19/accula/create.php';
                                                                    break;
                                                                case 'Antibody':
                                                                    $url = _HOST_LINK . '/view/covid_19/antibody/create.php';
                                                                    break;
                                                                case 'Flu':
                                                                    $url = _HOST_LINK . '/view/flu/create.php';
                                                                    break;
                                                                case 'Rx Order':
                                                                    $url = _HOST_LINK . '/view/rx_order/create.php';
                                                                    break;
                                                                case 'Mono':
                                                                    $url = _HOST_LINK . '/view/mono/create.php';
                                                                    break;
                                                                case 'Strep':
                                                                    $url = _HOST_LINK . '/view/strep/create.php';
                                                                    break;
                                                                case 'Syphilis':
                                                                    $url = _HOST_LINK . '/view/syphilis/create.php';
                                                                    break;
                                                                case 'Hemoglobin':
                                                                    $url = _HOST_LINK . '/view/hemoglobin/create.php';
                                                                    break;
                                                                case 'Hiv':
                                                                    $url = _HOST_LINK . '/view/hiv/create.php';
                                                                    break;
                                                                case 'Rsv':
                                                                    $url = _HOST_LINK . '/view/rsv/create.php';
                                                                    break;
                                                                case 'Thyroid':
                                                                    $url = _HOST_LINK . '/view/thyroid/create.php';
                                                                    break;
                                                            endswitch;
                                                    ?>
                                                        <a class="dropdown-item text-danger" href="<?php echo $url; ?>"><?php echo $type["name"]; ?></a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex px-0 justify-content-between">
                                                <a href="#" style="width:200px; margin-top: auto;" ><strong class="align-bottom h4"><u>Schedule follow up</u></strong> </a>
                                                <span><small>This will send an email reminder to us ADMIN
                                                    to follow up with this patient. At a specific date</small></span>
                                            </li>
                                            <li class="list-group-item d-flex px-0">
                                                <?php $url = _HOST_LINK . '/view/rx_order/create.php'; ?>
                                                <a href="<?php echo $url; ?>" style="width:206px; margin-top: auto;" ><strong class="align-bottom h4"><u>Create Rx Prescription</u></strong> </a>
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
                                                    <?php 
                                                    
                                                    foreach($getInfo['Notes'] as $report) : 
                                                        $timestamp = strtotime($report['handled_at']);
                                                        $date = date('m/d/Y', $timestamp);
                                                        $time = date('Gi.s', $timestamp);
                                                    ?>
                                                    <li class="pt-2 h4" style="white-space: nowrap;">
                                                        <a href="javascript:void(0);" class="text-info" data-toggle="modal" data-target="#notes_<?php echo $report['id'];?>"><u> <?php echo "notes ". $date;?> </u></a>
                                                    </li>
                                                    <!-- Begin File upload Modal -->
                                                    <div class="modal fade" id="notes_<?php echo $report['id'];?>" role="dialog">
                                                    
                                                        <div class="modal-dialog">
                                                            
                                                            <div class="modal-content">
                                                                
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                <h6 class="modal-title"><?php echo "Notes ". $date;?></h6>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                
                                                                <!-- Modal body -->
                                                                <div class="modal-body">
                                                                
                                                                    <div class="form-group">
                                                                        <label class="text-label text-primary">NOTES ABOUT THIS APPOINTMENT</label><br/>
                                                                        <label class="text-label text-info">
                                                                            <?php echo $report["noteForAppointment"]; ?>
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="text-label text-primary">NOTES ABOUT THIS CLIENT</label><br/>
                                                                        <label class="text-label text-info">
                                                                            <?php echo $report["noteForClient"]; ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Modal footer -->
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
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
                                                        <form id="notes" class="needs-validation" novalidate="">
                                                            <input type='hidden' name='method' value="insert" />
                                                            <?php echo "<input type='hidden' name='appointment_id' value=" . $id . " />"; ?>
                                                            <div class="mb-3">
                                                                <label for="noteForAppointment" class="font-weight-bold">NOTES ABOUT THIS APPOINTMENT</label>
                                                                <input type="text" class="form-control" name="noteForAppointment" id="noteForAppointment" placeholder="No notes">
                                                            </div>
                                                            <div class="mb-3 mt-3">
                                                                <label for="noteForClient" class="font-weight-bold">NOTES ABOUT THIS CLIENT</label>
                                                                <textarea class="form-control" name="noteForClient" id="noteForClient" placeholder="No notes"></textarea>
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
        <form id="sendingData" action="customer_detail.php" style="display: none;" method="POST">
        
                <input type="hidden" class="form-control" name="res" id="res">
            
                <input type="submit" id="submitData">Send Data</button>
        </form>

        <!--**********************************
            Footer start
        ***********************************-->
        <?php include(__ROOT . '/includes/footer.php'); ?>
        <!--**********************************
            Footer end
        ***********************************-->
        
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->
	<?php include(__ROOT . '/includes/script.php'); ?>
    <!--**********************************
        Scripts
    ***********************************-->
    <script>
        var userInfo = '<?php echo json_encode($userInfo); ?>';
        var id = '<?php echo $id; ?>';
        var fileSources = [];
        var count = 1;

        $(document).ready(function() {
            
            // change the text
            // var htm = $("div.modal-body div.form-group:nth-child(2)").html(); 
            // var str = htm.toString();
            // var text = str.replace(/innostudio.de/g, 'Fast Test Now'); 
            // html = $.parseHTML( text ),
            // $("div.modal-body div.form-group:nth-child(2)").append(html);

           
            $(".dashboard_bar").html("Customer Details");
            
            $('input[name="files"]').fileuploader({
                onSelect: function(item) {
                    
                    if (!item.html.find('.fileuploader-action-start').length)
                        item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-start" title="Upload"><i></i></a>');
                        item.html.find('.column-title span').after('<select name="report_results" id="report_results" class="form-control" required><option value="-1">--Select Results--</option><option value="0" >Negative</option><option value="1">Positive</option>');

                    $("div.column-title").each(function(){
                        $(this).children(":first-child").text($(this).children(":first-child").text().replace("innostudio.de_",""));
                        $(this).children(":first-child").attr('title', $(this).children(":first-child").text().replace("innostudio.de_",""));
                    });
                },
                upload: {
                    url: $host + '/upload/ajax_upload_file.php',
                    data: null,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    start: false,
                    synchron: true,
                    onSuccess: function(result, item) {
                        var test_result = item.html.find("select[name='report_results'] option:selected").val();
                        var data = jQuery.parseJSON(result);
                        save_data(data, test_result);
                        item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
                    },
                    onError: function(item) {
                        item.upload.status != 'cancelled' && item.html.find('.fileuploader-action-retry').length == 0 ? item.html.find('.column-actions').prepend(
                            '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
                        ) : null;
                    },
                    onComplete: null,
                },
                onRemove: function(item) {
                    // send POST request
                    $.post($host + '/upload/ajax_remove_file.php', {
                        file: item.name
                    });
                }
            });

        });
        $("#upload").on('click', file_upload);

        function file_upload() {
            var flag = 0;
            
            $("ul.fileuploader-items-list li").each(function(i) {
                if ($(this).find("select option:selected").val() == -1) {
                    flag = 1;
                    my_msg(`Warning`, `Please select the result of ${(i+1)}th report`);
                }
            })

            if(flag == 0) {
                $("ul.fileuploader-items-list li").each(function(i) {
                    $(this).find("a.fileuploader-action-start").trigger("click");
                    
                })
            }

        }
        
        // upload reports
        function save_data(data, results) {
            $.ajax({
                type: "POST",
                url: $host + '/controller/customers.php',
                data: {
                    method: 'uploadCustomerReport',
                    appointment_id: $(".userID").val(),
                    testType: $("select[name='type'] option:selected").val(),
                    filePath: data.files[0].file,
                    userInfo: JSON.parse(userInfo),
                    results: results
                },
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    if(data === true) {
                        count += 1;
                        my_msg(`Success`, `Successfully uploaded`);
                        if(count == $("ul.fileuploader-items-list li").length) {
                            var user = JSON.parse(userInfo);
                            $.ajax({
                                type: "POST",
                                url: $host + '/controller/customers.php',
                                data: {
                                    method: 'getCustomerDetail',
                                    id: id,
                                    email: null
                                },
                                success: function(data) {
                                    hideLoadingBar();
                                    var result = JSON.stringify(data);
                                    console.log(result);
                                    $("#res").val(result);
                                    $("#sendingData").submit();
                                }
                            });
                        }
                    }
                    
                }
            });
        }
        // save SOAP Note
        $("#soapNote").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.
            showLoadingBar();
            var form = $(this);

            $.ajax({
                type: "POST",
                url: $host + '/controller/soap.php',
                data: form.serialize(), // serializes the form's elements.
                dataType: "json",
                success: function(data) {
                    if (data.result === true) {
                        my_msg(`Success`, `Saved SOAP Note`);
                        hideLoadingBar();
                    }
                }

            });
        });
        // save Notes
        $("#notes").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.
            showLoadingBar();
            var form = $(this);

            $.ajax({
                type: "POST",
                url: $host + '/controller/notes.php',
                data: form.serialize(), // serializes the form's elements.
                dataType: "json",
                success: function(data) {
                    if (data.result === true) {
                        my_msg(`Success`, `Saved Notes`);
                        hideLoadingBar();
                        $.ajax({
                            type: "POST",
                            url: $host + '/controller/customers.php',
                            data: {
                                method: 'getCustomerDetail',
                                id: id,
                                email: null
                            },
                            success: function(data) {
                                hideLoadingBar();
                                var result = JSON.stringify(data);
                                console.log(result);
                                $("#res").val(result);
                                $("#sendingData").submit();
                            }
                        });
                    }
                }

            });
        });
        function my_msg(type, msg) {
            if(type == 'Warning') {
                toastr.success(msg, type, {
                    positionClass: "toast-top-right",
                    timeOut: 5e3,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    preventDuplicates: !0,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1
                })
            } else {
                toastr.info(msg, type, {
                    positionClass: "toast-top-right",
                    timeOut: 5e3,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    preventDuplicates: !0,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1
                })
            }
            
        }
        
    </script>
    <!-- Circle progress -->



</body>

</html>