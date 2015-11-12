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
$con        = mysqli_connect("localhost", "root", "", "cms");
$asst_array = "";

/*-------------------------------updateIncident(name, mobile, locX, locY, location, assistance, status, lastUpdatedUser)----------------------------*/
if (isset($_POST['submit'])) {
  $name       = $_POST["name"];
  $mobile     = $_POST["contact"];
  $locX       = $_POST["latitude"];
  $locY       = $_POST["longitude"];
  $location   = trim($_POST["location"]);
  $assistance = implode(",", $_POST["assistance"]);
  $status     = $_POST["status"];
  $id         = $_GET["id"];
  $last_upd   = $_SESSION["user_id"];
  $update     = $con->prepare("UPDATE incidents SET name = ?, mobile = ?, latitude = ?, longitude = ?, location = ?, assistance_type = ?, last_updated_user = ?, status = ? WHERE id = ?");
  $update->bind_param("siddssiii", $name, $mobile, $locX, $locY, $location, $assistance, $last_upd, $status, $id);
  $rc = $update->execute();
  $update->close();
/*-----------------------------------------------------------End of update incident----------------------------------------------------------*/

 
  // if update is successful in database, update facebook and redirect user back to incident details page
  if (!false === $rc) {
  	/* -----------------------postFacebookStatus(locX, locY, location)-------------------------*/
  	$APP_ID     = '1515229708793971';
  	$APP_SECRET = 'dbbf3d1a9618eeb0575a724cd4bbedd0';
  	//token
  	$TOKEN      = "CAAViFZBiLuHMBAEcPDpgooqZBeap8Hwp4nmYqmlSH3RkKXFFj5r0uZB3Kub06fQEDkfxzBLx6po5LfZBihu4ZAL0LIqUkZBrucvyq5SospdtgZC1sPjyHOHHW5UE4XAc1D3HpxZCTbeWI2LPw4uVt76KvrpMJbvQBygNGji01ukWgjbHm1w1IU91x8X0KLMerPsZD";
  	$ID         = "1487065338263076"; // your id or facebook page id
  	FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);
  	$session = new FacebookSession($TOKEN);
  	$address = str_replace(' ', '+', $location);
 
  	// UPDATE FACEBOOK MESSAGE ACCORDING TO THE STATUS. 1 = RE-OPEN , 0 = MARKED AS RESOLVED
  	if($status == 1) {
  		$params  = array(
  			"message" => "Accident along " . $location,
  			"link" => "https://www.google.com/maps/place/" . $address . "/@" . $locX . "," . $locY . ",17z/"
  		);
  	} else {
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
    header("Location: incident_details.php?id=" . $_GET["id"] . "&update=true");
  }
} else {

  /*-----------------------------------------getIncidentDetails(id)-------------------------------------------*/
  $retrieve = $con->prepare("SELECT id, name, mobile, assistance_type, reported_on, last_updated_on, status, latitude, longitude, location FROM incidents WHERE id = ?");
  $retrieve->bind_param("i", $_GET["id"]);
  $retrieve->execute();
  $retrieve->bind_result($id, $name, $mobile, $asst_type, $reported, $updated, $status, $lat, $lng, $location);
  while ($retrieve->fetch()) {
    $asst_array = explode(",", $asst_type);
  }
  if (!empty($updated)) {
    $retrieve = $con->prepare("SELECT u.name, t.name FROM incidents i, users u, users_type t WHERE i.id = ? AND i.last_updated_user = u.id AND u.user_type = t.id GROUP BY i.id");
    $retrieve->bind_param("i", $_GET["id"]);
    $retrieve->execute();
    $retrieve->bind_result($lastUser, $lastUserType);
    while ($retrieve->fetch()) {
    }
  }
  $retrieve->close();
  /*-------------------------------------End of get incident details-----------------------------------------*/
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
    <title>Update Incident Report :: Crisis Management System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false"></script>
    <script type="text/javascript">
    var map;
    var marker = null;
    var mapTypeIds = [];
    var geocoder = new google.maps.Geocoder;
    for (var type in google.maps.MapTypeId) {
        mapTypeIds.push(google.maps.MapTypeId[type]);
    }
    mapTypeIds.push("OSM");
    var latLng = new google.maps.LatLng(<?php echo $lat . ", " . $lng; ?>);

    /*-------------------------------------------displayMap()---------------------------------------------*/
    function initialize() {
        var mapOptions = {
            zoom: 18,
            minZoom: 12,
            maxZoom: 20,
            disableDefaultUI: false,
            center: latLng,
            mapTypeId: "OSM",
            mapTypeControlOptions: {
                mapTypeIds: mapTypeIds
            }
        };
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        var marker = new google.maps.Marker({
            position: latLng,
            map: map
        });
        map.mapTypes.set("OSM", new google.maps.ImageMapType({
            getTileUrl: function(coord, zoom) {
                return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
            },
            tileSize: new google.maps.Size(256, 256),
            name: "OpenStreetMap",
            maxZoom: 18
        }));
        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });
        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();
            if (places.length == 0) {
                return;
            }
            if (marker) {
                marker.setMap(null);
                $("#latitude").val('');
                $("#longitude").val('');
            }
            // Clear out the old markers.
            markers.forEach(function(marker) {
                marker.setMap(null);
            });
            markers = [];
            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };
                var marker2 = new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                });
                // Create a marker for each place.
                markers.push(marker2);

                google.maps.event.addListener(marker2, 'click', function() {
                    $("#latitude").val(marker2.getPosition().toUrlValue().split(',')[0]);
                    $("#longitude").val(marker2.getPosition().toUrlValue().split(',')[1]);
                    $("#latitude").blur();
                    $("#longitude").blur();
                    $("#pac-input").val('');
                    if (marker) {
                        marker.setMap(null);
                    }
                    marker = new google.maps.Marker({
                        position: marker2.getPosition(),
                        map: map
                    });
                    geocodeLatLng(geocoder, marker2.getPosition().toUrlValue());
                });

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });

        google.maps.event.addListener(map, 'click', function(event) {
            //call function to create marker
            $("#latitude").val(event.latLng.toUrlValue().split(',')[0]);
            $("#longitude").val(event.latLng.toUrlValue().split(',')[1]);
            $("#latitude").blur();
            $("#longitude").blur();
            $("#pac-input").val('');
            if (marker) {
                marker.setMap(null);
            }
            marker = new google.maps.Marker({
                position: event.latLng,
                map: map
            });
            geocodeLatLng(geocoder, event.latLng.toUrlValue());
        });
    }
    /*---------------------------------------End of display map-----------------------------------------*/

    function geocodeLatLng(geocoder, myLatLng) {
        var latlngStr = myLatLng.split(',', 2);
        var latlng = {
            lat: parseFloat(latlngStr[0]),
            lng: parseFloat(latlngStr[1])
        };
        geocoder.geocode({
            'location': latlng
        }, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    document.getElementById("location").value = results[0].formatted_address;
                    document.getElementById("location").focus();
                    document.getElementById("location").blur();
                }
            }
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
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
          if(isset($_POST["submit"])) {
          		echo '<div class="alert alert-danger" style="margin:10px 0 -5px 0;"><i class="fa fa-exclamation-triangle"></i> <b>ERROR UPDATING INCIDENT IN DATABASE.</b></div>';
          }
          ?>
        <form role="form" action="update.php?id=<?php echo $_GET["id"]; ?>" method="POST">
          <div class="row">
            <div class="col-lg-12 col-sm-12 page-header" style="margin-top:10px">
              <div class="col-lg-6 col-sm-8" style="padding-left:0">
                <h1 style="margin:10px 0 0 0">Update Incident Report #<?php echo $_GET["id"]; ?></h1>
              </div>
              <div class="col-lg-6 col-sm-4" style="text-align: right;padding-right:0">
                <button onclick="if(confirm('Are you sure you want to discard changes for this report?')){window.location.href='incident_details.php?id=<?php echo $_GET["id"]; ?>';return false;}return false;" class="btn btn-danger" style="padding:8px 15px 4px;"><i class="fa fa-times" style="font-size:22px"></i><br>Cancel</button>
                <button type="submit" id="submit" name="submit" class="btn btn-success" style="padding:8px 30px 4px;"><i class="fa fa-check" style="font-size:22px"></i><br>Save Report</button>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="name">Name of Caller</label>
                    <input class="form-control" id="name" name="name" data-validation="length" value="<?php echo $name; ?>" data-validation-length="2-50" />
                  </div>
                  <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input class="form-control" id="contact" name="contact" type="number" value="<?php echo $mobile; ?>" data-validation="number" data-validation-allowing="range[80000000;99999999]" />
                  </div>
                  <div class="form-group">
                    <label for="location">Location</label>
                    <textarea class="form-control" id="location" name="location" data-validation="length" data-validation-length="2-255"><?php echo $location; ?></textarea>
                  </div>
                  <div class="form-group" style="margin-top:18px">
                    <label>Type of Assistance</label>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value="1" data-validation="checkbox_group" data-validation-qty="1-3"<?php if (in_array("1", $asst_array)) {echo " checked";} ?>>Emergency Ambulance
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value="2"<?php if (in_array("2", $asst_array)) {echo " checked";} ?>>Rescue and Evacuation
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value="3"<?php if (in_array("3", $asst_array)) {echo " checked";} ?>>Fire Fighting
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <br /><br />
                    <label for="status">Incident Status</label>
                    <select class="form-control" name="status" id="status">
                      <option value="1"<?php if($status == "1") { echo ' selected="selected"'; } ?>>Open</option>
                      <option value="0"<?php if($status == "0") { echo ' selected="selected"'; } ?>>Resolved</option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-8">
                  <div class="row" style="margin-bottom:0!important;">
                    <div class="form-group col-lg-8">
                      <label for="pac-input">Search</label>
                      <div class="form-group input-group">
                        <input id="pac-input" type="text" class="form-control" placeholder="Search for any location by typing here...">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><i class="fa fa-search"></i>
                        </button>
                        </span>
                      </div>
                    </div>
                    <div class="form-group col-lg-2">
                      <label for="latitude">Latitude</label>
                      <input class="form-control" type="text" id="latitude" name="latitude" style="cursor:not-allowed" data-validation="required" onclick="this.blur()" value="<?php echo $lat; ?>" />
                    </div>
                    <div class="form-group col-lg-2">
                      <label for="longitude">Longitude</label>
                      <input class="form-control" type="text" id="longitude" name="longitude" style="cursor:not-allowed" data-validation="required" onclick="this.blur()" value="<?php echo $lng; ?>" />
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
      </div>
    </div>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/form-validator/jquery.form-validator.min.js"></script>
    <script>
      /* important to locate this script AFTER the closing form element, so form object is loaded in DOM before setup is called */
      var myLanguage = {
        errorTitle: 'Form submission failed!',
        requiredFields: 'Please click on the map below to set a location',
        lengthBadStart: 'The length must be between ',
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
  </body>
</html>