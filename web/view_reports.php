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
    <title>View Incident Reports :: Crisis Management System</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <!-- Custom Fonts -->
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
            lastWeek : '[last] dddd [at] LT',
            nextWeek : 'dddd [at] LT',
            sameElse : 'DD MMM YYYY [-] LT'
        }
    });
    </script>
  </head>
  <body>
    <div id="wrapper">
      <!-- Navigation -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php" style="letter-spacing:0.2em;color:#fff"><span style="color:red">CRISIS</span> MANAGEMENT SYSTEM</a>
        </div>
        <!-- Top Menu Items -->
        <ul class="nav navbar-right top-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> &nbsp;<?php echo $_SESSION["user_name"]; ?> (<?php echo $_SESSION["user_type_name"]; ?>) <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li>
                <a href="login.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
              </li>
            </ul>
          </li>
        </ul>
        <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav side-nav">
            <li>
              <a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <?php if($_SESSION["user_type"] != "3") { ?>
            <li>
              <a href="create.php"><i class="fa fa-fw fa-edit"></i> New Report</a>
            </li>
            <?php } ?>
            <li class="active">
              <a href="view_reports.php"><i class="fa fa-flag"></i> &nbsp;View All Reports</a>
            </li>
            <li>
              <a href="email_log.php"><i class="fa fa-fw fa-envelope"></i> Email Logs</a>
            </li>
          </ul>
        </div>
        <!-- /.navbar-collapse -->
      </nav>
      <div id="page-wrapper">
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="row">
            <div class="col-lg-12">
              <h1 class="page-header">
                View Incident Reports
              </h1>
            </div>
          </div>
          <!-- /.row -->
          <div class="row">
            <div class="col-lg-12">
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                      <thead>
                        <tr>
                          <th rowspan="2" width="1%" style="vertical-align:middle">#</th>
                          <th rowspan="2" width="30%" style="vertical-align:middle;text-align:center">Reported by</th>
                          <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><i class="fa fa-ambulance" style="font-size:24px"></i><br>Emergency Ambulance</th>
                          <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><i class="fa fa-life-ring" style="font-size:24px"></i><br>Rescue and Evacuation</th>
                          <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><span class="glyphicon glyphicon-fire" style="font-size:24px"></span><br>Fire Fighting</th>
                          <th rowspan="2" width="12%" style="vertical-align:middle;text-align:center">Reported on</th>
                          <th rowspan="2" width="12%" style="vertical-align:middle;text-align:center">Last Updated</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          date_default_timezone_set('Asia/Singapore');
                          $con = mysqli_connect("localhost", "root", "", "cms");
                          $retrieve = $con->prepare("SELECT id, name, mobile, assistance_type, reported_on, last_updated_on, status FROM incidents ORDER BY id DESC");
                          $retrieve->execute();
                          $retrieve->bind_result($id, $name, $mobile, $asst_type, $reported, $updated, $status);
                          while ($row = $retrieve->fetch()){
                          	$assistance = explode(",", $asst_type);
                          	$dt2 = new DateTime($updated);
                          	?>
                          	
                        <tr onclick="document.location='incident_details.php?id=<?php echo $id; ?>'" style="cursor:pointer">
                          <td><?php echo $id; ?></td>
                          <td>
                          <?php if($status == 0) {echo "<span class='btn btn-success btn-xs'>RESOLVED</span>";} elseif ($status==1) {echo "<span class='btn btn-primary btn-xs'>OPEN</span>";} ?>
                          <span style="font-weight:500">&nbsp;<?php echo $name . "</span> &nbsp;&mdash;&nbsp; <span style='font-size:12px'><i class='fa fa-phone'></i> " . $mobile . "</span>"; ?></td>
                          <td style="text-align:center"><?php if(in_array("1",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
                          <td style="text-align:center"><?php if(in_array("2",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
                          <td style="text-align:center"><?php if(in_array("3",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
                          <td style="text-align:center">
                          <script>
                          var t1 = "<?php echo $reported; ?>";
                          t1 = t1.split(/[- :]/);
                          var rDate = new Date(t1[0], t1[1]-1, t1[2], t1[3], t1[4], t1[5]);
                          document.write(moment(rDate).calendar());
                          </script>
						</td>
                          <td style="text-align:center">
                          <script>
                          var t2 = "<?php echo $updated; ?>";
                          t2 = t2.split(/[- :]/);
                          var uDate = new Date(t2[0], t2[1]-1, t2[2], t2[3], t2[4], t2[5]);
                          if(moment(uDate).fromNow() == "Invalid date") {document.write("&mdash;");}
                          else { document.write(moment(uDate).fromNow()); }
                          </script>
                          </td>
                        </tr>
                        <?php
                          }
                          $retrieve->close();
                          /* close all connections when done */
                          $con->close();
                          ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
              </div>
              <!-- /.panel -->
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>