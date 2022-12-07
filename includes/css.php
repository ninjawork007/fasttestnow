<?php 
$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
$host = $_SERVER['HTTP_HOST'] . '/';
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . $project) : ($protocole . $host);

?>
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $url; ?>/images/favicon.png">
<link rel="stylesheet" href="<?php echo $url; ?>/assets/vendor/chartist/css/chartist.min.css">
<link href="<?php echo $url; ?>/assets/vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
<!-- Datatable -->
<link href="<?php echo $url; ?>/assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Style for Select2 -->
<link rel="stylesheet" href="<?php echo $url; ?>/assets/vendor/select2/css/select2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?php echo $url; ?>/assets/vendor/toastr/css/toastr.min.css">

<link href="<?php echo $url; ?>/assets/css/style.css" rel="old stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

<link href="<?php echo $url; ?>/assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
<link href="<?php echo $url; ?>/assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet">

<!-- fonts -->
<link href="<?php echo $url; ?>/assets/css/font/font-fileuploader.css" rel="stylesheet" type="text/css">


<!-- styles -->
<link href="<?php echo $url; ?>/assets/css/jquery.fileuploader.min.css" rel="stylesheet" type="text/css">

<style>
    .form-control {
        color: black;
    }
</style>
</head>