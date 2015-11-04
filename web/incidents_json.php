<?php
/*-------------------------------------------getAllOpenIncidents()---------------------------------------------*/
$connection = mysqli_connect("localhost", "root", "", "cms") or die("Error " . mysqli_error($connection));
$sql = "SELECT id, name, latitude, longitude, mobile, assistance_type, reported_on, last_updated_on FROM incidents WHERE status=1";
$result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
$emparray[] = array();
while ($row = mysqli_fetch_assoc($result)) {
  $emparray[] = $row;
}
echo json_encode($emparray);
mysqli_close($connection);
/*-----------------------------------------End of get all open incidents---------------------------------------*/
?>