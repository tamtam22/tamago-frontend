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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Email Logs :: Crisis Management System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript">
      moment.locale('en', {
          calendar : {
              lastDay : '[Yesterday at] LT',
              sameDay : '[Today at] LT',
              nextDay : '[Tomorrow at] LT',
              lastWeek : '[Last] dddd [at] LT',
              nextWeek : 'dddd [at] LT',
              sameElse : 'DD MMM YYYY [-] LT'
          }
      });
    </script>
  </head>
  <body>
    <div id="wrapper">
      <nav class="navbar navbar-inverse navbar-fixed-top" style="border-width:0">
        <div class="navbar-header">
          <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button"><span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button> <a class="navbar-brand" href="index.php" style="letter-spacing:0.2em;color:#fff;font-size:15px;"><span style="color:red">CRISIS</span><br>MANAGEMENT SYSTEM</a>
        </div>
        <ul class="nav navbar-left top-nav">
          <li>
            <a href="index.php" style="text-align:center"><i class="fa fa-fw fa-dashboard"></i><br>Dashboard</a>
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
            <a href="email_log.php" class="active" style="text-align:center"><i class="fa fa-fw fa-envelope"></i><br>Email Log</a>
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
              <h1 class="page-header">
                Email Logs
              </h1>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                      <thead>
                        <tr>
                          <th width="5%">#</th>
                          <th width="30%">Sent on</th>
                          <th width="65%">Receipient</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          /* ---------------------------------getEmailLog()-----------------------------------*/
                          $con      = mysqli_connect("localhost", "root", "", "cms");
                          $retrieve = $con->prepare("SELECT e.id, e.sent_date_time, u.name, t.name FROM email_log e, users u, users_type t WHERE e.receipient_id = u.id AND t.id = u.user_type GROUP BY e.id ORDER BY e.id DESC");
                          $retrieve->execute();
                          $retrieve->bind_result($id, $sent, $receipient, $user_type);
                          while ($row = $retrieve->fetch()) {
                            $dt = new DateTime($sent);
                          /* --------------------------------End of email log----------------------------------*/  
                          ?>
                        <tr>
                          <td><?php echo $id; ?></td>
                          <td>
                            <script type="text/javascript">
                              var s1 = "<?php echo $sent; ?>";
                              s1 = s1.split(/[- :]/);
                              var sDate = new Date(s1[0], s1[1] - 1, s1[2], s1[3], s1[4], s1[5]);
                              document.write(moment(sDate).calendar());
                            </script>
                          </td>
                          <td><b><?php echo $receipient . " <div class='pull-right'><kbd>" . $user_type . "</kbd></div>"; ?></b></td>
                        </tr>
                        <?php
                          }
                          $retrieve->close();
                          $con->close();
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
    </div>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>