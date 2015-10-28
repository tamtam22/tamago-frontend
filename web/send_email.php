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
while ($retrieve->fetch()) {
	// do your code here
	// put into table format etc etc
}
$retrieve->close();

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
$mail->addAddress('presleylim92@gmail.com');               // Name is optional

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Status Report';
$mail->Body    = 'PSI Status <br> North: '.$northPSI.'<br>South: '.$southPSI.'<br>Central: '.$centralPSI.'<br>East: '.$eastPSI.'<br>west: '.$westPSI.'<br><br><br>Incident Report<br>Accident Location: <br>Assistance: ';

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