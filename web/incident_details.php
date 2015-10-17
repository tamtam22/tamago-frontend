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
    <!-- Map Script -->
    <script>
      var map;
      var marker = null;
      var mapTypeIds = [];
      for(var type in google.maps.MapTypeId) {
        mapTypeIds.push(google.maps.MapTypeId[type]);
      }
      mapTypeIds.push("OSM");
      function initialize() {
        var mapOptions = {
          zoom: 12,
          minZoom: 12,
          maxZoom: 20,
          disableDefaultUI: false,
          center: new google.maps.LatLng(1.354625,103.818740), //center on Singapore
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
      }  
      google.maps.event.addDomListener(window, 'load', initialize);
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
              View Incident Report
            </h1>
          </div>
        </div>
        <!-- /.row -->
        <form role="form" action="incident_details.php" method="POST">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="name">Name of Caller</label>
                    <p class="form-control-static" id="name" name="name">Tan Ah Kow</p>
                  </div>
                  <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <p class="form-control-static" id="contact" name="contact">89894545</p>
                  </div>
                  <div class="form-group">
                    <label>Type of Assistance</label>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value="" disabled>Emergency Ambulance
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value="" checked disabled>Rescue and Evacuation
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                      <input type="checkbox" name="assistance[]" value="" disabled>Fire Fighting
                      </label>
                    </div>
                    <div class="form-group">
                      <br /><br />
                      <label for="status">Incident Status</label>
                      <select class="form-control" name="status" id="status">
                        <option>Open</option>
                        <option>In Progress</option>
                        <option>Pending</option>
                        <option>Closed</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <br />
                      <label for="timeTaken">Time Elapsed</label>
                      <p class="form-control-static" id="timeTaken" name="timeTaken">2 hour 49 mins</p>
                    </div>
                    <div class="form-group">
                      <br />
                      <label for="lastUpdated">Last Updated on</label>
                      <p class="form-control-static" id="lastUpdated" name="lastUpdated">1 hour 15 mins ago</p>
                    </div>
                  </div>
                </div>
                <!-- /.col-lg-6 (nested) -->
                <div class="col-lg-8">
                  <div class="row" style="margin-bottom:0!important;">
                    <div class="form-group col-lg-2">
                      <label for="pac-input">Location</label>
                    </div>
                    <div class="form-group col-lg-4">
                      <label for="latitude">Latitude</label>
                      <p class="form-control-static" type="text" id="latitude" name="latitude">123.456789</p>
                    </div>
                    <div class="form-group col-lg-4">
                      <label for="longitude">Longitude</label>
                      <p class="form-control-static" type="text" id="longitude" name="longitude">123.456789</p>
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
                <div class="form-group col-lg-6" style="margin-top:15px"><button type="submit" class="btn btn-lg btn-success btn-block"><i class="fa fa-check-square-o"></i>&nbsp; Update</button></div>
                <div class="form-group col-lg-6" style="margin-top:15px"><button type="submit" onclick="location.href='view_reports.php';return false;" class="btn btn-lg btn-danger btn-block"><i class="fa fa-times"></i>&nbsp; Cancel</button></div>
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
  </body>
</html>