
<?php
require 'PHPMailer/PHPMailerAutoload.php';
require_once("facebook/autoload.php");
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

/*-----------------------------------------getPSIValue()---------------------------------------------*/
// get PSI
$url_psi = "http://www.nea.gov.sg/api/WebAPI?dataset=psi_update&keyref=781CF461BB6606ADBC7C75BF9D4F60DB2676ABFA7BD37F6E";
$xml_psi = simplexml_load_string(file_get_contents($url_psi));
$northPSI = $xml_psi->item[0]->region[0]->record[0]->reading[0]['value'];
$southPSI = $xml_psi->item[0]->region[5]->record[0]->reading[0]['value'];
$eastPSI = $xml_psi->item[0]->region[3]->record[0]->reading[0]['value'];
$westPSI = $xml_psi->item[0]->region[4]->record[0]->reading[0]['value'];
$centralPSI = $xml_psi->item[0]->region[2]->record[0]->reading[0]['value'];
/*-------------------------------------End of get PSI value------------------------------------------*/

/* -----------------------postFacebookStatus(northPSI, southPSI, eastPSI, westPSI, centralPSI)-------------------------*/
    $APP_ID     = '1515229708793971';
    $APP_SECRET = 'dbbf3d1a9618eeb0575a724cd4bbedd0';
    //token
    $TOKEN      = "CAAViFZBiLuHMBAEcPDpgooqZBeap8Hwp4nmYqmlSH3RkKXFFj5r0uZB3Kub06fQEDkfxzBLx6po5LfZBihu4ZAL0LIqUkZBrucvyq5SospdtgZC1sPjyHOHHW5UE4XAc1D3HpxZCTbeWI2LPw4uVt76KvrpMJbvQBygNGji01ukWgjbHm1w1IU91x8X0KLMerPsZD";
    $ID         = "1487065338263076"; // your id or facebook page id
    FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);
    $session = new FacebookSession($TOKEN);
    $params  = array(
      "message" => "Current 3-hr PSI values: \n" .
      				"North: " . $northPSI . " PSI\n" .
      				"South: " . $southPSI . " PSI\n" .
      				"East: " . $eastPSI . " PSI\n" .
      				"West: " . $westPSI . " PSI\n" .
      				"Central: " . $centralPSI . " PSI"
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
?>
