<?php
define('WP_USE_THEMES', false);
require("../../../wp-blog-header.php"); 
status_header(200);
nocache_headers();
require_once('class.phpmailer.php');
include_once('class.smtp.php');
require('send_confirmation.php');
//require('bookingclass.php');

/*
Simple IPN processing script
based on code from the "PHP Toolkit" provided by PayPal
*/

// Send


$url = 'https://www.paypal.com/cgi-bin/webscr';
$postdata = '';
foreach($_POST as $i => $v) {
	$postdata .= $i.'='.urlencode($v).'&';
}
$postdata .= 'cmd=_notify-validate';

$web = parse_url($url);
if ($web['scheme'] == 'https') { 
	$web['port'] = 443;  
	$ssl = 'ssl://'; 
} else { 
	$web['port'] = 80;
	$ssl = ''; 
}
$fp = @fsockopen($ssl.$web['host'], $web['port'], $errnum, $errstr, 30);

if (!$fp) { 
	echo $errnum.': '.$errstr;
} else {
	fputs($fp, "POST ".$web['path']." HTTP/1.1\r\n");
	fputs($fp, "Host: ".$web['host']."\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $postdata . "\r\n\r\n");

	while(!feof($fp)) { 
		$info[] = @fgets($fp, 1024); 
	}
	fclose($fp);
	$info = implode(',', $info);
	if (eregi('VERIFIED', $info)) { 
		
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
		$id=$postdata['option_selection1'];
		$update=$wpdb->query("update $table_request set payment_status = 'Completed' where id_request = '$_POST[option_selection1]'");
	
		// yes valid, f.e. change payment status and send confirmation voucher 
		send_confirmation($_POST['option_selection1']);


		
	} else {
		// invalid, log error or something
		$to      = get_bloginfo('admin_email');
		$subject = get_bloginfo('url').' IPN script error'.print_r($_POST);
		$message = get_bloginfo('url').' dormireinbedandbreakfast fault'.$_POST['st'];
		$headers = 'From: '.get_bloginfo('admin_email'). '\r\n'.
		    'Reply-To: '.get_bloginfo('admin_email'). '\r\n'.
		    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

	}
}
?>