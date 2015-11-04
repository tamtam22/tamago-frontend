<?php
#index.php of our root

require_once("facebook/autoload.php"); // set the right path
 
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;
 
 
$APP_ID = '1515229708793971';
$APP_SECRET = 'dbbf3d1a9618eeb0575a724cd4bbedd0';
//token
$TOKEN = "CAAViFZBiLuHMBAEcPDpgooqZBeap8Hwp4nmYqmlSH3RkKXFFj5r0uZB3Kub06fQEDkfxzBLx6po5LfZBihu4ZAL0LIqUkZBrucvyq5SospdtgZC1sPjyHOHHW5UE4XAc1D3HpxZCTbeWI2LPw4uVt76KvrpMJbvQBygNGji01ukWgjbHm1w1IU91x8X0KLMerPsZD";
$ID = "1487065338263076"; // your id or facebook page id
 
FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);
 
$session = new FacebookSession($TOKEN);
 
$params = array(
  "message" => $name,
  "link" => "http://maps.google.com/maps?q=" . $locX . "," . $locY . "&z=20"
);
 
if($session){
  try {
    $response = (new FacebookRequest(
    $session, 'POST', '/'.$ID.'/feed', $params
    ))->execute()->getGraphObject();
  } catch(FacebookRequestException $e) {
        echo "Exception occured, code: " . $e->getCode();
        echo " with message: " . $e->getMessage();
  }
}
?>