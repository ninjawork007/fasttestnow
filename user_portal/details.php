<?php
include("../global/variables.php");
include(__ROOT . '/user_portal/includes/head.php');
include(__ROOT . '/user_portal/includes/css.php');
?>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php require(__ROOT . "/user_portal/includes/loader.php"); ?>
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
        include(__ROOT . '/user_portal/includes/topbar.php');
        include(__ROOT . '/user_portal/includes/sidebar.php');

		$result = json_decode($_POST['res']);
        $getInfo = json_decode($result, true);

		// var_dump($getInfo);
        $generatedReports = $getInfo['GeneratedCovidFluReports'];
        $totalOrder = count($getInfo['GeneratedCovidFluReports']) + count($getInfo['RxOrder']) + count($getInfo['Mono']) + count($getInfo['Syphilis']) + count($getInfo['Hiv']) + count($getInfo['Strep']) + count($getInfo['Hemoglobin']) + count($getInfo['Rsv']) + count($getInfo['Thyroid']) + count($getInfo['Uploaded']);
		?>
        <!--**********************************
            Header / Sidebar end ti-comment-alt
        ***********************************-->

       		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<!-- Add Order -->

				<div class="d-flex flex-wrap mb-2 align-items-center justify-content-between">
					<div class="mb-3">
						<h6 class="fs-16 text-black font-w600 mb-0"><?php echo $totalOrder; ?> Total Orders</h6>
						<span class="fs-14">Based your preferences</span>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xl-12">	
						<div class="tab-content">
							<div id="Antigen" class="tab-pane active">
								<div class="table-responsive fs-14">
									<table class="table mb-4 dataTablesCard no-hover card-table fs-14" id="antigen_table">
										<thead>
											<tr>
												<th>No</th>
												<th>Name</th>
												<th>Test Type</th>
												<th class="d-none d-lg-inline-block">Result</th>
												<!-- <th>Status</th> -->
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="Covid">
										<?php 
											$i = 0;
											 
											foreach ($generatedReports as $report) { 
												$i++;
												$color = ($report['report_results'] == 'Positive')? "#ffd3cd": "#ffffff";
												$styleBackgroundColor = 'style="background-color:'.$color.'"';
												$gender = ($report['patient_gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";

												$testType = "";
												switch($report['type_id']) {
													case 1:
														$testType = "Visby PCR";
													break;
													case 2:
														$testType = "Antigen";
													break;
													case 3:
														$testType = "Accula";
													break;
													case 4:
														$testType = "Antibody";
													break;
													case 5:
														$testType = "Flu";
													break;
													default:
													break;
												}
												$downloadLink = "../model/downloadPDF.php?id='".$report['report_id']."'&report_type=D";
										?>
											<tr <?php echo $styleBackgroundColor; ?>>
												<td>
													<?php echo $i; ?>
												</td>
												<td>
													<div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['patient_firstname'] ." ". $report['patient_lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>
												</td>
												<td class="d-none d-lg-table-cell"><?php echo $testType; ?></td>
												<td class="d-none d-lg-table-cell"><?php echo $report['report_results']; ?></td>
												<!-- <td>
													<span class="badge light badge-warning">Pending</span>
													<span class="badge light badge-success">Successful</span>
													<span class="badge light badge-danger">Canceled</span>
												</td> -->
												<td>
													<div class="d-flex">
														<a href="<?php echo $downloadLink; ?>" class="btn btn-primary shadow btn-sm" style="font-size: 1.2rem!important;border-radius:50%"><i class="fa fa-download color-warning"></i></a>
													</div>
												</td>
											</tr>
											
											<?php
												} 
											?>
											
										</tbody>
									</table>
								</div>
							</div>
							<?php if(count($getInfo['RxOrder']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Rx Prescription</strong></div>
                                <div class="table-responsive">
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Shipping Service</th>
                                                <th scope="col">Semaglutide</th>
                                                <th scope="col">Quantity </th>
                                                <th scope="col">Insulin Qty</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="RxOrder">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['RxOrder'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div> 
                                                </td>
                                                <td class="left strong"><?php echo ($report['shipping_service'] == "patient")? "<span class='badge badge-success light'>Send to Patient</span>": "<span class='badge badge-info light'>Send to Office</span>"; ?></td>
                                                <td class="left strong"><?php echo ($report['semaglutide_check'] == 1)? "<span class='badge badge-primary'>Yes</span>": "<span class='badge badge-secondary'>No</span>"; ?></td>
                                                <td class="left"><?php echo $report['quantity']; ?></td>
                                                <td class="right"><?php echo $report['insulin_qty']; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Mono">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Mono'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>  
                                                </td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Syphilis">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Syphilis'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div> 
                                                </td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Hiv">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Hiv'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>   
                                                </td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Strep">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Strep'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div> 
                                                </td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Hemoglobin">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Hemoglobin'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>
                                                </td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Handled at</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Rsv">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Rsv'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td class="right">
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>
                                                </td>
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
                                <?php if(count($getInfo['Thyroid']) != 0) :?>
                                <div class="col-lg-4 col-sm-5"> <strong class="text-primary">Generated Thyroid Test</strong></div>
                                <div class="table-responsive">
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Results</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Thyroid">
                                        <?php
                                            $i = 0;
                                            foreach ($getInfo['Thyroid'] as $report) :
                                                $i ++;
                                                $gender = ($report['gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";
                                        ?>
                                            <tr>
                                                <td class="center"><?php echo $i; ?></td>
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>
                                                </td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
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
                                    <table class="table mb-4 dataTablesCard no-hover card-table fs-14">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="h6">No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Results</th>
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
                                                <td>
                                                    <div class="media align-items-center">
														<img class="img-fluid rounded mr-3 d-none d-xl-inline-block" width="70" src="<?php echo $gender; ?>" alt="DexignZone">
														<div class="media-body">
															<h4 class="font-w600 mb-1 wspace-no">
																<a href="javascript:void(0)" class="text-black">
																	<?php echo $report['firstname'] ." ". $report['lastname']; ?>
																</a>
															</h4>
															<span><?php echo $report['handled_at']; ?></span>
														</div>
													</div>
                                                </td>
                                                <td class="left"><?php echo $report['name']; ?></td>
                                                <td class="right"><?php echo ($report['results'] == 0)? "<span class='badge badge-success light'>Negative</span>": "<span class='badge badge-danger light'>Positive</span>"; ?></td>
                                                <td class="right">
                                                    <a href="<?php echo $report['pdfLink']; ?>" target="_blank" class="btn btn-rounded btn-sm btn-outline-success">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
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
		<?php 
		include(__ROOT . '/user_portal/includes/footer.php'); ?>
        <!--**********************************
            Footer end
        ***********************************-->

    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <?php include(__ROOT . '/user_portal/includes/script.php'); ?>
	<script>
		$(".dashboard_bar").html("User Portal");
	</script>
</body>
</html>