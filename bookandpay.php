<?php
/*
Plugin Name: BookandPay!
Plugin URI: http://www.giuseppesurace.com/
Description: Book and Pay 12/11/2010
Version: 0.1
Author: Giuseppe Surace
Author URI: http://www.giuseppesurace.com
*/
	

require('install-core.php');
require('admin-core.php');
require('bookingclass.php');
//require('paypal-ipn.php');
load_plugin_textdomain('bookandpay', PLUGINDIR.'/languages/'.dirname(plugin_basename(__FILE__)));

wp_register_sidebar_widget('booking_form', 'booking', 'Bookingform', get_the_ID());

	
define("BOOKANDPAY_ROOT", "/" . plugin_basename( dirname(__FILE__) ) . "/");
define("BOOKANDPAY_FULLURL", WP_PLUGIN_URL . BOOKANDPAY_ROOT );


register_activation_hook(__FILE__,'bookandpay_install');
add_action('wp_head', 'bookandpay_pushsack');
add_action('admin_menu', 'bookandpay_admin_pages');
add_filter('the_content', 'BookingView');

add_action('admin_head', 'admin_register_head');
add_action('admin_head', 'bookandpay_pushsack');

function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/bookandpay.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}





function bookandpay_pushsack() // sack jquery ui
{
  // uso JavaScript SACK library per Ajax
  wp_print_scripts( array( 'sack','jquery','jquery-ui-core'));
?>
<link type="text/css" href="<?php echo BOOKANDPAY_FULLURL ?>ui.all.css" rel="stylesheet" />
<link type="text/css" href="<?php echo BOOKANDPAY_FULLURL ?>bookandpay.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo BOOKANDPAY_FULLURL ?>ui.datepicker.js"></script>
<script type="text/javascript">
	var $j=jQuery.noConflict();
	$j(function() {
		//$j(".datepicker").datepicker({ dateFormat: 'dd/mm/yy',appendText: '(dd/mm/yyyy)' });
		var dates = $j( "#checkin, #checkout" ).datepicker({
			dateFormat: 'dd/mm/yy',
			//appendText: '(dd/mm/yyyy)',
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "checkin" ? "minDate" : "maxDate",
					instance = $j( this ).data( "datepicker" );
					date = $j.datepicker.parseDate(
						instance.settings.dateFormat ||
						$j.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
		
	});
	
    $j(document).ready(function(){
	$j('#payment_status').change(function(){
            var available = $j('#payment_status').val();
             if(available =='nondisponibile')
             {
                  $j('#others').removeClass('hidden');
			  }else
			  
			  {$j('#others').addClass('hidden');
                         }
        });
       
      }); 

	
</script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#booking_submit').click(function(){
		jQuery.ajax({
		beforeSend: function() { jQuery('#wait').show(); jQuery('#booking_submit').hide();},
        complete: function() { jQuery('#wait').hide(); jQuery('#booking_submit').show(); },

		type: "POST",
      	data: ({
      		contactname : jQuery('#contactname').val(),
      		contactsurname : jQuery('#contactsurname').val(),
      		email : jQuery('#email').val(),
      		phonenumber : jQuery('#phonenumber').val(),
      		post_id : jQuery('#post_id').val(),
      		post_name : jQuery('#post_name').val(),
      		post_url : jQuery('#post_url').val(),
      		post_email : jQuery('#post_email').val(),
      		people : jQuery('#people').val(),
      		room : jQuery('#room').val(),
      		room_number : jQuery('#room_number').val(),
      		checkin : jQuery('#checkin').val(),
      		checkout : jQuery('#checkout').val(),
      		notes : jQuery('#notes').val()
      		
      		}),  		
      	url: '<?php echo BOOKANDPAY_FULLURL ?>submit_request.php',
  		success: function(data) {
    	jQuery('#booking_response').html(data);
    	jQuery('#bookingform').addClass('hidden');
    	//alert(data);
    	jQuery('#bookingform').removeClass('hidden');

  }
});
	});
});

</script>


<script type="text/javascript">
function clearText(thefield){
if (thefield.defaultValue==thefield.value)
thefield.value = ""
} 
</script>

<?php
} // fine PHP function PushSack + javascript clear field


function BookingView($content)
{	
	$booking=new booking();
	global $wpdb;
	$table_request = $wpdb->prefix . "request";
	$table_messages = $wpdb->prefix . "booking_messages";
	$now = date('Y-m-d H:i:s', time());
	//echo $now;
	$find='[booking_handler]';
	
	switch ($_GET['mode']) {
	    case 'owner':
	    	  
	    //cancellazione provamoce
	    if($_POST['delete'] && $_POST['id_request']):   
			$delete=$wpdb->query("delete from $table_request where id_request = '$_POST[id_request]'"); 		
			echo '<div id="message" class="updated fade"><p><strong>'.__("Request deleted succesfully!", "bookandpay").'</strong></p></div>';   
	   		//print_r($_POST);
		endif;
    
    //modifica status e invio email di notifica
    if($_POST['id_request']): 
    //print_r($_POST); 
   		
$advance=($_POST['total_price']*30)/100;
$now=date("Y-m-d H:i:s",time());
		

   		$update=$wpdb->query("update $table_request set advanced_price = '$advance', total_price='$_POST[total_price]', payment_status = '$_POST[payment_status]', owner_notes='$_POST[owner_notes]', quoted_date='$now' where id_request = $_POST[id_request]"); 

   		
   		$request=$booking->GetrequestbyId($_POST[id_request]);

		if($_POST['send_email']==1)
		{
			
			if($_POST['owner_notes']!='')
				{
				//print_r($_POST);
				$wpdb->query("insert into $table_messages (id_message,id_request,msg_from,msg_to,message) VALUES (NULL,'$request->id_request','$request->post_name','$request->contactname','$_POST[owner_notes]')");
				}
			//	$welcome.=str_replace('{NAME}',$_POST['contactname'],get_option('bookandpay_firstmail'));
			
			if($_POST['payment_status']=='conversation')
			{ 
				$intro_message=str_replace('{NAME}',$request->contactname,get_option('bookandpay_mailavailable')); 							$show_details=TRUE;
			 }
			if($_POST['payment_status']=='nondisponibile')
			{ 
				if(isset($_POST['my_others']))
				{
					$other_house=get_post($_POST['my_others']);
					$alternative='<h3>We are also owner of this location available for your dates, take a look and if you like send us the request!</h3>';
					$alternative.='<h4><a href="'.get_permalink($other_house->ID).'">'.$other_house->post_title.'</a></h4>';
					$alternative.='<h5>'.get_the_term_list( $other_house->ID, 'areas', 'Rome: ', ', ', '' ). ' - '.get_the_term_list( $other_house->ID, 'type', '', ', ', '' ).'</h5>';
					$alternative.='<a href="'.get_permalink($other_house->ID).'"><img src="'.get_bloginfo('url').'/wp-content/gallery/'.$other_house->ID.'/thumbs/thumbs_1.jpg" alt="'.$other_house->post_title.'" border="0" /></a>';
					$alternative.=better_excerpt('300','...','','','1', ''); 
					
				}
				$intro_message=str_replace('{NAME}',$request->contactname,get_option('bookandpay_mailunavailable'));
				$show_details=FALSE;
			
			}
	
		
			$header= get_option('bookandpay_header');
						//$messaggio= $templaterow->welcome;
						$welcome = __('ref. request for '.$request->post_name.' n.'.substr($request->magic_string,0,6), 'bookandpay').' from '.get_bloginfo('home');
						$messaggio= str_replace('{STRUTTURA}',$request->post_name, $intro_message);
$intro_message;
						$messaggio.=$alternative;
						
						//mostro i dettagli solo in caso di accettazione
						if($show_details) :						
							$messaggio='<p><strong>Nome:</strong> '.$request->contactname.'</p>';

							$messaggio.='<p><strong>Cognome:</strong> '.$request->contactsurname.'</p>';
							$email='<p><strong>telefono:</strong> '.$request->phonenumber.'</p>';

							$email.='<p><strong>email:</strong> '.$request->email.'</p>';
							$details='<p><strong>Struttura:</strong> '.$request->post_name.'</p>';
							$details.='<p><strong>checkin:</strong> '.$request->checkin.'</p>';
							$details.='<p><strong>checkout:</strong> '.$request->checkout.'</p>';
							$details.='<p><strong>persone:</strong> '.$request->people.'</p>';
							$details.='<p><strong>Rooms/apt:</strong> '.$request->room.'</p>';
							$details.='<p><strong>numero camere:</strong> '.$request->room_number.'</p>';
							$details.='<p><strong>Prezzo totale:</strong>'.$request->total_price.'</p>';
							$details.='<p><strong>Acconto:</strong> '.$request->advanced_price.'</p>';
							$details.='<p><strong>Note:</strong> '.$request->notes.'</p>';
							if($request->owner_notes!=''):
							    $details.='<p><strong>Owner notes:</strong> '.$request->owner_notes.'</p>';
							endif;
							$link_booking='<hr /><p>To confirm your reservation you have to send the advance of  '.$request->advanced_price.' <b>within 12 hours</b> click the link below:</p>';
							$link_booking.='<p>Per vedere i dettagli dell\'offerta e confermare la prenotazione deve cliccare sul seguente link: <b>entro 12 ore</b> e versare l\'anticipo di  '.$request->advanced_price.':</p>';

							$link_booking.='<p><a href="'.get_option('home').'/'.get_option('bookandpay_bookingpage').'?mode=customer&bid='.$request->magic_string.'&lang='.getActiveLanguage().'">Confirm your reservation  / Conferma Prenotazione</a></p>';
							
							$disclaimer=get_option('bookandpay_mail_disclaimer');
							
							
						endif;
						$footer= get_option('bookandpay_footer');
						
						$content_full = $header.$intro_message.$messaggio.$link_booking.$email.$details.$disclaimer.$footer;
						$content_censored = $header.$messaggio.$details.$link_booking.$disclaimer.$footer;
						#### Creo object PHPMailer e imposto le COSTANTI SMTP PHPMAILER
					
						require_once('class.phpmailer.php');
						require_once('class.smtp.php');
					
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
							//$mail->SMTPSecure = "tls";
							//pino 2011 da fare

						else :
							$mail->SMTPAuth = false;// disable SMTP authentication
						endif;
					endif;

						
						$mail->SetFrom(get_option('bookandpay_fromemail'));
						//$mail->AddReplyTo('pinobulini@gmail.com');
						$mail->Subject ='[your reservation] - '.$welcome;
						$mail->AltBody = " To view the message, please use an HTML compatible email viewer!";
						// optional, comment out and test
						$mail->MsgHTML($content_full);
						 
						 $admin_mail_message = __('New booking request: ', 'bookandpay'). get_bloginfo('blog_name');		 
	
						 $mail->AddAddress($_POST['email']);
						 if($mail->Send()) : 
							//echo $successo ; 
						 //	echo 'invio email riuscito';
						 else : 
						 	
						 	//echo $errore; 
						 //	echo 'Invio email fallito';
						 endif;
						//notifica a admin
						$mail->ClearAddresses();
						
						//notifico  a GESTORE SITO ADMIN
						
							//notifica a mirko
							$mail->AddAddress(get_option('bookandpay_fromemail'));
							$mail->Subject = '[admin quote notification] - '.$welcome;
							$mail->AltBody = __('Your request was processed: ', 'bookandpay').get_bloginfo('blog_name'); 
							// optional, comment out and test
							$mail->MsgHTML($content_full);
							$mail->Send();
						
						$mail->ClearAddresses();
							
							//proprietario	spenta ora (pino 2011)
							$mail->AddAddress($_POST['post_email']);
							$mail->Subject = '[Promemoria Offerta inviata correttamente] - '.$welcome;
							$mail->AltBody = __('Your request was processed: ', 'bookandpay').get_bloginfo('blog_name'); 
							// optional, comment out and test
							
							$custom_content='<p>Gentile gestore la tua offerta &egrave; stata inviata correttamente al cliente. <br /></p>';
							$mail->MsgHTML($custom_content);
							$mail->Send();
		
	
			
		}
	
	
	   		
	   		echo '<div id="message" class="success">
					<h2>La tua risposta &egrave; stata inviata con successo al cliente!</h2>
					<p><strong>'.__('Grazie per la collaborazione, per dubbi o informazioni contattaci! <a href="mailto:info@dormireinbedandbreakfast.net">Dormire in bed and breakfast!</a>', 'bookandpay').'</strong></p>
					<a href="'.bloginfo('url').'">'.bloginfo('name').'</a>
					</div>';   
	 		die();
	   
	    endif;

	    	
	    	
	    	
	    	$request= $wpdb->get_row("select * from $table_request where payment_status='pending' and magic_string like '$_GET[bid]%'");
			//print_r($request);

	if(count($request)>0):

	        $html='
  			<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">

  			<h2 style="margin-bottom:20px;">Enquiry number  '.$request->id_request.' received : '.date("d/m/Y h:m",strtotime($request->created_at)).'</h2>
			<p>Apartment: <a href="'.$request->post_url.'">'.$request->post_name.'</a><br />
			Customer name: '.$request->contactname.'<br />
			period: from '.date("d/m/Y",strtotime($request->checkin)).' to '.date("d/m/Y",strtotime($request->checkout)).'<br />
			/*nights: '.NumeroNotti($request->checkin,$request->checkout).'<br />people: '.$request->people.'<br />Rooms/apt: '.$request->room.'<br />*/
			<p>room mumber: '.$request->room_number.'</p>
			<p>notes: '.$request->notes.'</p>
			
			<div style="background:#FFF6BF; border:2px solid #FFD324;padding:10px; font-size:80%; margin-bottom:20px;">'.get_option('bookandpay_owner_rules');
			
			
			
			$html.='</div>
			
			<input type="hidden" id="id_request" name="id_request" value="'.$request->id_request.'"/>
			<label for="payment_status"><b>Vuoi accettare questa richiesta?</b></label>

			<select name="payment_status" id="payment_status">
				<option value="conversation"';if($request->payment_status=='conversation') {$html.= 'selected="selected"'; } $html.=' >SI</option>
				<option value="nondisponibile"';if($request->payment_status=='nondisponibile') {$html.= 'selected="selected"'; } $html.=' >NO</option>

			</select>
			<br />			<p>Nel caso in cui tu possieda diverse strutture, cliccando su NO potrai automaticamente proporle al cliente in alternativa, il cliente successivamente riformuler&agrave; la richiesta, se interessato.</p>';
			
			
			
			$my_others=$booking->get_others($request->post_email);
			//print_r($my_others);
			if(count($my_others)>0):
				$html.='<div id="others" class="hidden">';
				$html.='<h3>If you dont have availability you can send offers in your other houses</h3>';
				$html.='<label for="my_others">Select one</label><select name="my_others" id="my_others">';
				foreach($my_others as $my_other):
					$html.='<option value="'.$my_other->ID.'">'.$my_other->post_title.'</option>';
				endforeach;	
				$html.='</select></div>';
			endif;
			
			
			
			
			$html.='<br />
			<!--<label for="advanced_price">Acconto:<input type="text" name="advanced_price" id="advanced_price" maxlength="45" size="10" value="'.$request->advanced_price.'" /></label>-->';
			if(function_exists(CalcolaPrezzo)):
				$html.='<p>Suggested price based on your settings &euro; : '.CalcolaPrezzo($request->post_id,$request->checkin,$request->checkout).'</p>';
			endif;
			$html.='<br />
			&nbsp;<label for="total_price">Total price in &euro;:<input type="text" name="total_price" id="total_price" maxlength="35" size="6" style="font-size:16px;" value="'.$request->total_price.'" /><small>&nbsp; (Only if you have availability)</small></label>
<br />';
//messaggi
$messaggi= $booking->GetMessages($request->id_request);
if(count($messaggi)>0):
$html.='<h2 style="margin:10px;">Messaggi</h2>';	
	foreach ($messaggi as $msg):
	 	$html.='<div class="prezzi" style="border:1px solid #ccc; padding:5px; margin:10px;">
	 		<p><strong>da: '.$msg->msg_from.' a '.$msg->msg_to.'</strong></p>
	 		<p>'.$msg->message.'</p>
	 	</div>';
	 endforeach;
endif;

			$html.='
			<h3>Additional message for customer</h3>
			<label for="owner_notes"><b>Warning</b> </label>
			<small> Messages are under monitoring from our staff (we are able to see what you write to customers), so please do not use to send private info like phone, email or website (you will be removed instantly from the website).</small></p>
			<p>
			<textarea name="owner_notes" cols="60" rows=""></textarea>';

		
		$html.=' 
			<input type="hidden" name="send_email" id="send_email_1" value="1"  />


			<input type="hidden" name="email" value="'.$request->email.'" />
			<input type="hidden" name="post_email" value="'.$request->post_email.'" />
		    </p>
		<div style="float:right;">
			<input type="submit" class="button-primary" style="font-size:20px;padding:10px;" value="Send now &raquo" id="post-booking-submit"/>
		</div>
		<br clear="all" />
		</p>
 
</form>';
else:

	$html='<div class="error"><h1>Enquiry expired or already Booked</h1><p>please verify you are not working on expired enquiry links, remember enquiry have 12 h of expiration</p></div>';
endif;






		break;
	        
	        
	    case 'customer':
	        $html='';
	       	$booking_data=$booking->GetrequestbyCode($_GET['bid']);
	        //print_r($booking_data);
			if($booking->is_expired($_GET['bid'])):
				$html.='<div class="error"><h2>Attenzione! Offerta scaduta</h2><p>Sono trascorse oltre 12 ore dal momento in cui ti &egrave; stata inviata questa offerta dal proprietario di  '.$booking_data->post_name.' e la struttura  potrebbe non essere disponibile si consiglia di ripetere la prenotazione</p>
				<hr />
				<h2>Riformula subito la prenotazione a <a href="http://'.$booking_data->post_url.'">'.$booking_data->post_name.'&raquo;</a></h2></div>';
			
			endif;
			
	       //$booking->BuildMessageArea($booking_data->id_request);
		   //Bookingform($booking_data->id_request);
			
			if(!$booking->is_expired($_GET['bid'])):
			//print_r($booking_data);
			echo wp_get_attachment_url($booking_data->post_id);
			$args = array(
    'post_type' => 'attachment',
    'numberposts' => -1,
    'offset' => 0,
    'orderby' => 'menu_order',
    'order' => 'asc',
    'post_status' => null,
    'post_parent' => $booking_data->post_id
    );
$attachments = get_posts($args);
if ($attachments) {
    foreach ($attachments as $attachment) {
        if(wp_attachment_is_image( $attachment->ID )) {
		$img=wp_get_attachment_image_src($attachment->ID, 'large', false);
		//echo '<a href="'.$img[0].'" style="float:left;" rel="lightbox">';
		$thumb= wp_get_attachment_image($attachment->ID, 'thumbnail', false, false);
		//echo '</a>';

        break;
    }
}
}
			
			$html.='
			<table class="bptable">

				<tr>
					<td class="bpthumb">'.$thumb.'</td>
					<td><p>
						Camera: '.$booking_data->post_name.'<br />
						Checkin: '.date("d/m/Y",strtotime($booking_data->checkin)).'<br />
						Checkout: '.date("d/m/Y",strtotime($booking_data->checkout)).'<br />
						persone: '.$booking_data->people.'<br />
						Notti: '.NumeroNotti($booking_data->checkin,$booking_data->checkout).'<br />
						Prezzo: <strong>'.$booking_data->total_price.'</strong><br />
						Acconto richiesto: <strong>'.$booking_data->advanced_price.'</strong></p>
					</td>
					<td class="bplist">'
						.ServicesList($booking_data->post_id).
					'</td>
				</tr>
			</table><br />		
			<strong>Metodo di pagamento</strong><br />
							<small>Per confermare la prenotazione dovete effettuare il pagamento (entro 12 ore) di <b>'.$booking_data->advanced_price.'</b> (50% del totale). Selezionate il metodo di pagamento</small><br /><br />

			<table class="payment">
			<tr>
				<td><br />
				<h3>Paga con Paypal</h3>
				

				
				<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				    <input type="hidden" name="on0" value="order-ID" />
				    <input type="hidden" name="os0" value="'.$booking_data->id_request.'" />
				    
					<input type="hidden" name="return" value="'.get_bloginfo('siteurl').'/'.get_option('bookandpay_bookingpage').'" />
					<input type="hidden" name="notify_url" value="'.get_bloginfo('siteurl').'/wp-content/plugins/bookandpay/ipn.php" />
			    
				<input type="hidden" name="image_url" value="'.get_option('bookandpay_sitelogo').'" />			
				<input type="hidden" name="business" value="'.get_option('bookandpay_paypalemail').'">
				<input type="hidden" name="item_name" value="Acconto reservation: '.$booking_data->id_request.'- '.substr($booking_data->magic_string,0,6).' struttura: '.$booking_data->post_name.' from:'.$booking_data->checkin.' to: '.$booking_data->checkout.'">
				<input type="hidden" name="currency_code" value="EUR">
				
				<!--<input type="hidden" name="amount" value="'.$booking_data->advanced_price.'">-->
				
				<input type="hidden" name="amount" value="0.01">

				<input class="bookandpay_button" type="submit" name="submit" value="paga con Paypal">
				</form>
												<!-- PayPal Logo --><a href="#" onclick="javascript:window.open(\'https://www.paypal.com/it/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside\',\'olcwhatispaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350\');"><img  src="https://www.paypalobjects.com/WEBSCR-640-20110124-1/it_IT/IT/i/bnr/bnr_horizontal_solution_PP_178wx80h.gif" border="0" alt="Che cos\'&egrave; PayPal"></a><!-- PayPal Logo -->
				
				</td>
				<td>
				<h3>Paga con iwbank</h3>
				
				<form action="https://checkout.iwsmile.it/Pagamenti/" method="post">
<input type="hidden" name="ACCOUNT" value="71701608"> <input type="hidden" name="AMOUNT" value="0.1"> <input type="hidden" name="ITEM_NAME" value="Acconto reservation: '.$booking_data->id_request.'- '.substr($booking_data->magic_string,0,6).' struttura: '.$booking_data->post_name.' from:'.$booking_data->checkin.' to: '.$booking_data->checkout.'">
<input type="hidden" name="ITEM_NUMBER" value="'.$booking_data->id_request.'">
<input type="hidden" name="QUANTITY" value="1">
<input type="hidden" name="NOTE" value="1">
<input type="hidden" name="URL_OK" value="'.get_bloginfo('siteurl').'/'.get_option('bookandpay_bookingpage').'">
<input type="hidden" name="URL_BAD" value="'.get_bloginfo('siteurl').'/'.get_option('bookandpay_bookingpage').'">
<input type="hidden" name="URL_CALLBACK" value="'.get_bloginfo('siteurl').'/wp-content/plugins/bookandpay/callback.php">
<input type="hidden" name="LANG_COUNTRY" value="IT">
<input type="hidden" name="CUSTOM" value="'.$booking_data->id_request.'">
<input type="hidden" name="FLAG_ONLY_IWS" value="0">
<input type="submit" class="iwbank bookandpay_button" value="Paga con Carta di credito">
</form>
				<img src="'.get_bloginfo('siteurl').'/wp-content/themes/toolbox/images/verified_by_visa.gif" width="100" alt="secure payment">
				<img src="'.get_bloginfo('siteurl').'/wp-content/themes/toolbox/images/verisign.gif" width="100" alt="secure payment">
				<img src="'.get_bloginfo('siteurl').'/wp-content/themes/toolbox/images/mastercard_securecode.gif" width="100" alt="secure payment">
				</td>
			</tr>
			</table>
				

				

				
				';
				
				$html.='';
			endif;
				
				
				
				
				
				
			break;

	    case 'bo':
	        $html='<h1>cliente</h1>';
	        break;

		default:
			if($_GET['st']!=''): 
				$_GET['st']=='Completed' ? $html='<div class="success" id="message"><p><strong>Reservation completed succesfully!</strong></p></div>' : $html='<div class="error" id="message"><p><strong>Sorry there was some error sendig your email</strong></p></div>';
		endif;
			//wp_redirect(get_option('siteurl') . '/wp-login.php');		
	}

	$content=str_replace($find,$html,$content); 
	return $content;

}



function Bookingform($id='') 
{
	global $post;
	//echo get_post_type($post->ID);
	$id=get_the_ID();
	if(1==1):
		
		//pagina di richiesta dati	
		//email dal custom field del post
		
		if(get_post_meta($id, 'bookandpay_emailowner', TRUE)!=''):
			$email = get_post_meta($id, 'bookandpay_emailowner', TRUE);
		else:
			$email = get_option('bookandpay_fromemail');
		endif;			 
		$maxpax=get_post_meta($id, "bookandpay_maxpeople", true);
			$form_aggiunta="
			
			<div class=\"bookandpay_div\" id=\"bookandpayform\">
			
						<form name=\"bookingform\" id=\"bookingform\">
				
						<ul id=\"bookinglist\">
							<li>
								<label for=\"name\">".__('Name','bookandpay')."</label>
								<input id=\"contactname\" type=\"text\" name=\"contactname\" value=\"".$_COOKIE['bookandpay_contactname']."\"/>
							</li>
							<li>
								<label for=\"surname\">".__('Surname','bookandpay')."</label>
								<input id=\"contactsurname\" type=\"text\" name=\"contactsurname\" value=\"".$_COOKIE['bookandpay_contactsurname']."\" />
							</li>
								<label for=\"email\">".__('Your email','bookandpay')."</label>
								<input id=\"email\" type=\"text\" name=\"email\" value=\"".$_COOKIE['bookandpay_email']."\"/><br />
							<li>
								<label for=\"phonenumber\">".__('Phone','bookandpay')."</label>
								<input id=\"phonenumber\" type=\"text\"value=\"".$_COOKIE['bookandpay_phonenumber']."\" name=\"phonenumber\"/><br />
							</li>
							<li>	
								<input type=\"hidden\" name=\"post_id\" id=\"post_id\" value=\"".$id."\">
								<input type=\"hidden\" name=\"post_name\" id=\"post_name\" value=\"".get_the_title($id)."\">
								<input type=\"hidden\" name=\"post_url\" id=\"post_url\" value=\"".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\">
								<input type=\"hidden\" name=\"post_email\" id=\"post_email\" value=\"".$email."\">

								<label for=\"people\">".__('People','bookandpay')."</label>
							
							
							
							
														
							
							
								<select  id=\"people\"  name=\"people\">
                                                                 
                                                                <option value=\"".$_COOKIE['bookandpay_people']."\" >".$_COOKIE['bookandpay_people']."</option>";
                                           						                     
                                                                for($i=1;$i<=$maxpax;$i++):
                                                                	$form_aggiunta.="<option value=\"".$i."\">".$i."</option>";
                                                                endfor;                                                               
                                                                $form_aggiunta.="</select></li>";

							
							/*verifico l'esistenza di camere o nomi appartamento*/
						
							//$rooms = get_post_meta($id,'bookandpay_room',TRUE);
							$args = array(
							    'numberposts'     => 5,
							    'offset'          => 0,
							    'orderby'         => 'post_date',
							    'order'           => 'DESC',
							    'post_type'       => 'page',
							    'post_parent'     => 119,
							    'post_status'     => 'publish' );
							
							// the_ID();
							$current=get_post(get_the_ID());
							//sprint_r($current);
							$room_name=$current->post_title;
							
							$rooms = get_posts($args);
							//print_r($rooms);
							if($rooms!=''):
								//$rooms=explode(',',$rooms);
								//stampo la select
							$form_aggiunta.="<li><label for=\"room\">".__('Rooms','bookandpay')."</label>
								<select name=\"room\" id=\"room\">";
								$form_aggiunta.="<option value=\"\">".__('Select','bookandpay')."</option>";
								foreach($rooms as $room) :
									if($room_name==$room->post_title) { $sel=" selected=\"selected\""; } else { $sel=""; }
									$form_aggiunta.="<option value=\"".$room->post_title."\"".$sel.">".$room->post_title."</option>";
								endforeach;
								$form_aggiunta.="</select></li>";
							else :
								//hidden	
								$form_aggiunta.="<input type=\"hidden\" name=\"room\" value=\"\" id=\"room\">";
							endif;						
		
						
						
						//	$form_aggiunta.='<label for="room_number">quante camere?</label>
							$form_aggiunta.='<input id="room_number" type="hidden" name="room_number" value="'.$_COOKIE['bookandpay_room_number'].'" />';
							$form_aggiunta.="<li><label for=\"checkin\">".__('checkin','bookandpay')."</label><input id=\"checkin\" type=\"text\" value=\"".$_COOKIE['bookandpay_checkin']."\" class=\"datepicker short\"  name=\"checkin\" autocomplete=\"off\" /></li>
							<li>
								<label for=\"checkout\">".__('checkout','bookandpay')."</label><input id=\"checkout\" type=\"text\" value=\"".$_COOKIE['bookandpay_checkout']."\" class=\"datepicker short\" name=\"checkout\" autocomplete=\"off\"/>
							<li>	
								<label for=\"comments\">".__('notes','bookandpay')."</label><textarea id=\"notes\" name=\"notes\"/></textarea>
								<!--<input type=\"hidden\" name=\"notes\" id=\"notes\"/>-->					
							</li>
							<li>
								<input type=\"button\" class=\"bookandpay_button\" name=\"agg_email\" id=\"booking_submit\" value=\"".__('Check availability', 'bookandpay')."\"/>
							</li>
							<li>	<div id=\"wait\" style=\"display:none;\">".__('1 moment please','bookandpay')."</div></li>
						</ul>	
						
						

				</form>
				<div id=\"booking_response\"></div>
				<div class=\"clear\"></div>
				</div>
				
				";

			echo $form_aggiunta;
			
	endif;
}

//register_sidebar_widget('booking', 'Book now',Bookingform($id));


?>