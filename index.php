<?php include('includes/css.php'); ?>
<?php
include("config/connection.php");
if (isset($_SESSION['admin_name'])) {
  header("Location:dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <title>HEALTH</title>
  <meta content="Admin Dashboard" name="description" />
  <meta content="Themesbrand" name="author" />
  <link rel="shortcut icon" href="assets/images/favicon.ico">
  <style>
    @media only screen and (max-width: 600px) {
      .wrapper-page {
        margin: 40% auto;
      }
    }
  </style>

<body class="h-100">
  <!-- Begin page -->
  <div class="authincation h-100">
    <div class="container h-100">
      <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-6">
          <div class="authincation-content">
            <div class="row no-gutters">
              <div class="col-xl-12">
                <div class="auth-form">
                  <div class="text-center mb-3">
                    <a href="/"><img src="images/logo.png" alt=""></a>
                  </div>
                  <h4 class="text-center mb-4 text-white">Sign in your account</h4>
                  <form id="myform" role="form" action="login.php" method="post">
                    <div class="form-group">
                      <label class="mb-1 text-white"><strong>Username</strong></label>
                      <input type="text" name="username" id="username" class="form-control">
                    </div>
                    <div class="form-group">
                      <label class="mb-1 text-white"><strong>Password</strong></label>
                      <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-row d-flex justify-content-between mt-4 mb-2">
                      <div class="form-group">
                        <div class="custom-control custom-checkbox ml-1 text-white">
                          <input type="checkbox" class="custom-control-input"  id="customControlInline">
                          <label class="custom-control-label" for="customControlInline">Remember my preference</label>
                        </div>
                      </div>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn bg-white text-primary btn-block" name="login" id="login">Sign Me In</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
 
  <?php include('includes/script.php'); ?>
  <script>
    var msg = '<?php echo $_COOKIE["msg"]; ?>';
    if (msg)
        toastr.warning(msg, "Warning", {
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
  </script>
  <script>
    $(document).ready(function() {
      // enter keyd
      $(document).bind('keypress', function(e) {
        if (e.keyCode == 13) {
          $("#myform").submit();
        }
      });

    });
  </script>
  <?php include('includes/script-bottom.php'); ?>
</body>

</html>