<?php
include("../../../wp-blog-header.php"); 
require_once('class.phpmailer.php');
include_once('class.smtp.php');
require('send_confirmation.php');

// PHP 4.1

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp) {
// HTTP ERROR
} else {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);
if (strcmp ($res, "VERIFIED") == 0) {
// check the payment_status is Completed
// check that txn_id has not been previously processed
// check that receiver_email is your Primary PayPal email
// check that payment_amount/payment_currency are correct
// process payment

				global $wpdb;
				$table_request = $wpdb->prefix . "request";
				$id=$postdata['option_selection1'];
				$update=$wpdb->query("update $table_request set payment_status = 'Completed' where id_request = '$_POST[option_selection1]'");

				// yes valid, f.e. change payment status  
		/*
				$to = "pinobulini@gmail.com";
				$subject = "Test mail OK";
				$message = "Hello! This is a simple email message. OK ".$_POST[''];
				$from = "info@dormireinbedandbreakfasti.com";
				$headers = "From: $from";
				mail($to,$subject,$message,$headers);
				echo "Mail Sent.";
		*/
				send_confirmation($_POST['option_selection1']);
		/*
				$booking=new booking();
				$booking->SetPayment($postdata['option_selection1']);
		*/

}
else if (strcmp ($res, "INVALID") == 0) {
// log for manual investigation
		// invalid, log error or something
		$to      = 'pinobulini@gmail.com';
		$subject = 'test paypal KO'.print_r($_POST);
		$message = 'hello'.$_POST['st'];
		$headers = 'From: webmaster@allbbrome.com' . "\r\n" .
		    'Reply-To: webmaster@example.com' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);


}
}
fclose ($fp);
}
?>
