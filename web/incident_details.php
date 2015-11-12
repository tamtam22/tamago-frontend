<?php
session_start();
require_once("facebook/autoload.php");
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

if (!isset($_SESSION["user_id"])) {
  header('Location: login.php');
  exit();
}
if (!isset($_GET["id"])) {
  header("Location: view_reports.php");
}
$asst_array = "";
$con        = mysqli_connect("localhost", "root", "", "cms");

/*-----------------------------------------getIncidentDetails(id)-------------------------------------------*/
$retrieve = $con->prepare("SELECT i.id, i.name, i.mobile, i.assistance_type, i.reported_on, i.last_updated_on, i.status, i.latitude, i.longitude, i.location, u.name FROM incidents i, users u WHERE i.id = ? AND i.operator = u.id GROUP BY i.id");
$retrieve->bind_param("i", $_GET["id"]);
$retrieve->execute();
$retrieve->bind_result($id, $name, $mobile, $asst_type, $reported, $updated, $status, $lat, $lng, $location, $operator);
while ($retrieve->fetch()) {
	$asst_array = explode(",", $asst_type);
}
$retrieve->close();
if (!empty($updated)) {
	$retrieve = $con->prepare("SELECT u.name, t.name FROM incidents i, users u, users_type t WHERE i.id = ? AND i.last_updated_user = u.id AND u.user_type = t.id GROUP BY i.id");
	$retrieve->bind_param("i", $_GET["id"]);
	$retrieve->execute();
	$retrieve->bind_result($lastUser, $lastUserType);
	while ($retrieve->fetch()) {
	}
	$retrieve->close();
}
/*-------------------------------------End of get incident details-----------------------------------------*/

$locX = $lat;
$locY = $lng;

/*------------------------------------------reopenIncident(id)----------------------------------------------*/
if (isset($_GET["reopen"]) && $_GET["reopen"] == "true") {
  $update = $con->prepare("UPDATE incidents SET status = 1, last_updated_user = ? WHERE id = ?");
  $update->bind_param("ii", $_SESSION["user_id"], $_GET["id"]);
  $update->execute();
  $reopen = $update->affected_rows;
  $update->close();
  if($reopen == 1) {
  	/* -----------------------postFacebookStatus(locX, locY, location)-------------------------*/
  	$APP_ID     = '1515229708793971';
  	$APP_SECRET = 'dbbf3d1a9618eeb0575a724cd4bbedd0';
  	//token
  	$TOKEN      = "CAAViFZBiLuHMBAEcPDpgooqZBeap8Hwp4nmYqmlSH3RkKXFFj5r0uZB3Kub06fQEDkfxzBLx6po5LfZBihu4ZAL0LIqUkZBrucvyq5SospdtgZC1sPjyHOHHW5UE4XAc1D3HpxZCTbeWI2LPw4uVt76KvrpMJbvQBygNGji01ukWgjbHm1w1IU91x8X0KLMerPsZD";
  	$ID         = "1487065338263076"; // your id or facebook page id
  	FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);
  	$session = new FacebookSession($TOKEN);
  	$address = str_replace(' ', '+', $location);
  	
  	$params  = array(
  			"message" => "Accident along " . $location,
  			"link" => "https://www.google.com/maps/place/" . $address . "/@" . $locX . "," . $locY . ",17z/"
  	);
  	if ($session) {
  		try {
  			$response = (new FacebookRequest($session, 'POST', '/'.$ID.'/feed', $params))->execute()->getGraphObject();
  		}
  		catch (FacebookRequestException $e) {
  			echo "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
  		}
  	}
  	/* -------------------------------End of Facebook------------------------------------------*/
  }
  $status = 1;
}
/*----------------------------------------End of reopen incident--------------------------------------------*/

/*------------------------------------------closeIncident(id)-----------------------------------------------*/
if (isset($_GET["resolved"]) && $_GET["resolved"] == "true") {
  $update = $con->prepare("UPDATE incidents SET status = 0, last_updated_user = ? WHERE id = ?");
  $update->bind_param("ii", $_SESSION["user_id"], $_GET["id"]);
  $update->execute();
  $resolved = $update->affected_rows;
  $update->close();
  if($resolved == 1) {
  	/* -----------------------postFacebookStatus(locX, locY, location)-------------------------*/
  	$APP_ID     = '1515229708793971';
  	$APP_SECRET = 'dbbf3d1a9618eeb0575a724cd4bbedd0';
  	//token
  	$TOKEN      = "CAAViFZBiLuHMBAEcPDpgooqZBeap8Hwp4nmYqmlSH3RkKXFFj5r0uZB3Kub06fQEDkfxzBLx6po5LfZBihu4ZAL0LIqUkZBrucvyq5SospdtgZC1sPjyHOHHW5UE4XAc1D3HpxZCTbeWI2LPw4uVt76KvrpMJbvQBygNGji01ukWgjbHm1w1IU91x8X0KLMerPsZD";
  	$ID         = "1487065338263076"; // your id or facebook page id
  	FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);
  	$session = new FacebookSession($TOKEN);
  	$address = str_replace(' ', '+', $location);
  	
  	$params  = array(
  			"message" => "Accident along " . $location . ", has been resolved",
  			"link" => "https://www.google.com/maps/place/" . $address . "/@" . $locX . "," . $locY . ",17z/"
  	);
  }
  	if ($session) {
  		try {
  			$response = (new FacebookRequest($session, 'POST', '/'.$ID.'/feed', $params))->execute()->getGraphObject();
  		}
  		catch (FacebookRequestException $e) {
  			echo "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
  		}
  	}
  	/* -------------------------------End of Facebook------------------------------------------*/
  $status = 0;
}
/*-----------------------------------------End of close incident--------------------------------------------*/

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
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>.labels{color:#ff0000;font-family:"Open Sans",sans-serif;font-size:15px;font-weight:500;text-align:center;width:30px;white-space:nowrap}</style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false"></script>
    <script type="text/javascript" src="js/maplabel.js"></script>
    <script type="text/javascript" src="js/markerwithlabel.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/moment-duration-format.js"></script>
    <script type="text/javascript">
    var map;
    var marker = null;
    var mapTypeIds = [];
    for (var type in google.maps.MapTypeId) {
        mapTypeIds.push(google.maps.MapTypeId[type]);
    }
    mapTypeIds.push("OSM");
    var latLng = new google.maps.LatLng(<?php echo $lat . ", " . $lng; ?>);

    /*-------------------------------------------displayMap()---------------------------------------------*/
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
    /*---------------------------------------End of display map-----------------------------------------*/
    
    google.maps.event.addDomListener(window, 'load', initialize);

    moment.locale('en', {
        calendar: {
            lastDay: '[Yesterday at] LT',
            sameDay: '[Today at] LT',
            nextDay: '[Tomorrow at] LT',
            lastWeek: '[Last] dddd [at] LT',
            nextWeek: 'dddd [at] LT',
            sameElse: 'DD MMM YYYY [-] LT'
        }
    });
    <?php if($status == 1) { ?>
    var myVar = setInterval(myTimer, 1000);

    function myTimer() {
        var duration = moment.duration(moment().diff(rDate));
        document.getElementById("timeTaken").innerHTML = duration.format("d[days] h[hr] mm[min] ss[sec]");
    }
    <?php } ?>

    var t1 = "<?php echo $reported; ?>";
    t1 = t1.split(/[- :]/);
    var rDate = new Date(t1[0], t1[1] - 1, t1[2], t1[3], t1[4], t1[5]);
    rDate = moment(rDate);

    var t2 = "<?php echo $updated; ?>";
    t2 = t2.split(/[- :]/);
    var uDate = new Date(t2[0], t2[1] - 1, t2[2], t2[3], t2[4], t2[5]);
    uDate = moment(uDate);
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
          <a href="index.php" style="text-align:center"><i class="fa fa-fw fa-dashboard"></i><br>Map</a>
        </li>
        <?php if($_SESSION["user_type"] != "3") { ?>
        <li>
          <a href="create.php" style="text-align:center"><i class="fa fa-fw fa-edit"></i><br>New Report</a>
        </li>
        <?php } ?>
        <li>
          <a href="view_reports.php" class="active" style="text-align:center"><i class="fa fa-flag"></i><br>View All Reports</a>
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
          } elseif(isset($_GET['update']) && $_GET['update'] == "true") {
          		echo '<div class="alert alert-success" style="margin:10px 0 -5px 0;"><i class="fa fa-check"></i> Incident updated successfully!</div>';
          }
        ?>
        <div class="row">
          <div class="col-lg-12 col-sm-12 page-header" style="margin-top:10px">
            <div class="col-lg-6 col-sm-8" style="padding-left:0">
              <h1 style="margin:0 0 10px 0">Viewing Incident Report #<?php echo $_GET["id"]; ?></h1>
              <label for="status">Status:</label>&nbsp;&nbsp;
              <?php if($status == "1") {echo "<span class='btn btn-primary btn-xs'>OPEN</span>";} 
                elseif($status == "0") {echo "<span class='btn btn-success btn-xs'>RESOLVED</span>";} ?>
            </div>
            <div class="col-lg-6 col-sm-4" style="text-align: right;padding-right:0">
              <a href="view_reports.php" class="btn btn-primary" style="padding:8px 15px 4px;"><i class="fa fa-arrow-circle-left" style="font-size:22px"></i><br>Back</a>
              <a href="update.php?id=<?php echo $_GET["id"]; ?>" class="btn btn-warning" style="padding:8px 30px 4px;"><i class="fa fa-pencil" style="font-size:22px"></i><br>Update Incident</a>
            </div>
          </div>
        </div>
        <form role="form" action="incident_details.php?id=<?php $_GET["id"]; ?>" method="POST">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="name">Name of Caller</label>
                    <p class="form-control-static" id="name" name="name"><?php echo $name?></p>
                  </div>
                  <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <p class="form-control-static" id="contact" name="contact"><?php echo $mobile?></p>
                  </div>
                  <div class="form-group">
                    <label for="location">Location</label>
                    <p class="form-control-static" id="location" name="location"><?php echo $location?></p>
                  </div>
                  <div class="form-group" style="margin-top:18px">
                    <label>Type of Assistance</label>
                    <div class="checkbox">
                      <label style="cursor:text">
                      <input type="checkbox" name="assistance[]" value=""<?php if (in_array("1", $asst_array)) {echo " checked";} ?> disabled>Emergency Ambulance
                      </label>
                    </div>
                    <div class="checkbox">
                      <label style="cursor:text">
                      <input type="checkbox" name="assistance[]" value=""<?php if (in_array("2", $asst_array)) {echo " checked";} ?> disabled>Rescue and Evacuation
                      </label>
                    </div>
                    <div class="checkbox">
                      <label style="cursor:text">
                      <input type="checkbox" name="assistance[]" value=""<?php if (in_array("3", $asst_array)) {echo " checked";} ?> disabled>Fire Fighting
                      </label>
                    </div>
                  </div>
                  <div class="form-group panel panel-default" style="text-align:center;margin-top:24px;height:180px;">
                    <?php if($status == "1") { ?>
                    <div class="panel-heading"><b>Time Elapsed</b></div>
                    <div class="panel-body" style="padding-bottom:0">
                      <p class="form-control-static" id="timeTaken" name="timeTaken" style="font-size:34px;padding:0;margin:0;"><img src="images/load.gif" alt="Loading..."/>
                      </p>
                      <p style="margin:0"><br><a href="incident_details.php?id=<?php echo $_GET["id"]; ?>&resolved=true" class="btn-lg btn-success" style="margin-left:7px;text-decoration:none;"><i class="fa fa-check"></i> &nbsp;Mark as Resolved</a></p>
                    </div>
                    <?php  } elseif($status == "0") {?>
                    <div class="panel-heading"><b>Total Incident Duration</b></div>
                    <div class="panel-body" style="padding-bottom:0">
                      <p class="form-control-static" id="timeTaken" name="timeTaken" style="font-size:34px;padding:0;margin:0;">
                        <script>
                          var duration = moment.duration(uDate.diff(rDate));
                          document.write(duration.format("d[days] h[hr] mm[min] ss[sec]"));
                        </script>
                      </p>
                      <p><br><a href="incident_details.php?id=<?php echo $_GET["id"]; ?>&reopen=true" class="btn-lg btn-danger" style="margin-left:7px;text-decoration:none;"><i class="fa fa-undo"></i> &nbsp;Re-open Incident</a></p>
                    </div>
                    <?php } ?>
                    <br><br>
                  </div>
                </div>
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
                </div>
              </div>
            </div>
        </form>
        </div>
        <div class="col-lg-12" style="padding:0">
          <table class="table table-hover">
            <tr>
              <td style="width:20%;border-top:none!important;"><b>Created by</b></td>
              <td style="border-top:none!important;">
                <?php echo $operator . " on "; ?><script>document.write(rDate.format("DD MMM YYYY, h:mm:ss a"));</script>
              </td>
            </tr>
            <tr>
              <td><b>Last updated on</b></td>
              <td>
                <script>
                  if(moment(uDate).fromNow() == "Invalid date") {document.write("&mdash;");}
                  else { document.write(moment(uDate).fromNow()); }
                </script><?php if(!empty($updated)) {echo " &nbsp;&nbsp;(by " . $lastUser . " - " . $lastUserType . ")";} ?>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>