<?php
include('includes/css.php');
include("config/connection.php");
include("includes/loader.php");

?>
<body>
<div class="wrapper-page">
    <div class="card">
        <div class="card-body">
            <div class="p-3">
                <h4 class="text-muted font-18 m-b-5 text-center">Register Nurse</h4>
                <p class="text-muted text-center"></p>
                <form class="form-horizontal m-t-30" role="form" action="register.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label for="username">Email</label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="userpassword">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
                    </div>
                    <div class="form-group">
                        <label for="userpassword">Retype Password</label>
                        <input type="password" class="form-control" name="repassword" id="repassword" placeholder="ReEnter password">
                    </div>
                    <div class="form-group row m-t-20">
                        <div class="col-12 text-right">
                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit" name="login" onclick="return empty()" >Send</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function empty() {
        var username;
        username = document.getElementById("username").value;
        if (username == "") {
            alert("Enter user name");
            document.getElementById("username").focus();
            return false;
        };

        var email;
        email = document.getElementById("email").value;
        if (email == "") {
            alert("Enter email");
            document.getElementById("email").focus();
            return false;
        };

        var password;
        password = document.getElementById("password").value;
        if (password == "") {
            alert("Enter password");
            document.getElementById("password").focus();
            return false;
        };

        var repassword;
        repassword = document.getElementById("repassword").value;
        if (repassword == "") {
            alert("Enter password again");
            document.getElementById("repassword").focus();
            return false;
        };

        if(password != repassword){
            alert("Password not match");
            document.getElementById("password").value = "";
            document.getElementById("repassword").value = "";
            document.getElementById("password").focus();
            return false;
        }
    }
</script>
</body>
</html>
