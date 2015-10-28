<?php
	include ( "src/NexmoMessage.php" );
	/**
	 * To send a text message.
	 *
	 */
	// Step 1: Declare new NexmoMessage.
	$nexmo_sms = new NexmoMessage('4161573d', 'f580506f');
	
	// Step 2: Use sendText( $to, $from, $message ) method to send a message. 
	$info = $nexmo_sms->sendText( '6581885992', 'TamagoCMS', 'Emergency!' );
	
	// Step 3: Display an overview of the message
	echo $nexmo_sms->displayOverview($info);
	
	// Done!
?>