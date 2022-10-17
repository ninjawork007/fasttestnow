<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Fast Test Now - Login</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.ico">
    <link href="assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        .disabled {
            display: none;
        }

        .show {
            display: block;
        }

        .invalid-feedback {
            color: #ffffff;
        }
    </style>
</head>
<?php
    $user_email = "";
    if(!empty($_GET["email"])) {
        $user_email = $_GET["email"];
    }
?>
<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <a href="index.html"><img src="./images/logo.png" alt=""></a>
                                    </div>
                                    <h4 class="text-center mb-4 text-white">Sign in your account</h4>
                                    <form class="form-valide" action="#">
                                        <div class="form-group">
                                            <label class="mb-1 text-white col-form-label" for="email"><strong>Email</strong>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control" placeholder="email" name="email" id="email" value="<?php echo $user_email; ?>">
                                        </div>
                                        <div class="text-center show" id="btn_otp_container">
                                            <button type="button" class="btn bg-white text-primary btn-block" id="btn_otp">Send One Time Password</button>
                                        </div>
                                    </form>
                                    <form id="form-valide">
                                        <div class="form-group disabled" id="otp_container">
                                            <label class="mb-1 text-white col-form-label" for="otp"><strong>Enter OTP</strong>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div>
                                            <input type="password" class="form-control" placeholder="OTP" name="otp" id="otp">
                                            </div>
                                        </div>

                                        <div class="text-center disabled" id="btn_login_container">
                                            <button type="button" class="btn bg-white text-primary btn-block" id="btn_login">Login</button>
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
    <form id="sendingData" action="../user_portal/details.php" style="display: none;" method="POST">
        
            <input type="hidden" class="form-control" name="res" id="res">
        
            <input type="submit" id="submitData">Send Data</button>
    </form>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <?php include('includes/script.php'); ?>
    <script>
        jQuery("#form-valide").validate({
            rules: {
                "otp": {
                    required: !0,
                    minlength: 6
                }
            },
            messages: {
                "otp": {
                    required: "Please provide a OTP",
                    minlength: "Your OTP must be 6 digits"
                },
            },

            ignore: [],
            errorClass: "invalid-feedback animated fadeInUp",
            errorElement: "div",
            errorPlacement: function(e, a) {
                jQuery(a).parents(".form-group > div").append(e)
            },
            highlight: function(e) {
                jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid");
            },
            success: function(e) {
                jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove();
                // redirect to User detail page
                console.log("sending OTP for validation");
            },
        });

        $("#btn_login").click(function() {
            
            if (!$("#form-valide").valid()) { // Not Valid
                return false;
            } else {
                $.ajax({
                type: "POST",
                url: 'verifyOTP.php',
                data: {
                    method: 'validateOTP',
                    otp: $("#otp").val(),
                    email: window.localStorage.getItem("email"),
                    member_id: window.localStorage.getItem("member_id")
                },
                success: function(data) {
                    
                    var result = jQuery.parseJSON(data);
                    if(result.notice == "success") {
                        var data = JSON.stringify(result.data);
                        $("#res").val(data);
                        $("#sendingData").submit();
                    } else {
                        alert(2);
                    }

                }

            });
            }
        })
    </script>
    <script>
        $("#btn_otp").click(function() {
            console.log("getting OTP ...");
            $("#otp_container").removeClass("disabled").addClass("show");
            $("#btn_login_container").removeClass("disabled").addClass("show");
            $("#btn_otp_container").removeClass("show").addClass("disabled");

            $.ajax({
                type: "POST",
                url: 'verifyOTP.php',
                data: {
                    method: 'getOTP',
                    email: $("#email").val(),
                },
                success: function(data) {
                    var result = jQuery.parseJSON(data);
                    window.localStorage.setItem('email', result.email);
                    window.localStorage.setItem('member_id', result.member_id);
                }

            });

        })
    </script>
</body>

</html>