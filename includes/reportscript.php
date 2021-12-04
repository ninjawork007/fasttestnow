<script src="assets/vendor/global/global.min.js"></script>
<script src="assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="assets/js/custom.min.js"></script>
<script src="assets/js/deznav-init.js"></script>

<!-- Datatable -->
<script src="assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>

<script>
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



