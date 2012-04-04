<?php

function send_confirmation($id)
{
global $wpdb;
$table_request = $wpdb->prefix . "request";
$request= $wpdb->get_row("select * from $table_request where id_request ='$id'");

$header= get_option('bookandpay_header');
			//$messaggio= $templaterow->welcome;
			$welcome = __('Confirmation booking / conferma booking: '.$request->post_name.'  |  voucher n.'.$request->id_request.'-'.substr($request->magic_string,0,6), 'sendit').' from '.get_bloginfo('home');
			$messaggio= $intro_message;
			$messaggio.='<h3>Booking confirmation - voucher</h3><hr />';
			$messaggio.='<p>Please, print this page or simply note this voucher code / Stampare questa pagina o segnarsi il codice seguente: '.$request->id_request.'-'.substr($request->magic_string,0,6).'</p><hr />';
			
			$messaggio.='<h4>Voucher n: '.$request->id_request.'-'.substr($request->magic_string,0,6).'</h4>';	
			
			$messaggio.='<h3>Customer Details</h3>';

			$messaggio.='<p><strong>Name / Nome:</strong> '.$request->contactname.'</p>';
			$messaggio.='<p><strong>email:</strong> '.$request->email.'</p>';
			$messaggio.='<p><strong>Phone / telefono:</strong> '.$request->phonenumber.'</p>';
			
			$details='<h3><strong>Bed and breakfast:</strong><a href="http://'.$request->post_url.'">'.$request->post_name.'</a></h3>';
			$details.='<p><strong>checkin:</strong> '.$request->checkin.'</p>';
			$details.='<p><strong>checkout:</strong> '.$request->checkout.'</p>';
			$details.='<p><strong>people / persone:</strong> '.$request->people.'</p>';
			$details.='<p><strong>room type / tipo camera:</strong> '.$request->room.'</p>';
			$details.='<p><strong>Rooms / numero camere:</strong> '.$request->room_number.'</p>';
			$details.='<p><strong>Total / PricePrezzo del soggiorno:</strong>'.$request->total_price.'</p>';
			$details.='<p><strong>Advance payed / Acconto pagato:</strong> '.$request->advanced_price.'</p>';
			
			$saldo=$request->total_price-$request->advanced_price;
			
			$details.='<p><strong>To pay at your arrival / Saldo dovuto (al vostro arrivo):</strong> '.$saldo.'</p>';

			$details.='<p><strong>Notes / Note:</strong> '.$request->notes.'</p>';
			$details.='<p><strong>Advance Payment status:</strong> '.$request->payment_status.'</p>';
			
			
			//b&b details
			$details.='<h3>Bed and Breakfast Details / Dettagli bed and breakfast</h3>';
			$nomevero=get_post_meta($request->post_id, 'bookandpay_owner_truename', true);
			$telefono = get_post_meta($request->post_id, 'bookandpay_owner_phone', true);
			$email_owner = get_post_meta($request->post_id, 'bookandpay_emailowner', true);
			$indirizzo = get_post_meta($request->post_id, 'bookandpay_owner_address', true);
			$note = get_post_meta($request->post_id, 'bookandpay_owner_notes', true);
			
			
			$details.='<p><strong>B&amp;B real Name / Nome struttura: </strong>'.$nomevero.'</p>';
			$details.='<p><strong>Phone / Telefono: </strong>'.$telefono.'</p>';
			$details.='<p><strong>Address / indirizzo: </strong>'.$indirizzo.'</p>';
			$details.='<p><strong>email: </strong>'.$email_owner.'</p>';

			$details.='<p><strong>notes / note aggiuntive: </strong>'.$note.'</p>';
			
			$details.='<p><small>Please contact these numbers to give your arrival informations and details to the owner: '.$telefono.'</small></p>';
			$details.='<p><small>Si prega di contattare i seguenti numeri di telefono per comunicare orari / dettagli: '.$telefono.'</small></p>';

			
			
			$footer= get_option('bookandpay_footer');
			
			$content_full = $header.$messaggio.$email.$details.$link_booking.$footer;
			#### Creo object PHPMailer e imposto le COSTANTI SMTP PHPMAILER
		
			require_once('class.phpmailer.php');
			require_once('class.smtp.php');
		
			$mail = new PHPMailer();

			if(get_option('bookandpay_smtp_host')!='') :	
			//print_r($mail);
				$mail->IsSMTP(); // telling the class to use SMTP
				
				
				$mail->Host = get_option('bookandpay_smtp_host'); // Host
				$mail->Hostname = get_option('bookandpay_smtp_hostname');// SMTP server hostname
				$mail->Port  = get_option('bookandpay_smtp_port');// set the SMTP port
				
				if(get_option('bookandpay_smtp_username')!=''):	
					$mail->SMTPAuth = true;     // turn on SMTP authentication
					$mail->Username = get_option('bookandpay_smtp_username');  // SMTP username
					$mail->Password = get_option('bookandpay_smtp_password'); // SMTP password
				else :
					$mail->SMTPAuth = false;// disable SMTP authentication
				endif;
			endif;
			
			$mail->SetFrom(get_option('bookandpay_fromemail'));
			//$mail->AddReplyTo('pinobulini@gmail.com');
			$mail->Subject ='[Booking confirmation] - '.$welcome;
			$mail->AltBody = " To view the message, please use an HTML compatible email viewer!";
			// optional, comment out and test
			$mail->MsgHTML($content_full);
			 //admin
			$mail->AddAddress(get_option('bookandpay_fromemail'));
			 //cliente
			$mail->AddBCC($request->email, $request->contactname);
			 //proprietario
			$mail->AddBCC($request->post_email, $request->post_email);
			 
			if($mail->Send()) : 
				//echo $successo ; 
			 //	echo 'invio email riuscito';
			 else : 
			 	
			 	//echo $errore; 
			 //	echo 'Invio email fallito';
			 endif;
			//notifica a admin
			$mail->ClearAddresses();

}
?>