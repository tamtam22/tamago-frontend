
<?php
require 'PHPMailer/PHPMailerAutoload.php';
// get PSI
$url_psi = "http://www.nea.gov.sg/api/WebAPI?dataset=psi_update&keyref=781CF461BB6606ADBC7C75BF9D4F60DB2676ABFA7BD37F6E";
$xml_psi = simplexml_load_string(file_get_contents($url_psi));
$northPSI = $xml_psi->item[0]->region[0]->record[0]->reading[0]['value'];
$southPSI = $xml_psi->item[0]->region[5]->record[0]->reading[0]['value'];
$eastPSI = $xml_psi->item[0]->region[3]->record[0]->reading[0]['value'];
$westPSI = $xml_psi->item[0]->region[4]->record[0]->reading[0]['value'];
$centralPSI = $xml_psi->item[0]->region[2]->record[0]->reading[0]['value'];

$con = mysqli_connect("localhost", "root", "", "cms");
// get all the accidents which are ongoing, showing the latest one on top
$retrieve = $con->prepare("SELECT id, name, mobile, location, assistance_type, reported_on FROM incidents WHERE status = 1 ORDER BY id DESC");
$retrieve->execute();
$retrieve->bind_result($id, $name, $mobile, $location, $asst_type, $reported);
$body = null;

// PSI Table
$body .= '<table style="width:20%">';
$body .= '<tr><td colspan="5"><h2>PSI Reading</h2></td></tr>';
$body .= '<tr style="font-weight:bold;text-align:center">';
$body .= '<td>North</td>';
$body .= '<td>South</td>';		
$body .= '<td>East</td>';
$body .= '<td>West</td>';
$body .= '<td>Central</td>';
$body .= '</tr>'; 

$body .= '<tr style="text-align:center">';
$body .= '<td>'.$northPSI.'</td>';
$body .= '<td>'.$southPSI.'</td>';		
$body .= '<td>'.$eastPSI.'</td>';
$body .= '<td>'.$westPSI.'</td>';
$body .= '<td>'.$centralPSI.'</td>';
$body .= '</tr>'; 
$body .= '</table><br><br>';

//Incident Report Table 
$body .= '<table style="width:100%">';
$body .= '<tr><td colspan="6"><h2>Incident Report</h2></td></tr>';
$body .= '<tr>';
$body .= '<td style="width:20px"><b>ID</b></td>';
$body .= '<td><b>Name of Caller</b></td>';		
$body .= '<td><b>Mobile Number</b></td>';
$body .= '<td><b>Location of Incident</b></td>';
$body .= '<td><b>Assistance Type(s)</b></td>';
$body .= '<td><b>Reported Time</b></td>';
$body .= '</tr>';

$Id = 1;


while ($retrieve->fetch()) :
$asst_name = null;
if($asst_type != null){
	$asst_array = (explode(",",$asst_type));
	if(in_array("1",$asst_array)){
		$asst_name = $asst_name."[Ambulance]  ";
	}
	
	if(in_array("2",$asst_array)){
		$asst_name = $asst_name."[Rescue & Evacuation]  ";
	}
	
	if(in_array("3",$asst_array)){
		$asst_name = $asst_name."[Fire Fighting]  ";
	}
}
$body .= '<tr>';
$body .= '<td>'.$Id.'</td>';
$body .= '<td>'.$name.'</td>';		
$body .= '<td>'.$mobile .'</td>';
$body .= '<td>'.$location.'</td>';
$body .= '<td>'.$asst_name.'</td>';
$body .= '<td>'.$reported.'</td>';
$body .= '</tr>';
  
$Id = $Id + 1;

endwhile;
$retrieve->close();
$body .= '</table>';
$mail = new PHPMailer;
//$mail->SMTPDebug = 3;                               // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'presleylim92@gmail.com';                 // SMTP username
$mail->Password = '32presleyzx';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->setFrom('presleylim92@gmail.com', 'TamagoCMS');
$mail->addAddress('zach.junwei@hotmail.com');               // Name is optional

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Status Report';
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$mail->Body    = $body;

if($mail->send())
{
	$insert = $con->prepare("INSERT INTO email_log (receipient_id) VALUES (?);");
	$pmo = 2;
	$insert->bind_param("i", $pmo);
	$insert->execute();
	$rows = $insert->affected_rows;
	
	if ($rows == 1) {
		echo "Added to email log.<br>";
	}
	else {
		echo "Failed to add into email log<br>";
	}
	echo "Email sent successfully!";
}
else
{
	echo "Error sending: " . $mail->ErrorInfo;
}
$insert->close();
$con->close();
?>
