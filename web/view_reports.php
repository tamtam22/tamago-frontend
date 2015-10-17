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
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> John Smith <b class="caret"></b></a>
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
            <li>
              <a href="create.php"><i class="fa fa-fw fa-edit"></i> New Report</a>
            </li>
            <li class="active">
              <a href="view_reports.php"><i class="fa fa-flag"></i> &nbsp;View Reports</a>
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
                          <th rowspan="2" width="20%" style="vertical-align:middle;text-align:center">Reported by</th>
                          <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><i class="fa fa-ambulance" style="font-size:24px"></i><br>Emergency Ambulance</th>
                          <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><i class="fa fa-life-ring" style="font-size:24px"></i><br>Rescue and Evacuation</th>
                          <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><span class="glyphicon glyphicon-fire" style="font-size:24px"></span><br>Fire Fighting</th>
                          <th rowspan="2" width="17%" style="vertical-align:middle;text-align:center">Reported on</th>
                          <th rowspan="2" width="17%" style="vertical-align:middle;text-align:center">Last Updated</th>
                          <th rowspan="2" width="17%" style="vertical-align:middle;text-align:center">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          date_default_timezone_set('Asia/Singapore');
                          function time_ago(Datetime $date) {
                          	$time_ago = '';
                          
                          	$diff = $date->diff(new Datetime('now'));
                          	$seconds = $diff->days*86400 + $diff->h*3600 + $diff->i*60 + $diff->s;
                          	if($seconds > 0){
                          		if (($t = $diff->format("%m")) > 0)
                          			$time_ago .= $t . ' months ';
                          			if (($t = $diff->format("%d")) > 0)
                          				$time_ago .= $t . ' days ';
                          				if (($t = $diff->format("%H")) > 0) {
                          					$time_ago .= $t . 'hr ';
                          				}
                          				if (($t = $diff->format("%i")) >= 0) {
                          					if (($t = $diff->format("%i")) < 10) {
                          						$time_ago .= '0' . $t . 'min ';
                          					} else {
                          						$time_ago .= $t . 'min ';
                          					}
                          				}
                          
                          				if (($t = $diff->format("%s")) >= 0) {
                          					if (($t = $diff->format("%s")) < 10) {
                          						$time_ago .= '0' . $t . 'sec';
                          					} else {
                          						$time_ago .= $t . 'sec';
                          					}
                          				}
                          	}
                          	else
                          		$time_ago = "&mdash;";
                          		return $time_ago;
                          }
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
                          <td style="text-align:center"><?php echo $name . " (" . $mobile . ")"; ?></td>
                          <td style="text-align:center"><?php if(in_array("1",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
                          <td style="text-align:center"><?php if(in_array("2",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
                          <td style="text-align:center"><?php if(in_array("3",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
                          <td style="text-align:center"><?php echo date("h:i A",strtotime($reported)) . "<br><span style='font-size:12px'>(" . date("d M Y",strtotime($reported)); ?>)</span></td>
                          <td style="text-align:center"><b><?php echo time_ago($dt2) . " ago"; ?></b></td>
                          <td style="text-align:center"><b><?php if($status == 0) {echo "<span style='color:red'>CLOSED</span>";} elseif ($status==1) {echo "<span style='color:blue'>OPEN</span>";} ?></b></td>
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