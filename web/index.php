<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="" name="description">
    <meta content="" name="author">
    <title>Dashboard :: Crisis Management System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <script src="js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript">
      function resizeIframe(obj) {
          // fix incomplete height calculation due to slow page load
          if(obj.contentWindow.document.body.scrollHeight < 1000) { document.getElementById('live').contentWindow.location.reload(); }
          obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        }
    </script>
    <script type="text/javascript" src="js/moment.min.js"></script>
  </head>
  <body>
    <div id="wrapper">
      <nav class="navbar navbar-inverse navbar-fixed-top" style="border-width:0">
        <div class="navbar-header">
          <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button"><span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button> <a class="navbar-brand" href="index.php" style="letter-spacing:0.2em;color:#fff;font-size:15px;"><span style="color:red">CRISIS</span><br>MANAGEMENT SYSTEM</a>
        </div>
        <ul class="nav navbar-left top-nav">
          <li>
            <a href="index.php" class="active" style="text-align:center"><i class="fa fa-fw fa-dashboard"></i><br>Dashboard</a>
          </li>
          <?php if($_SESSION["user_type"] != "3") { ?>
          <li>
            <a href="create.php" style="text-align:center"><i class="fa fa-fw fa-edit"></i><br>New Report</a>
          </li>
          <?php } ?>
          <li>
            <a href="view_reports.php" style="text-align:center"><i class="fa fa-flag"></i><br>View All Reports</a>
          </li>
          <li>
            <a href="email_log.php" style="text-align:center"><i class="fa fa-fw fa-envelope"></i><br>Email Log</a>
          </li>
        </ul>
        <ul class="nav navbar-right top-nav">
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-user"></i> &nbsp;<?php echo $_SESSION["user_name"]; ?><br><span style="font-size:13px"><?php echo $_SESSION["user_type_name"]; ?></span> <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li>
                <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <div id="page-wrapper">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <iframe frameborder="0" src="live_map.php" width="100%" style="height:1000px" scrolling="no" id="live" onload='javascript:resizeIframe(this);'>Browser not compatible.</iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>