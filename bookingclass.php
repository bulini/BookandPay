<?php

class booking{

	function GetMessages($id_request)
	{
		global $wpdb;
		$table_messages = $wpdb->prefix . "booking_messages";
		$messaggi=$wpdb->get_results("select * from $table_messages where id_request = $id_request");	
		return $messaggi;
	}
	
	function GetAllrequest()
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
		$requests=$wpdb->get_results("select * from $table_request");	
		return $requests;
	}
	
	function GetrequestbyId($id)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
		$request=$wpdb->get_row("select * from $table_request where id_request=$id");	
		return $request;
	}
	
	function NewRequest()
	{
			

			
			
			load_plugin_textdomain('bookandpay', PLUGINDIR.'/languages/'.dirname(plugin_basename(__FILE__)));
			//print_r($_POST);
			
		    global $_POST;
			global $wpdb;
			require_once('class.phpmailer.php');
			include_once('class.smtp.php');
			require_once('datetime.php');

			$id_struttura=icl_object_id($_POST['post_id'],'page',true,'it');


			//google short
			include('google_url.php');
		
			$key = 'AIzaSyCG4gdxpovYfTxGMIIfPo7jGsvQzAneIXY';
		
			$googler = new GoogleURLAPI($key);	
		    
			$table_name_request = $wpdb->prefix . "request";
		    
			//messaggio di errore email
			$errore_mail=__(get_option('bookandpay_mailerror'));
		
			$errore_generico='<div id="message" class="error"><p>'.__(get_option('bookandpay_formerror')).'</div>';
		 	$errore_disponibilita='<div id="message" class="error"><p><strong>'.__('Room not available for your dates','bookandpay').'</strong></p></div>';
		   
		    if(isset($_POST['email'])):   
			
			//setcookie("bookandpay_checkin", $_POST['checkin'], time()+3600);
			$checkin = itdate_php_to_mysql($_POST['checkin']);
			$checkout = itdate_php_to_mysql($_POST['checkout']);
			
				if($_POST['people']=="" or $_POST['checkin']=="" or $_POST['checkout']=="" or $_POST['contactname']=="" or $_POST['contactsurname']=="" or $_POST['phonenumber']=="") :
		
					die($errore_generico);
				elseif (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $_POST['email'])) :
					die($errore_mail);		
				else:
						
						//verifico se fare instant booking col plugin ON availability
						include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
						if(is_plugin_active('bookandpay-availability/bookandpay-availability.php')):
				
							require_once(ABSPATH . 'wp-content/plugins/bookandpay-availability/bookandpay-availability.php');
							
							if(!AppartamentoDisponibileInUnPeriodo($id_struttura,$checkin,$checkout)):
								die($errore_disponibilita);
							endif;	
							$prezzo=CalcolaPrezzo($id_struttura,$checkin,$checkout,$_POST['people']);
							$advance=($prezzo/2);	
							echo $prezzo;
										
						endif;
							
							$now=date("Y-m-d H:i:s",time());
							//genero stringa univoca x conferme sicure
							$code = md5(uniqid(rand(), true));
						
							if($prezzo>0) { $status='instant_booking';} else { $status='pending';}
			 				
			 				
			 				$wpdb->query("INSERT INTO $table_name_request (contactname,contactsurname,email,phonenumber,post_id,post_name,post_url,post_email,people,room,room_number,checkin,checkout, notes,magic_string,created_at,payment_status,total_price,advanced_price) VALUES ('$_POST[contactname]','$_POST[contactsurname]', '$_POST[email]', '$_POST[phonenumber]', '$id_struttura','$_POST[post_name]','$_POST[post_url]','$_POST[post_email]', '$_POST[people]', '$_POST[room]','$_POST[room_number]','$checkin', '$checkout', '$_POST[notes]','$code','$now','$status','$prezzo','$advance')");
			 				   
			 			/*qui mando email*/
						
						
						
					
							$handler_link=get_option('siteurl').'/booking/?mode=owner&bid='.$code;
							$customer_handler_link=get_option('siteurl').'/booking/?mode=customer&bid='.$code.'&lang='.getActiveLanguage();
		
							$header= get_option('bookandpay_header');
							//$messaggio= $templaterow->welcome;
							$subject = __('struttura '.$_POST['post_name'].' n: '.substr($code,0,6)." from:", 'bookandpay').get_bloginfo('home');
							$welcome.= "<h3>".$subject."</h3>";
							$welcome.=str_replace('{NAME}',$_POST['contactname'],get_option('bookandpay_firstmail'));
							$messaggio='<p><strong>Nome:</strong> '.$_POST['contactname'].'</p>';		
							$messaggio.='<p><strong>Cognome:</strong> '.$_POST['contactsurname'].'</p>';
							$email='<p><strong>Telefono:</strong> '.$_POST['phonenumber'].'</p>';
							$email.='<p><strong>email:</strong> '.$_POST['email'].'</p>';
							$dettagli='<p><strong>Struttura:</strong> '.$_POST['post_name'].'</p>';
							$dettagli.='<p><strong>checkin:</strong> '.$_POST['checkin'].'</p>';
							$dettagli.='<p><strong>checkout:</strong> '.$_POST['checkout'].'</p>';
							$dettagli.='<p><strong>people</strong> '.$_POST['people'].'</p>';
							$dettagli.='<p><strong>rooms:</strong> '.$_POST['room'].'</p>';
							$dettagli.='<p><strong>numero camere:</strong> '.$_POST['room_number'].'</p>';
							$dettagli.='<p><strong>Note:</strong> '.$_POST['notes'].'</p>';
		
							// Test: Shorten a URL
							
							//$short_link = $googler->shorten($handler_link);
							$short_link = $handler_link;
							//echo $short_link; // returns http://goo.gl/i002
							
							$owner_link='<h3>Per gestire la richiesta e rispondere cliccare sul seguente link</h3>';
		
							$owner_link.='<p><a href="'.$short_link.'">Gestisci richiesta</a></p>';
		
							$customer_instant_link='<p><a class="bookandpay_button" href="'.$customer_handler_link.'">Conferma prenotazione</a></p>';
		
		
							$footer= get_option('bookandpay_footer');
							
							$content_send = $header.$welcome.$messaggio.$email.$dettagli.$footer;
							$content_for_owner = $header.$messaggio.$dettagli.$owner_link.$footer;
		
							#### Creo object PHPMailer e imposto le COSTANTI SMTP PHPMAILER
							$mail = new PHPMailer();
		
							if(get_option('bookandpay_smtp_host')!='') :	
							//print_r($mail);
								$mail->IsSMTP(); // telling the class to use SMTP
								
								
								$mail->Host = get_option('bookandpay_smtp_host'); // Host
								$mail->Port  = get_option('bookandpay_smtp_port');// set the SMTP port
								
								if(get_option('bookandpay_smtp_username')!=''):	
									$mail->SMTPAuth = true;     // turn on SMTP authentication
									$mail->Username = get_option('bookandpay_smtp_username');  // SMTP username
									$mail->Password = get_option('bookandpay_smtp_password'); // SMTP password
									$mail->SMTPSecure = "tls";
									//pino 2011 da fare
		
								else :
									$mail->SMTPAuth = false;// disable SMTP authentication
								endif;
							endif;
							
							$mail->SetFrom(get_option('bookandpay_fromemail'));
							//$mail->AddReplyTo('pinobulini@gmail.com');
							$mail->Subject = '[your request] - '.$subject;
							$mail->AltBody = " To view the message, please use an HTML compatible email viewer!";
							// optional, comment out and test
							$mail->MsgHTML($content_send);
							 
							 $admin_mail_message = __('New booking request from ', 'bookandpay').get_bloginfo('blog_name');		 
							
							
						
							 $mail->AddAddress($_POST['email']);
							
							if($status!='instant_booking'):
							
							if($mail->Send()) : 
								$mail->ClearAddresses();
								//invio a admin
								$mail->SetFrom(get_option('bookandpay_fromemail'));
		
								$mail->AddAddress(get_option('bookandpay_fromemail'));
								//$mail->AddBCC('pinobulini@gmail.com');
								
								
								$mail->Subject = '[admin] - '.$subject;
								$mail->AltBody =" To view the message, please use an HTML compatible email viewer!"; 
								// optional, comment out and test
								$admin_message=$header.$welcome.$messaggio.$email.$dettagli.$owner_link.$footer;
								$mail->MsgHTML($admin_message);
								$mail->Send();
									
								if($_POST['post_email']) :
								$mail->ClearAddresses();
		
								//il from è l'indirizzo generico del sito web es booking@
								$mail->SetFrom(get_option('bookandpay_fromemail'));
								//notifica a proprietario struttura
										$mail->AddAddress($_POST['post_email']);
										$mail->Subject = 'Nuova richiesta info per'.$subject;
										$mail->AltBody = __('new request for you: ', 'bookandpay').get_bloginfo('blog_name'); 
										// optional, comment out and test
										$mail->MsgHTML($content_for_owner);
										$mail->Send();
								$mail->ClearAddresses();
								endif;
								//echo $successo ; 
							    //messaggio di successo
						
								$booking=new booking();
								
				
								
		
							 else : 
							 	
							 	//echo $errore; 
							 	die($errore);
							 endif;
							//notifica a admin del sito
							endif;
							
							endif;
							
							if($prezzo>0):
									$successo='<div id="message" class="available">
										<p>'.__('The room you requested is available', 'bookandpay').'<br />'.__('Price', 'bookandpay').'<strong> &euro;'.$prezzo.'</strong>
										<br />
										
										Per confermare subito la vostra prenotazione &egrave; necessario pagare subito ed in modalit&agrave; sicura un anticipo del 50% pari a: <strong>&euro;'.($prezzo/2).'</strong> '.$customer_instant_link.'</p></div>';
							else:
									$successo=__(get_option('bookandpay_mailsuccess'));
							endif;
							 	die($successo);
					
		    
		    endif;
		
		
	}
				

	
	
	
	function SetPayment($id)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
   		$update=$wpdb->query("update $table_request set payment_status = 'Completed-advance' where id_request = $id"); 
		return true;
	}
	
	function ChangeStatus($id,$status)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
   		$update=$wpdb->query("update $table_request set payment_status = '$request' where id_request = $id"); 
		return true;
	}
	
	function GetrequestbyCode($code)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
		$request=$wpdb->get_row("select * from $table_request where magic_string='".$code."'");	
		return $request;
	}
	
	function is_expired($code)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
		$now=$wpdb->get_var("select SYSDATE()");
		$request=$this->GetrequestbyCode($code);			
		$diff=$wpdb->get_var("SELECT TIMEDIFF('$now','$request->quoted_date')");
		if($diff>'12:00:00'):		
			return true;
		endif;
	}
	
	
	function DeleteRequest($id)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
			$update=$wpdb->query("delete from $table_request where id_request = $id"); 
		return true;
	}
	
	/* markup functions */
	
	function BuildMessageArea($id_request)
	
	{
		$messages=$this->GetMessages($id_request);
		$html='';
		
		foreach($messages as $message):
			$html.='<div style="background:#efefef;border:1px solid #ccc;padding:5px;" id="message-'.$message->id_message.'" class="message">';
				$html.='<p>da: '.$message->msg_from.' a: '.$message->msg_to.'</p>';
				$html.='<p>messaggio: '.$message->message.' created at: '.$message->created_at.'</p>';
			$html.='</div>';
		endforeach;
			
		$html.='<p><label for="customer_notes">Messaggio:</label></p>
						<textarea name="customer_notes" cols="50"></textarea>';
		
		$html.='<input type="submit" name="invia" value="go" />';				
		
		
		echo $html;
	}
	
	
	function get_others($email_owner)
	{
		$my_others=get_posts('post_type=accommodations&meta_key=bookandpay_emailowner&meta_value='.$email_owner); 
		return $my_others;
		wp_reset_query();
	}

}
?>