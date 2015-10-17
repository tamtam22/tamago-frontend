<script>incident_count = 0;</script>
<table class="table table-bordered table-hover table-striped" style="font-size:16px">
  <thead>
    <tr>
      <th rowspan="2" width="1%" style="vertical-align:middle">#</th>
      <th rowspan="2" width="20%" style="vertical-align:middle;text-align:center">Reported by</th>
      <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><i class="fa fa-ambulance" style="font-size:24px"></i><br>Emergency Ambulance</th>
      <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><i class="fa fa-life-ring" style="font-size:24px"></i><br>Rescue and Evacuation</th>
      <th rowspan="1" width="15%" style="vertical-align:middle;text-align:center"><span class="glyphicon glyphicon-fire" style="font-size:24px"></span><br>Fire Fighting</th>
      <th rowspan="2" width="17%" style="vertical-align:middle;text-align:center">Reported on</th>
      <th rowspan="2" width="17%" style="vertical-align:middle;text-align:center">Time Elapsed</th>
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
        $retrieve = $con->prepare("SELECT id, name, mobile, latitude, longitude, assistance_type, reported_on FROM incidents WHERE status = 1 ORDER BY id DESC");
        $retrieve->execute();
        $retrieve->bind_result($id, $name, $mobile, $lat, $lng, $asst_type, $reported);
        while ($row = $retrieve->fetch()){
        $assistance = explode(",", $asst_type);
        $dt = new DateTime($reported);
        ?>
    <tr onclick="mymap2.setCenter(new google.maps.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));mymap2.setZoom(17); return false" style="cursor:pointer">
      <td><?php echo $id; ?></td>
      <td style="text-align:center"><?php echo $name . " (" . $mobile . ")"; ?></td>
      <td style="text-align:center"><?php if(in_array("1",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
      <td style="text-align:center"><?php if(in_array("2",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
      <td style="text-align:center"><?php if(in_array("3",$assistance)) { ?><i class="fa fa-check" style="font-size:24px"></i><?php } ?></td>
      <td style="text-align:center"><?php echo date("h:i A",strtotime($reported)) . "<br><span style='font-size:12px'>(" . date("d M Y",strtotime($reported)); ?>)</span></td>
      <td style="text-align:center"><b><?php echo time_ago($dt); ?></b></td>
      <script>incident_count++;</script>
    </tr>
    <?php
      }
      $retrieve->close();
      /* close all connections when done */
      $con->close();
      ?>
  </tbody>
</table>