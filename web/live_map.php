<html>
<head>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <style type="text/css">body,html{height:100%;margin:0;padding:0}#map{height:600px}.labels{color:#ff0000;font-family:"Open Sans",sans-serif;font-size:15px;font-weight:500;text-align:center;width:30px;white-space:nowrap}</style>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="js/maplabel.js"></script>
    <script type="text/javascript" src="js/markerwithlabel.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script>
        var myArr = [];
        var mymap;
        var incident_count = 0;
        var incident_count2 = -1;
        $(document).ready(function() {
            $.ajax({
                type: "GET",
                url: "http://www.nea.gov.sg/api/WebAPI?dataset=psi_update&keyref=781CF461BB6606ADBC7C75BF9D4F60DB2676ABFA7BD37F6E",
                dataType: "xml",
                success: xmlParser
            });

            var xmlhttp = new XMLHttpRequest();
            var url = "incidents_json.php";

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    myArr = JSON.parse(xmlhttp.responseText);
                }
            }
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
        });
        //setup PSI here
        var psi = [];
        var lati, lngi, psiValue;

        function xmlParser(xml) {
            $(xml).find("region").each(function() {
                lati = $(this).find("latitude").text();
                lngi = $(this).find("longitude").text();

                $(this).find('record').each(function() {
                    psiValue = $(this).find("reading[type='NPSI']").attr("value");
                });
                psi.push([lati, lngi, psiValue]);
            });
            initialize();
        }
    </script>
    <script type="text/javascript">
        var labelObjects = [],
            mymap2;

        function initialize() {
            var element = document.getElementById("map");
            var mapTypeIds = [];
            for (var type in google.maps.MapTypeId) {
                mapTypeIds.push(google.maps.MapTypeId[type]);
            }
            mapTypeIds.push("OSM");

            var mymap = new google.maps.Map(element, {
                center: new google.maps.LatLng(1.354625, 103.818740),
                zoom: 11,
                minZoom: 11,
                mapTypeId: "OSM",
                mapTypeControlOptions: {
                    mapTypeIds: mapTypeIds
                }
            });
            mymap.mapTypes.set("OSM", new google.maps.ImageMapType({
                getTileUrl: function(coord, zoom) {
                    return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
                },
                tileSize: new google.maps.Size(256, 256),
                name: "OpenStreetMap",
                maxZoom: 18
            }));
            var color, stroke;
            for (x = 0; x < psi.length; x++) {
                if (psi[x][2] < 100) {
                    color = "#00FF14";
                    stroke = "#000000"
                } else if (psi[x][2] < 300) {
                    color = "#ffff00";
                    stroke = "#000000";
                } else {
                    color = "#ffff00";
                    stroke = "#FF1717";
                }
                labelObjects[x] = new MapLabel({
                    text: psi[x][2],
                    position: new google.maps.LatLng(psi[x][0], psi[x][1]),
                    map: mymap,
                    fontSize: 32,
                    align: 'center',
                    fontColor: color,
                    strokeColor: stroke,
                    strokeWeight: 9
                });
                labelObjects[x].set('position', new google.maps.LatLng(psi[x][0], psi[x][1]));
            }
            mymap2 = mymap;

            //create a single InfoWindow-instance for all markers
            if(!mymap.get('infoWin')){mymap.set('infoWin',new google.maps.InfoWindow({
                pixelOffset: new google.maps.Size(0, -40)
              }));}
            // Place markers on map
            for (b = 0; b < myArr.length; b++) {
                for (var key in myArr[b]) {
                    var latLng = new google.maps.LatLng(myArr[b]["latitude"], myArr[b]["longitude"]);
                    var displayField = "";
                    var t1 = myArr[b]["reported_on"].split(/[- :]/);
                    var rDate = new Date(t1[0], t1[1]-1, t1[2], t1[3], t1[4], t1[5]);
                    
                    var assType = myArr[b]["assistance_type"].split(",");
                    var assTypeDefine = "";
                    var counter = 0;
                    for (i = 0; i < assType.length; i++) {
						if(assType[i] == "1") {
							assTypeDefine += "<i class='fa fa-ambulance'></i> &nbsp;Emergency Ambulance";
							counter++;
						} else if(assType[i] == "2") {
							if(counter > 0) {assTypeDefine += "<br>";}
							assTypeDefine += "<i class='fa fa-life-ring'></i> &nbsp;Resuce and Evacuation";
							counter++;
						} else {
							if(counter > 0) {assTypeDefine += "<br>";}
							assTypeDefine += "<i class='glyphicon glyphicon-fire'></i> &nbsp;Fire Fighting";
						}
                    }
                    
                    displayField += "<table style='display:block'><tr><td>Reported by:</td><td><b>" + myArr[b]["name"] + " (" + myArr[b]["mobile"] + ")</b></td></tr>";
                    displayField += "<tr><td>Reported on:&nbsp;&nbsp;&nbsp;</td><td><b>" + moment(rDate).calendar() + "</b></td></tr>";
                    displayField += "<tr><td valign='top' style='padding:12px 0 0 0;'>Assistance Requested:&nbsp;&nbsp;&nbsp;</td><td style='padding:10px 0;line-height:25px;'><b>" + assTypeDefine + "</b></td></tr>";
                    if(myArr[b]["last_updated_on"] != null) {
                        var t2 = myArr[b]["reported_on"].split(/[- :]/);
                        var uDate = new Date(t2[0], t2[1]-1, t2[2], t2[3], t2[4], t2[5]);
                    	displayField += "<tr><td>Last updated on:&nbsp;&nbsp;&nbsp;</td><td><b>" + moment(uDate).calendar() + "</b></td></tr></table>";
                    }
                    else {
                    	displayField += "<tr><td>Last updated on:&nbsp;&nbsp;&nbsp;</td><td><b>-</b></td></tr></table>";
                    }
					
                    var marker = new MarkerWithLabel({
                        position: latLng,
                        infoWinContent: displayField,
                        map: mymap,
                        icon: {
                            url: "images/pin.png"
                        },
                        draggable: false,
                        raiseOnDrag: false,
                        labelContent: myArr[b]["id"],
                        labelAnchor: new google.maps.Point(15.5, 41),
                        labelClass: "labels", // the CSS class for the label
                        labelInBackground: false
                    });

                    google.maps.event.addListener(marker, "click", function(e) {
                        this.getMap().get('infoWin').setOptions({
                            map:this.getMap(),
                            position:this.getPosition(),
                            //set the content
                            content:this.get('infoWinContent')
                          })
                    });

                    google.maps.event.addListener(marker, "dblclick", function(e) {
                        mymap2.setCenter(this.position);
                        mymap2.setZoom(17);
                    });
                }
            }

            function pinSymbol(color) {
                return {
                    path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
                    fillColor: color,
                    fillOpacity: 1,
                    strokeColor: '#000',
                    strokeWeight: 2,
                    scale: 1.4
                };
            }

        }

        function toggleP() {
            if (labelObjects[0].getMap()) {
                labelObjects[0].setMap(null);
                labelObjects[1].setMap(null);
                labelObjects[2].setMap(null);
                labelObjects[3].setMap(null);
                labelObjects[4].setMap(null);
                labelObjects[5].setMap(null);
                document.getElementById('toggle').innerHTML = "<i class='fa fa-check-square-o'></i>&nbsp; SHOW PSI VALUE";
                document.getElementById('toggle').className = "btn btn-md btn-success pull-right";
            } else {
                labelObjects[0].setMap(mymap2);
                labelObjects[1].setMap(mymap2);
                labelObjects[2].setMap(mymap2);
                labelObjects[3].setMap(mymap2);
                labelObjects[4].setMap(mymap2);
                labelObjects[5].setMap(mymap2);
                document.getElementById('toggle').innerHTML = "<i class='fa fa-times'></i>&nbsp; HIDE PSI VALUE";
                document.getElementById('toggle').className = "btn btn-md btn-danger pull-right";
            }
        }
    </script>
</head>

<body>
    <div id="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <a style="margin-left:5px" class="btn btn-md btn-primary pull-right" onclick="mymap2.setCenter(new google.maps.LatLng(1.354625,103.818740));mymap2.setZoom(11);">RESET ZOOM</a>
                    <a id="toggle" class="btn btn-md btn-danger pull-right" onclick="toggleP()"><i class='fa fa-times'></i>&nbsp; HIDE PSI VALUE</a>
                    <br>
                    <br>
                    <div id="map"></div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="alert alert-info" style="text-align:center;padding:5px 0;font-size:16px;"><b>TIP:</b> Double-click on any pins above/incident below to zoom in to incident location</div>
                            <div class="table-responsive" id="live-table">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            refreshTable();
        });

        function refreshTable() {
            $('#live-table').load('incidents_ajax.php', function() {
                setTimeout(refreshTable, 1000);
            });
        }
        $(document).ajaxComplete(function() {
            if (incident_count2 == -1) {
                incident_count2 = incident_count;
            } else {
                if (incident_count != incident_count2) {
                    location.reload();
                }
            }
        });
    </script>
</body>

</html>