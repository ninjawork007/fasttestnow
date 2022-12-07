<?php 

$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
$host = $_SERVER['HTTP_HOST'] . '/';
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . $project) : ($protocole . $host);

?>

<script src="<?php echo $url; ?>/assets/vendor/global/global.min.js"></script>
<script src="<?php echo $url; ?>/assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="<?php echo $url; ?>/assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo $url; ?>/assets/js/custom.min.js"></script>
<script src="<?php echo $url; ?>/assets/js/deznav-init.js"></script>

<!-- Datatable -->
<script src="<?php echo $url; ?>/assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo $url; ?>/assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>

<!-- select 2 -->
<script src="<?php echo $url; ?>/assets/vendor/select2/js/select2.full.min.js"></script>
<script src="<?php echo $url; ?>/assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="<?php echo $url; ?>/assets/js/plugins-init/select2-init.js"></script>


<!-- Toastr -->
<script src="<?php echo $url; ?>/assets/vendor/toastr/js/toastr.min.js"></script>

<!-- All init script -->
<script src="<?php echo $url; ?>/assets/js/plugins-init/toastr-init.js"></script>
<!-- Multi file uploads -->
<script src="<?php echo $url; ?>/assets/js/jquery.fileuploader.min.js" type="text/javascript"></script>

<script>
    // define GLOBAL variable;
    var $host = '<?php echo $url; ?>';
   
    // define GLOBAL functions;
    function showLoadingBar() {
        $('#preloader').css("display", "block");
        $("#main-wrapper").css("opacity", 0.6);
    }
    function hideLoadingBar() {
        $('#preloader').css("display", "none");
        $("#main-wrapper").css("opacity", 1);
    }
    (function() {
        var redirect = false
        if (navigator.userAgent.match(/iPhone/i)) {
            redirect = true
        }
        var isAndroid = /(android)/i.test(navigator.userAgent)
        var isMobile = /(mobile)/i.test(navigator.userAgent)
        if (isAndroid && isMobile) {
            redirect = true
        }
        if (redirect) {
            $("a.brand-logo").removeAttr("href");
        }
    })();
</script>


