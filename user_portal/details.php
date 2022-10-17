<?php
include('includes/head.php');
include('includes/css.php');
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

		$result = json_decode($_POST['res']);
        $getInfo = json_decode($result, true);

		// for($i = 0; $i < count($getInfo); $i ++) :
		// 	$user_generated_report = $getInfo[$i]['GeneratedReports'];
		// 	$user_firstname = $user_generated_report[0]['patient_firstname'];
		// 	$user_lastname = $user_generated_report[0]['patient_lastname'];

		// 	$user_to_be_generated_report = $getInfo[$i]['ToBeGeneratedReports'];
		// 	$user_firstname_to_be_generated = $user_to_be_generated_report[0]['firstName'];
		// 	$user_lastname_to_be_generated = $user_to_be_generated_report[0]['lastName'];

		// 	for($j = 0; $j < count($getInfo); $j ++) :
		// 		$tempUser_generated_report = $getInfo[$j]['GeneratedReports'];
		// 		$tempUser_firstname = $tempUser_generated_report[0]['patient_firstname'];
		// 		$tempUser_lastname = $tempUser_generated_report[0]['patient_lastname'];

		// 		$tempUser_to_be_generated_report = $getInfo[$j]['ToBeGeneratedReports'];
		// 		$tempUser_firstname_to_be_generated = $tempUser_to_be_generated_report[0]['firstName'];
		// 		$tempUser_lastname_to_be_generated = $tempUser_to_be_generated_report[0]['lastName'];

		// 		if($user_firstname == $tempUser_firstname && $user_lastname == $tempUser_lastname) :

		// 		endif;
		// 	endfor;
		// endfor;
		
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
						<h6 class="fs-16 text-black font-w600 mb-0"><?php echo count($getInfo); ?> Total Orders</h6>
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
										<tbody>
										<?php 
											$i = 0;
											 
											foreach($getInfo as $user) { 
												$i++;
												$color = ($user['report_results'] == 'Positive')? "#ffd3cd": "#ffffff";
												$styleBackgroundColor = 'style="background-color:'.$color.'"';
												$gender = ($user['patient_gender'] == 0)? "./assets/images/avatar/man.png": "./assets/images/avatar/woman.png";

												$testType = "";
												switch($user['type_id']) {
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
												$downloadLink = "../downloadPDF.php?id='".$user['report_id']."'&report_type=D";
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
																	<?php echo $user['patient_firstname'] ." ". $user['patient_lastname']; ?>
																</a>
															</h4>
															<span><?php echo $user['handled_at']; ?></span>
														</div>
													</div>
												</td>
												<td class="d-none d-lg-table-cell"><?php echo $testType; ?></td>
												<td class="d-none d-lg-table-cell"><?php echo $user['report_results']; ?></td>
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
		include('includes/footer.php'); ?>
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
    <?php include('includes/script.php'); ?>
	<script>
		$(".dashboard_bar").html("User Portal");
	</script>
</body>
</html>