<?php
session_start();
if(!isset($_SESSION["user_id"])){
	header('Location: login.php');
    exit();
}
if(!isset($_GET["id"])) {
	header("Location: view_reports.php");
}

$asst_array = "";
$con = mysqli_connect("localhost", "root", "", "cms");

if(isset($_GET["reopen"]) && $_GET["reopen"] == "true") {
	$update = $con->prepare("UPDATE incidents SET status = 1, last_updated_user = ? WHERE id = ?");
    $update->bind_param("ii", $_SESSION["user_id"], $_GET["id"]);
    $update->execute();
	$reopen = $update->affected_rows;
}

if(isset($_GET["resolved"]) && $_GET["resolved"] == "true") {
	$update = $con->prepare("UPDATE incidents SET status = 0, last_updated_user = ? WHERE id = ?");
	$update->bind_param("ii", $_SESSION["user_id"], $_GET["id"]);
	$update->execute();
	$resolved = $update->affected_rows;
}

$retrieve = $con->prepare("SELECT id, name, mobile, assistance_type, reported_on, last_updated_on, status, latitude, longitude FROM incidents WHERE id = ?");
$retrieve->bind_param("i", $_GET["id"]);
$retrieve->execute();
$retrieve->bind_result($id, $name, $mobile, $asst_type, $reported, $updated, $status, $lat, $lng);
while($retrieve->fetch()){
	$asst_array = explode(",", $asst_type);
}
$retrieve->close();

if(!empty($updated)) {
	$retrieve = $con->prepare("SELECT u.name, t.name FROM incidents i, users u, users_type t WHERE i.id = ? AND i.last_updated_user = u.id AND u.user_type = t.id GROUP BY i.id");
	$retrieve->bind_param("i", $_GET["id"]);
	$retrieve->execute();
	$retrieve->bind_result($lastUser, $lastUserType);
	while($retrieve->fetch()){}
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>View Incident Report :: Crisis Management System</title>
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
    <!-- Loading Google Map API engine v3 -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false"></script>
    <script type="text/javascript" src="js/maplabel.js"></script>
    <script type="text/javascript" src="js/markerwithlabel.js"></script>
    <style>.labels{color:#ff0000;font-family:"Open Sans",sans-serif;font-size:15px;font-weight:500;text-align:center;width:30px;white-space:nowrap}</style>
    <!-- Map Script -->
    <script>
      var map;
      var marker = null;
      var mapTypeIds = [];
      for(var type in google.maps.MapTypeId) {
        mapTypeIds.push(google.maps.MapTypeId[type]);
      }
      mapTypeIds.push("OSM");
      var latLng = new google.maps.LatLng(<?php echo $lat . ", " . $lng; ?>);
      
      function initialize() {
        var mapOptions = {
          zoom: 18,
          minZoom: 11,
          maxZoom: 20,
          disableDefaultUI: false,
          center: latLng,
          mapTypeId: "OSM",
          mapTypeControlOptions: {
            mapTypeIds: mapTypeIds
          }
        };
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        map.mapTypes.set("OSM", new google.maps.ImageMapType({
          getTileUrl: function(coord, zoom) {
            return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
          },
          tileSize: new google.maps.Size(256, 256),
          name: "OpenStreetMap",
          maxZoom: 18
        }));
        
        var marker = new MarkerWithLabel({
            position: latLng,
            map: map,
            icon: {
                url: "images/pin.png"
            },
            draggable: false,
            raiseOnDrag: false,
            labelContent: <?php echo $id; ?>,
            labelAnchor: new google.maps.Point(15.5, 41),
            labelClass: "labels", // the CSS class for the label
            labelInBackground: false
        });
      }  
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/moment-duration-format.js"></script>
    <script>
var t1 = "<?php echo $reported; ?>";
t1 = t1.split(/[- :]/);
var rDate = new Date(t1[0], t1[1]-1, t1[2], t1[3], t1[4], t1[5]);
rDate = moment(rDate);

var t2 = "<?php echo $updated; ?>";
t2 = t2.split(/[- :]/);
var uDate = new Date(t2[0], t2[1]-1, t2[2], t2[3], t2[4], t2[5]);
uDate = moment(uDate);
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
      <?php
      if(isset($reopen)) {
      	if($reopen == 1) {
      		echo '<div class="alert alert-info" style="margin:10px 0 -5px 0;"><i class="fa fa-undo"></i> Incident re-opened successfully!</div>';
      	}
      	else {
      		echo '<div class="alert alert-danger" style="margin:10px 0 -5px 0;"><i class="fa fa-exclamation-triangle"></i> <b>ERROR RE-OPENING INCIDENT IN DATABASE. This incident may have been re-opened already. <a href="incident_details.php?id=' . $_GET["id"] . '" class="btn btn-primary btn-sm">Click here to refresh this page</a></b></div>';
      	}
      } elseif(isset($resolved)) {
      	if($resolved == 1) {
      		echo '<div class="alert alert-success" style="margin:10px 0 -5px 0;"><i class="fa fa-check"></i> Incident marked as RESOLVED successfully!</div>';
      	}
      	else {
      		echo '<div class="alert alert-danger" style="margin:10px 0 -5px 0;"><i class="fa fa-exclamation-triangle"></i> <b>ERROR UPDATING INCIDENT IN DATABASE. This incident may have been marked as RESOLVED already. <a href="incident_details.php?id=' . $_GET["id"] . '" class="btn btn-primary btn-sm">Click here to refresh this page</a></b></div>';
      	}
      }
	  ?>
        <!-- Page Heading -->
        <div class="row">
          <div class="col-lg-12 page-header"">
            <div class="col-lg-6" style="padding-left:0">
           <h1 style="margin:0">Viewing Incident Report #<?php echo $_GET["id"]; ?></h1>
           </div>
            <div class="col-lg-6" style="text-align: right;padding-right:0">
          <a href="view_reports.php" class="btn btn-primary"><i class="fa fa-arrow-circle-left"></i> &nbsp;Back</a>
          <?php if(isset($_GET["update"]) && $_GET["update"] == "true") { ?>
          <a href="incident_details.php?id=<?php echo $_GET["id"]; ?>&update=true" class="btn btn-success" style="margin-left:7px"><i class="fa fa-check"></i> &nbsp;SAVE CHANGES</a>
          <?php } else { ?>
          <a href="incident_details.php?id=<?php echo $_GET["id"]; ?>&update=true" class="btn btn-warning" style="margin-left:7px"><i class="fa fa-pencil"></i> &nbsp;Update Incident Details</a>
          <?php } ?>
          </div>
          </div>
        </div>
        <!-- /.row -->
        <?php if(!isset($_GET["update"]) || (isset($_GET["update"]) && $_GET["update"] != "true")) { ?>
                    <div class="row" style="margin-bottom:15px">
                    <div class="col-lg-2">
                      <label for="status">Incident Status:</label>&nbsp;&nbsp;
                      <?php if($status == "1") {echo "<span class='btn btn-primary btn-xs'>OPEN</span>";} 
                      elseif($status == "0") {echo "<span class='btn btn-success btn-xs'>RESOLVED</span>";} ?>
                      </div>
                      <div class="col-lg-10">
                      <label for="lastUpdated">Last updated on:</label> &nbsp;&nbsp;
                      <script>
                          if(moment(uDate).fromNow() == "Invalid date") {document.write("&mdash;");}
                          else { document.write(moment(uDate).fromNow()); }
                          </script><?php if(!empty($updated)) {echo " &nbsp;&nbsp;<span class='btn btn-default btn-xs'>" . $lastUser . " - " . $lastUserType . "</span>";} ?>
                      </div>
                    </div>
                    <?php } ?>
        <form role="form" action="incident_details.php?id=<?php $_GET["id"]; ?>" method="POST">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="name">Name of Caller</label>
                    <?php if(isset($_GET["update"]) && $_GET["update"] == "true") { ?>
        			<input class="form-control" id="name" name="name" data-validation="length" value="<?php echo $name; ?>" data-validation-length="2-50" />
                    <?php } else { ?>
                    <p class="form-control-static" id="name" name="name"><?php echo $name?></p>
                    <?php } ?>
                  </div>
                  <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <?php if(isset($_GET["update"]) && $_GET["update"] == "true") { ?>
        			<input class="form-control" id="contact" name="contact" type="number" value="<?php echo $mobile; ?>" data-validation="number" data-validation-allowing="range[80000000;99999999]" />
                    <?php } else { ?>
                    <p class="form-control-static" id="contact" name="contact"><?php echo $mobile?></p>
                    <?php } ?>
                  </div>
                  <div class="form-group">
                    <label>Type of Assistance</label>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value=""<?php if (in_array("1", $asst_array)) {echo " checked";} if(!isset($_GET["update"]) || (isset($_GET["update"]) && $_GET["update"] != "true")) {echo " disabled"; }?>>Emergency Ambulance
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value=""<?php if (in_array("2", $asst_array)) {echo " checked";} if(!isset($_GET["update"]) || (isset($_GET["update"]) && $_GET["update"] != "true")) {echo " disabled"; }?>>Rescue and Evacuation
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value=""<?php if (in_array("3", $asst_array)) {echo " checked";} if(!isset($_GET["update"]) || (isset($_GET["update"]) && $_GET["update"] != "true")) {echo " disabled"; }?>>Fire Fighting
                      </label>
                    </div>
                    <div class="form-group" style="text-align:center;">
                      <br /><br />
                      <?php if($status == "1") { ?>
                      <label for="timeTaken">Time Elapsed</label>
                      <p class="form-control-static" id="timeTaken" name="timeTaken" style="font-size:34px;padding-top:0;">
                      <script>
                      var duration = moment.duration(moment().diff(rDate));
                      document.write(duration.format("d[days] h[hr] mm[min] ss[sec]"));
                      </script>
					</p>
          			<p><br><a href="incident_details.php?id=<?php echo $_GET["id"]; ?>&resolved=true" class="btn-lg btn-success" style="margin-left:7px;text-decoration:none;"><i class="fa fa-check"></i> &nbsp;Click here to mark incident as RESOLVED</a></p>

                      <?php  } elseif($status == "0") {?>
                      <label for="timeTaken">Total Incident Duration</label>
                      <p class="form-control-static" id="timeTaken" name="timeTaken" style="font-size:34px;padding-top:0;">
                      <script>
                      var duration = moment.duration(uDate.diff(rDate));
                      document.write(duration.format("d[days] h[hr] mm[min] ss[sec]"));
                      </script>
                      </p>
          			<p><br><a href="incident_details.php?id=<?php echo $_GET["id"]; ?>&reopen=true" class="btn-lg btn-danger" style="margin-left:7px;text-decoration:none;"><i class="fa fa-undo"></i> &nbsp;Click here to RE-OPEN incident</a></p>
						<?php } ?>
						<br><br>
                    </div>
                  </div>
                </div>
                <!-- /.col-lg-6 (nested) -->
                <div class="col-lg-8">
                  <div class="row" style="margin-bottom:0!important;">
                    <div class="form-group col-lg-4">
                      <label for="latitude">Latitude:</label> <?php echo $lat; ?>
                    </div>
                    <div class="form-group col-lg-4">
                      <label for="longitude">Longitude:</label> <?php echo $lng; ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-lg-12">
                      <div id="map"></div>
                    </div>
                  </div>
                  <!-- /.col-lg-6 (nested) -->
                </div>
                <!-- /.row (nested) -->
                
          
          </div>
              <!-- /.panel-body -->
            </div>
        </form>
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
    
                    <?php if(isset($_GET["update"]) && $_GET["update"] == "true") { ?>
    <script src="js/form-validator/jquery.form-validator.min.js"></script>
    <script>
      /* important to locate this script AFTER the closing form element, so form object is loaded in DOM before setup is called */
      var myLanguage = {
        errorTitle: 'Form submission failed!',
        requiredFields: 'Please click on the map below to set a location',
        lengthBadStart: 'The name must be between ',
        lengthBadEnd: ' characters',
        badInt: 'The number must start with either "8" or "9" and have exactly 8 digits',
        badAlphaNumericExtra: ' and ',
        groupCheckedRangeStart: 'You must choose between ',
        groupCheckedEnd: ' type(s) of assistance'
      };
      $.validate({
        language : myLanguage
      });
    </script>
    <?php } ?>
  </body>
</html>