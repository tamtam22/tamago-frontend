<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "cms");
if (mysqli_connect_errno()) {
  die("MySQL error: " . mysqli_connect_error());
}
if (isset($_POST['login'])) {
  $retrieve = $con->prepare("SELECT u.id, u.name, u.user_type, t.name FROM users u, users_type t WHERE u.email = ? AND u.password = ? AND u.user_type = t.id");
  $email    = $_POST["email"];
  $pw       = md5($_POST["password"]);
  $retrieve->bind_param("ss", $email, $pw);
  $retrieve->execute();
  $retrieve->bind_result($id, $name, $type, $typeName);
  $count = 0;
  while ($retrieve->fetch()) {
    $_SESSION["user_id"]        = $id;
    $_SESSION["user_name"]      = $name;
    $_SESSION["user_type"]      = $type;
    $_SESSION["user_type_name"] = $typeName;
    header('Location: index.php');
    $count++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Authentication :: Crisis Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">body{background-image:url(images/bg.jpg);background-repeat:no-repeat;background-size:auto 100%;background-attachment:fixed;background-color:#fff}@media screen and (min-width: 1028px){body{background-size:100% 100vh;}}#loginbox{margin-top:-150px}#loginbox>div:first-child{padding-bottom:30px}.iconmelon{display:block;margin:auto}#form>div{margin-bottom:25px}#form>div:last-child{margin-top:10px;margin-bottom:10px}.panel
      {background-color:transparent}.panel-body{padding-top:30px}#loginbox{background-color:rgba(0,0,0,.8);padding:25px 5px 10px 5px;border-radius:15px}.iconlogo,.im{position:relative;display:block;fill:#525151;text-align:center;font-size:26px;letter-spacing:.2em;font-weight:bold}.iconlogo:after,.im:after
      {content:'';position:absolute;top:0;left:0;width:100%;height:100%}.form-horizontal .form-control{font-size:15px;box-sizing:border-box;padding:10px;height:auto}.vertical-center{min-height:100%;min-height:100vh;display:flex;align-items:center}
    </style>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </head>
  <body>
    <div class="container vertical-center">
      <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
        <div class="row" style="padding-bottom:10px">
          <div class="iconlogo" style="color:#fff">
            <span style="color:red;font-size:50px">CRISIS</span><br>MANAGEMENT SYSTEM
          </div>
        </div>
        <div class="panel panel-default" style="border:0;box-shadow:none;-webkit-box-shadow:none;margin-bottom:0">
          <div class="panel-body">
            <?php if (isset($count) && $count == 0) { ?>
            <div class="alert alert-danger">
              <b>
                <center>ERROR: Incorrect Email Address or Password</center>
              </b>
            </div>
            <?php } ?>
            <form name="form" id="form" class="form-horizontal" enctype="multipart/form-data" action="login.php" method="POST">
              <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input id="email" type="email" class="form-control" name="email" value="" placeholder="Email Address" required autofocus>                                        
              </div>
              <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="form-group">
                <!-- Button -->
                <div class="col-sm-12 controls">
                  <button type="submit" name="login" class="btn btn-lg btn-primary btn-block"><i class="glyphicon glyphicon-log-in"></i>&nbsp; Sign in</button>                          
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>