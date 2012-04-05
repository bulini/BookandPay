<?php
/*******************
wp-admin core:pages functions options pages etc.. 
******************/


function bookandpay_admin_pages() {
global $wpdb;
   
	add_menu_page(__('Booking', 'bookandpay'), __('Booking and Payment', 'bookandpay'), 8, __FILE__, 'request_list');
	add_submenu_page(__FILE__, __('Manage requests', 'bookandpay'), __('Manage requests', 'bookandpay'), 8, 'Requests', 'request_list');
	add_submenu_page(__FILE__, __('SMTP settings', 'bookandpay'), __('SMTP settings', 'bookandpay'), 8, 'bookandpay_smtp', 'bookandpay_smtp_settings');
	add_submenu_page(__FILE__, __('Email Options', 'bookandpay'), __('Template settings', 'bookandpay'), 8, 'template-options', 'template_options');
	add_submenu_page(__FILE__, __('Paypal Options', 'bookandpay'), __('Book and Pay settings', 'bookandpay'), 8, 'bookandpay-options', 'bookandpay_options');
}

/*******************
Settings SMTP
******************/

function bookandpay_smtp_settings()
{
	
	$markup= "<div class=\"wrap\"class=\"wrap\"><h2>".__('bookandpay SMTP settings', 'bookandpay');
	
	if($_POST):
		update_option('bookandpay_smtp_host',$_POST['bookandpay_smtp_host']);
		update_option('bookandpay_smtp_hostname',$_POST['bookandpay_smtp_hostname']);
		update_option('bookandpay_smtp_port',$_POST['bookandpay_smtp_port']);
		update_option('bookandpay_smtp_authentication',$_POST['bookandpay_smtp_authentication']);
		update_option('bookandpay_smtp_username',$_POST['bookandpay_smtp_username']);
		update_option('bookandpay_smtp_password',$_POST['bookandpay_smtp_password']);
		
	$markup.='<div id="message" class="updated fade"><p><strong>'.__('Settings saved!', 'bookandpay').'</strong></p></div>';
	endif;
	$markup.='<h3>'.__('Smtp settings are required only if you want to send mail using an SMTP server','bookandpay').'</h3>
	<p>'.__('By default bookandpay will send newsletter using the mail() function, if you sant to send mail using SMTP server you just have to type your settings here').'</p>
<form method="post" action="'.$_SERVER[REQUEST_URI].'">
<table class="form-table">
	<tr>
		<th><label for="bookandpay_smtp_host">SMTP host</label></th>
		<td><input name="bookandpay_smtp_host" id="bookandpay_smtp_host" type="text" value="'.get_option('bookandpay_smtp_host').'" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="bookandpay_smtp_hostname">SMTP hostname</label></th>
		<td><input name="bookandpay_smtp_hostname" id="bookandpay_smtp_hostname" type="text" value="'.get_option('bookandpay_smtp_hostname').'" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="bookandpay_smtp_port">SMTP port</label></th>
		<td><input name="bookandpay_smtp_port" id="bookandpay_smtp_hostname" type="text" value="'.get_option('bookandpay_smtp_port').'" class="regular-text code" /></td>
	</tr>
	<tr>
		<th colspan="2">
		<h3>'.__('Settings below are required only if SMTP server require authentication').'</h3>
		</th>
	</tr>	
	<tr>
		<th><label for="bookandpay_smtp_username">SMTP username</label></th>
		<td><input name="bookandpay_smtp_username" id="bookandpay_smtp_username" type="text" value="'.get_option('bookandpay_smtp_username').'" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="bookandpay_smtp_password">SMTP password</label></th>
		<td><input name="bookandpay_smtp_password" id="bookandpay_smtp_password" type="text" value="'.get_option('bookandpay_smtp_password').'" class="regular-text code" /></td>
	</tr>


</table>


<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="'.__('Save settings', 'bookandpay').'" />
</p>
  </form>';

	$markup.='</div>';

	echo $markup;

}


/**************************
Book and Pay options page
**************************/


function bookandpay_options()
{
	

	$markup= "<div class=\"wrap\"class=\"wrap\"><h2>".__('Book and pay settings', 'bookandpay');
	
	if($_POST):
		update_option('bookandpay_sitelogo', $_POST['bookandpay_sitelogo']);
		update_option('bookandpay_bookingpage',$_POST['bookandpay_bookingpage']);

		update_option('bookandpay_paypalemail',$_POST['bookandpay_paypalemail']);
		update_option('bookandpay_fromemail',$_POST['bookandpay_fromemail']);
		//errori form
		update_option('bookandpay_formerror',$_POST['bookandpay_formerror']);
		update_option('bookandpay_mailerror',$_POST['bookandpay_mailerror']);
		update_option('bookandpay_mailsuccess',$_POST['bookandpay_mailsuccess']);
		//email
		update_option('bookandpay_firstmail',stripslashes($_POST['bookandpay_firstmail']));
		update_option('bookandpay_mailavailable',stripslashes($_POST['bookandpay_mailavailable']));
		update_option('bookandpay_mailunavailable',stripslashes($_POST['bookandpay_mailunavailable']));
		//privacy text
		update_option('bookandpay_privacy',stripslashes($_POST['bookandpay_privacy']));
		//confirmation and rules
		update_option('bookandpay_rules',stripslashes($_POST['bookandpay_rules']));
		//rules showed in the owner booking page:
		update_option('bookandpay_owner_rules',stripslashes($_POST['bookandpay_owner_rules']));
		//rules showed in bottom email:
		update_option('bookandpay_mail_disclaimer',stripslashes($_POST['bookandpay_mail_disclaimer']));




	$markup.='<div id="message" class="updated fade"><p><strong>'.__('Settings saved!', 'bookandpay').'</strong></p></div>';
	endif;
	$markup.='<h3>'.__('All settings are required','bookandpay').'</h3>';
	//which post types are booking
	$markup.='<h3>Select where do you want the booking boxes</h3>';
	
	$args=array(
  	'public'   => true
	); 
	$markup.='<ul>';
	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
	$post_types=get_post_types($args,$output,$operator); 
  		foreach ($post_types  as $post_type ) {
    $markup.='<li><input type="checkbox" name="bookandpay_post_type[]" value="'.$post_type.'" />'. $post_type. '</li>';
  }
		
	$markup.='</ul>
	
<form method="post" action="'.$_SERVER[REQUEST_URI].'">
<table class="form-table">
<tr>
	<th><label for="bookandpay_sitelogo">Booking logo url<small> with http:// (will be display also on Paypal payment page)</small></label></th>
	<td><input name="bookandpay_sitelogo" id="bookandpay_sitelogo" type="text" value="'.get_option('bookandpay_sitelogo').'" class="regular-text code" /></td>
</tr>
	<tr>
		<th><label for="bookandpay_bookingpage">Booking page (slug)<small>'.get_option("home").'/</small></label></th>
		<td><input name="bookandpay_bookingpage" id="bookandpay_bookingpage" type="text" value="'.get_option('bookandpay_bookingpage').'" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="bookandpay_fromemail">(From) email</label></th>
		<td><input name="bookandpay_fromemail" id="bookandpay_fromemail" type="text" value="'.get_option('bookandpay_fromemail').'" class="regular-text code" /></td>
	</tr>

	<tr>
		<th><label for="bookandpay_paypalemail">Paypal email</label></th>
		<td><input name="bookandpay_paypalemail" id="bookandpay_paypalemail" type="text" value="'.get_option('bookandpay_paypalemail').'" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="bookandpay_formerror">Errore invio email generico</label></th>
		<td><textarea rows="4" cols="60" name="bookandpay_formerror" id="bookandpay_formerror">'.get_option('bookandpay_formerror').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_mailerror">Errore formato email</label></th>
		<td><textarea rows="4" cols="60" name="bookandpay_mailerror" id="bookandpay_mailerror">'.get_option('bookandpay_mailerror').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_mailsuccess">Successo</label></th>
		<td><textarea rows="4" cols="60" name="bookandpay_mailsuccess" id="bookandpay_mailsuccess">'.get_option('bookandpay_mailsuccess').'</textarea></td>
	</tr>
	<tr>
		<th colspan="2"><h3>Intestazione messaggi email</h3></th>
	</tr>
	<tr>
		<th><label for="bookandpay_firstmail">Risposta automatica di default</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_firstmail" id="bookandpay_firstmail">'.get_option('bookandpay_firstmail').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_mailavailable">Intestazione messaggio in caso di struttura disponibile</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_mailavailable" id="bookandpay_mailavailable">'.get_option('bookandpay_mailavailable').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_mailunavailable">Intestazione messaggio in caso di struttura NON disponibile</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_mailunavailable" id="bookandpay_mailunavailable">'.get_option('bookandpay_mailunavailable').'</textarea></td>
	</tr>
	<tr>
		<th colspan="2"><h3>'.__('Privacy and rules for voucher','bookandpay').'</h3></th>
	</tr>
	<tr>
		<th><label for="bookandpay_privacy">Privacy text</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_privacy" id="bookandpay_privacy">'.get_option('bookandpay_privacy').'</textarea></td>
	</tr>
	
	<tr>
		<th><label for="bookandpay_rules">Rules text</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_rules" id="bookandpay_rules">'.get_option('bookandpay_rules').'</textarea></td>
	</tr>
	
	<tr>
		<th><label for="bookandpay_owner_rules">Rules for owner text</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_owner_rules" id="bookandpay_owner_rules">'.get_option('bookandpay_owner_rules').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_mail_disclaimer">Disclaimer and privacy text (showed at the bottom of email)</label></th>
		<td><textarea rows="7" cols="60" name="bookandpay_mail_disclaimer" id="bookandpay_mail_disclaimer">'.get_option('bookandpay_mail_disclaimer').'</textarea></td>
	</tr>

</table>


<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="'.__('Save settings', 'bookandpay').'" />
</p>
  </form>';

	$markup.='</div>';

	echo $markup;

}


/***********************
Email Template Options
************************/

function template_options()
{
	

	$markup= "<div class=\"wrap\"class=\"wrap\"><h2>".__('Book and pay Template settings', 'bookandpay');
	
	if($_POST):
		update_option('bookandpay_header',stripslashes($_POST['bookandpay_header']));
		update_option('bookandpay_message',$_POST['bookandpay_message']);
		update_option('bookandpay_footer',stripslashes($_POST['bookandpay_footer']));

	$markup.='<div id="message" class="updated fade"><p><strong>'.__('Settings saved!', 'bookandpay').'</strong></p></div>';
	endif;
	$markup.='<form method="post" action="'.$_SERVER[REQUEST_URI].'">
<table class="form-table">
	<tr>
		<th><label for="bookandpay_header">Header (HTML)</label></th>
		<td><textarea name="bookandpay_header" class="large-text code">'.get_option('bookandpay_header').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_message">Messaggio</label></th>
		<td><textarea name="bookandpay_message" class="large-text code">'.get_option('bookandpay_message').'</textarea></td>
	</tr>
	<tr>
		<th><label for="bookandpay_footer">Footer</label></th>
		<td><textarea name="bookandpay_footer" class="large-text code">'.get_option('bookandpay_footer').'</textarea></td>
	</tr>
	<tr>
	

</table>


<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="'.__('Save settings', 'bookandpay').'" />
</p>
  </form>';

	$markup.='</div>';

	echo $markup;

}


/*********************
PAGINA LISTA richieste
*********************/
function request_list($idstruttura='') {
	require('pagination.class.php');
	
    global $_POST;
	global $wpdb;

    $table_request = $wpdb->prefix . "request";
    
    //cancellazione provamoce col GET (stocazzo)
    if($_GET['delete'] && $_GET['id_request']):   

  		$delete=$wpdb->query("delete from $table_request where id_request = '$_GET[id_request]'"); 		
   		
   		echo '<div id="message" class="updated fade"><p><strong>'.__("Request deleted succesfully!", "sendit").'</strong></p></div>';   
   		//print_r($_POST);
   
    endif;
    
    //modifica status e invio email di notifica
    if($_POST['id_request']): 
   // print_r($_POST); 
   $advance=($_POST['total_price']*30)/100;
		
   		$update=$wpdb->query("update $table_request set advanced_price = '$advance', total_price='$_POST[total_price]', payment_status = '$_POST[payment_status]' where id_request = '$_POST[id_request]'"); 
   		
   		$request=$wpdb->get_row("select * from $table_request where id_request = '$_POST[id_request]'");

	if($_POST['send_email']==1)
	{
		if($_POST['payment_status']=='approvata') { $intro_message=get_option('bookandpay_mailavailable'); $show_details=TRUE;}
		if($_POST['payment_status']=='nondisponibile') { $intro_message=get_option('bookandpay_mailunavailable'); $show_details=FALSE;}

	
		$header= get_option('bookandpay_header');
					//$messaggio= $templaterow->welcome;
					$welcome = __('ref. request n: '.substr($request->magic_string,0,6), 'sendit').get_bloginfo('blog_name');
					$messaggio= $intro_message;
					//mostro i dettagli solo in caso di accettazione
					if($show_details) :
						$messaggio.='<p><strong>Nome:</strong> '.$request->contactname.'</p>';
						$messaggio.='<p><strong>email:</strong> '.$request->email.'</p>';
						$messaggio.='<p><strong>Struttura:</strong> '.$request->post_name.'</p>';
						$messaggio.='<p><strong>checkin:</strong> '.$request->checkin.'</p>';
						$messaggio.='<p><strong>checkout:</strong> '.$request->checkout.'</p>';
						$messaggio.='<p><strong>people:</strong> '.$request->people.'</p>';
						$messaggio.='<p><strong>tipo camera:</strong> '.$request->room.'</p>';
						$messaggio.='<p><strong>Prezzo del soggiorno:</strong>'.$request->total_price.'</p>';
						$messaggio.='<p><strong>Acconto:</strong> '.$request->advanced_price.'</p>';
						$messaggio.='<p><strong>Note:</strong> '.$request->notes.'</p>';
						$messaggio.='<hr /><p>Per confermare la prenotazione e scegliere il metodo di pagamento di euro '.$request->advanced_price.' clicca il link al pannello di controllo:</p>';
						$messaggio.='<a href="http://'.$request->post_url.'?bid='.substr($request->magic_string,0,6).'">conferma prenotazione</a>';
					endif;
					$footer= get_option('bookandpay_footer');
					
					$content_send = $header.$messaggio.$footer;
					#### Creo object PHPMailer e imposto le COSTANTI SMTP PHPMAILER
					$mail = new PHPMailer();

					if(get_option('bookandpay_smtp_host')!='') :	
					//print_r($mail);
						$mail->IsSMTP(); // telling the class to use SMTP
						
						
						$mail->Host = get_option('bookandpay_smtp_host'); // Host
						$mail->Hostname = get_option('bookandpay_smtp_hostname');// SMTP server hostname
						$mail->Port  = get_option('bookandpay_smtp_port');// set the SMTP port
						
						if(get_option('bookandpay_smtp_auth')=='1'):	
							$mail->SMTPAuth = true;     // turn on SMTP authentication
							$mail->Username = get_option('bookandpay_smtp_username');  // SMTP username
							$mail->Password = get_option('bookandpay_smtp_password'); // SMTP password
						else :
							$mail->SMTPAuth = false;// disable SMTP authentication
						endif;
					endif;
					
					$mail->SetFrom(get_option('bookandpay_fromemail'));
					//$mail->AddReplyTo('pinobulini@gmail.com');
					$mail->Subject = $welcome;
					$mail->AltBody = " To view the message, please use an HTML compatible email viewer!";
					// optional, comment out and test
					$mail->MsgHTML($content_send);
					 
					 $admin_mail_message = __('New booking request: ', 'sendit'). get_bloginfo('blog_name');		 

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
					
					//notifico solo se è settato post_email altrimenti ciccia 
					if($_POST['post_email']) :
						//notifica a proprietario struttura
						$mail->AddAddress($_POST['post_email']);
						$mail->Subject = $admin_mail_message;
						$mail->AltBody = __('Your request was processed: ', 'sendit').get_bloginfo('blog_name'); 
						// optional, comment out and test
						$mail->MsgHTML($_POST['email'].__('processing your request:   ').get_bloginfo('url'));
						$mail->Send();
					endif;
	

		
	}


   		
   		echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__('richiesta %s aggiornata', 'bookandpay'), $_POST[email]).'</strong></p></div>';   
 
   
    endif;
	
	 /*gestisco la visualizzazione a seconda del tipo di utente*/
	  
	  global $current_user;
      get_currentuserinfo();

	  
	 $where=' where 1';
	
	 
	 if(isset($_GET['status']) && $_GET['status']!=''):
		$where.= " and payment_status='".$_GET['status']."'";
	 endif;
	
	  if($current_user->user_level!='' and $current_user->user_level<7):
		  $where.= ' and post_email="'.$current_user->user_email.'"';
	  else :
		  $where.='';
	  endif;
	  
	  //last minute per vedere richieste solo x struttura
	  if($idstruttura!=''):
	  	 $where.= ' and post_id="'.$idstruttura.'"';
	  endif;
	  
/*
      echo 'Username: ' . $current_user->user_login . "\n";
      echo 'User email: ' . $current_user->user_email . "\n";
      echo 'User level: ' . $current_user->user_level . "\n";
      echo 'User first name: ' . $current_user->user_firstname . "\n";
      echo 'User last name: ' . $current_user->user_lastname . "\n";
      echo 'User display name: ' . $current_user->display_name . "\n";
      echo 'User ID: ' . $current_user->ID . "\n";
*/
    
   
    //qui fare megafiltro
	$items = $wpdb->get_var("SELECT count(*) FROM $table_request $where"); // number of total rows in the database
	//echo $items;
if($items > 0) {
		$p = new pagination;
		$p->items($items);
		$p->limit(20); // Limit entries per page
		//$p->target("admin.php?page=Requests&status=".$_GET['status']);
		$p->target('http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
		$p->calculate(); // Calculates what to show
		$p->parameterName('paging');
		$p->adjacents(1); //No. of page away from the current page

		if(!isset($_GET['paging'])) {
			$p->page = 1;
		} else {
			$p->page = $_GET['paging'];
		}

		//Query for limit paging
		$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;

} else {
	echo "No Record Found";
}
	

	
	//echo 'SELECT * FROM '.$table_request.$where.' order by id_request desc '.$limit;
	
	$requests = $wpdb->get_results("SELECT * FROM $table_request $where order by id_request desc $limit");
	
    echo "<div class=\"wrap\"><h2>".__('Booking request management', 'bookandpay')."</h2>";
    
    
	


	echo'<div class="alignleft actions">
			<form method="get">
			<select name="status">
				<option value="">'.__('View','bookandpay').'</option>
				<option value="pending">'.__('Pending','bookandpay').'</option>
				<option value="conversation">'.__('Conversation','bookandpay').'</option>
				<option value="completed">'.__('Payment OK','bookandpay').'</option>
				<option value="nondisponibile">'.__('Rejected','bookandpay').'</option>
				<option value="booked_by_owner">'.__('Owner private booking','bookandpay').'</option>

			</select>
		<input type="hidden" name="page" value="Requests" />
		<input type="submit" class="button-secondary" value="'.__('Filter results','bookandpay').'" id="post-query-submit"/>
		</form>
		</div>';
	echo '<div class="alignright actions">';
if($p):
	echo $p->show();
endif;
		echo '</div>';
	

echo '<div class="clear"/>
<br />
	<h3>'.__('Requests', 'bookandpay').' n.'.$items.'</h3>	
	<table cellspacing="0" class="widefat">
	<thead>		
		<tr>
			<th>id</th>
			<th>data/ora</th>
			<th>struttura</th>
			<th>nome</th>
			<th>email</th>
			<th>telefono</th>
			<th>persone</th>
			<th>checkin</th>
			<th>checkout</th>
			<th>commissione</th>
			<th>Tot Booking</th>
			<th></th>
			<th></th>
			<th></th>
			</tr>
	</thead>	
	';
		
    	foreach ($requests as $request) { 
    	$nights = $wpdb->query("SELECT DATEDIFF(".$request->checkin.",".$request->checkout.")");
		$tot_advanced +=$request->advanced_price;
		$tot_booking +=$request->total_price;
    		//coloro le input per distinguere tra chi ha confermato e chi no
    		if ($request->payment_status=='pending') { $style='class="notice"'; } 
    		elseif ($request->payment_status=='instant_booking') { $style='class="instant"'; }
    		elseif ($request->payment_status=='Completed') { $style='class="success"'; }
    		elseif ($request->payment_status=='conversation') { $style='class="conversation"'; }
    		elseif ($request->payment_status=='nondisponibile') { $style='class="error"'; }
     		elseif ($request->payment_status=='booked_by_owner') { $style='class="owner"'; }
 
    		 	
    		else { $style="class=\"notice\""; }
    			//print_r($request);	
    			
    	echo '<tr>
		    	<td '.$style.'><a href="javascript:Void();" class="row-title button-'.$request->id_request.'">'.$request->id_request.' / '.substr($request->magic_string,0,6).'</a></td>
				<td>'.date("d/m/Y H:i:s",strtotime($request->created_at)).' - '.$request->payment_status.'</td>
				<td>'.$request->post_name.'</td>
				<td>'.$request->contactname.'</td>
				<td>'.$request->email.'</td>
				<td>'.$request->phonenumber.'</td>
				<td>'.$request->people.'</td>
				<td>'.date("d/m/Y",strtotime($request->checkin)).'</td>
				<td>'.date("d/m/Y",strtotime($request->checkout)).'</td>
				<td>'.$request->advanced_price.'</td>
				<td>'.$request->total_price.'</td>
				<td><button class="button-secondary button-'.$request->id_request.'">visualizza</button></td>
				<td>';
				if($request->payment_status=='Completed'): 
					echo '<button class="button-secondary button-'.$request->id_request.'">reinvia voucher</button>';
				endif;
				
				echo '</td>
				<td>
					<a class="button-secondary" href="http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'&id_request='.$request->id_request.'&delete=1&status='.$_GET['status'].'&paging='.$_GET['paging'].'">Elimina richiesta</a>
				</td>
		    </tr>';
		echo '<tr><td colspan="14">
		<script type="text/javascript">
		 jQuery(document).ready(function(){

		jQuery(".button-'.$request->id_request.'").click(function () {
     	 jQuery("#open-'.$request->id_request.'").toggle("slow");
   		 });
   		  });

		</script>';
		
		if($_POST && $_POST[id_request]==$request->id_request)
		{
			 $display='';
			 $class='class="fade"';
		}	 
			 else 
		{
			$display='display:none;';
		}
		
		
  		echo'<div id="open-'.$request->id_request.'" style="'.$display.'padding:5px;" '.$class.'>
  			<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
  			<p>Richiesta ricevuta il:'.$request->created_at.'</p>
	<p>Struttura: <a href="http://'.$request->post_url.'">'.$request->post_name.'</a><br /><strong>Nome:</strong> '.$request->contactname.'<br />periodo: dal '.date("d/m/Y",strtotime($request->checkin)).' al '.date("d/m/Y",strtotime($request->checkout)).'<br />notti: '.$nights.'<br />Persone: '.$request->people.'<br />Tipo camera: '.$request->room.'</p>
		<p>'.$request->notes.'</p>


<input type="hidden" id="id_request" name="id_request" value="'.$request->id_request.'"/>
			<select name="payment_status" id="pay'.$request->id_request.'">
			<option value="'.$request->payment_status.'">'.$request->payment_status.'</option>

				<option value="pending">Pending</option>
				<option value="approvata">Approvata</option>
				<option value="nondisponibile">Rifiutata (struttura non disponibile)</option>

				<option value="Completed">Completed</option>
				<option value="pagata_bonifico">Pagamento effettuato (Bonifico)</option>
			</select>
			<label for="advanced_price">Acconto:<input type="text" name="advanced_price" id="advanced_price" maxlength="45" size="10" value="'.$request->advanced_price.'" /></label>
			<label for="total_price">Prezzo totale:<input type="text" name="total_price" id="total_price" maxlength="45" size="10" value="'.$request->total_price.'" /></label>
<br />
			<p><label for="owner_notes">Messaggio:</label></p><textarea name="owner_notes" cols="30"></textarea>

		
		<p>
		Invia email di notifica a cliente / struttura? 
		<label for="send_email_0">SI</label>
			<input type="radio" name="send_email" id="send_email" value="1" checked />
		<label for="send_email_1">	
			NO
			</label>
			<input type="radio" name="send_email" id="send_email_0" value="0" />
			<input type="text" name="email" value="'.$request->email.'" />
			<input type="text" name="post_email" value="'.$request->post_email.'" />
		</label>
		<input type="submit" class="button-primary" value="Procedi" id="post-booking-submit"/>
		</p>
 
</form>
		</div>
  	
 
		</td>
		
		</tr>';
		    
    	
    	}
	echo '<tfoot>		
			<tr>
			<th>id</th>
			<th>data/ora</th>
			<th>struttura</th>
			<th>nome</th>
			<th>email</th>
			<th>telefono</th>
			<th>persone</th>
			<th>checkin</th>
			<th>checkout</th>
			<th>Tot incassi: '.$tot_advanced.'</th>
			<th>Tot Booking: '.$tot_booking.'</th>
			<th></th>
			<th></th>
			</tr>
		</tfoot>	';
echo '</table>';
echo '</div>';


    
}



/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'bookandpay_add_custom_box');

/* Use the save_post action to do something with the data entered */
add_action('save_post', 'bookandpay_save_postdata');

/* Adds a custom section to the "advanced" Post and Page edit screens */
function bookandpay_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'bookandpay_email', __( 'Booking Single Settings', 'bookandpay' ), 
                'bookandpay_inner_custom_box', 'post', 'side','high' );
	add_meta_box( 'bookandpay_email', __( 'Booking Single Settings', 'bookandpay' ), 
		                'bookandpay_inner_custom_box', 'page', 'side','high' );
	add_meta_box( 'bookandpay_email', __( 'Booking Single Settings', 'bookandpay' ), 
		                'bookandpay_inner_custom_box', 'accommodations', 'side','high' );		                
	add_meta_box( 'bookandpay_email', __( 'Booking Single Settings', 'bookandpay' ), 
		                'bookandpay_inner_custom_box', 'italy', 'side','high' );
	//add_meta_box( 'bookandpay_sectionid', __( 'email struttura', 'bookandpay' ),'bookandpay_inner_custom_box', 'post', 'advanced','high' );

    add_meta_box( 'bookandpay_room', __( 'Rooms / apt type or name es (double,triple,single separated by comma)', 'bookandpay' ), 
                'bookandpay_inner_room_box', 'post', 'normal','high' );
    
	add_meta_box( 'bookandpay_room', __( 'Rooms type or name es (double,triple,single separated by comma)', 'bookandpay' ), 
		                'bookandpay_inner_room_box', 'page', 'normal','high' );
	add_meta_box( 'bookandpay_room', __( 'Rooms type or name es (double,triple,single separated by comma)', 'bookandpay' ), 
		                'bookandpay_inner_room_box', 'accommodations', 'normal','high' );

	add_meta_box( 'bookandpay_room', __( 'Rooms type or name es (double,triple,single separated by comma)', 'bookandpay' ), 
		                'bookandpay_inner_room_box', 'italy', 'normal','high' );

    /*add_meta_box( 'bookandpay_sectionb', __( 'My Post Section Title', 'bookandpay' ), 
                'bookandpay_inner_room_box', 'post', 'advanced' );
                */
   } else {
    add_action('dbx_post_advanced', 'bookandpay_old_custom_box' );
    add_action('dbx_page_advanced', 'bookandpay_old_custom_box' );
  }
}
   
/* Prints the inner fields for the custom post/page section */
function bookandpay_inner_custom_box() {

  // Use nonce for verification

  echo '<input type="hidden" name="bookandpay_noncename" id="bookandpay_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // The actual fields for data entry

	echo '<p><label for="bookandpay_enabled">' . __("Enable Booking form", 'bookandpay' ) . '?</label></p>';
	echo '<p><select name="bookandpay_enabled">
			<option selected="selected" value="'.get_post_meta($_GET['post'],'bookandpay_enabled',TRUE).'">'.get_post_meta($_GET['post'],'bookandpay_enabled',true).'</option>
			<option value="on">on</option>
			<option value="off">off</option>';
	echo '</select>
		 </p>';

echo '<p><label for="bookandpay_instant_booking">' . __("Enable Instant Booking", 'bookandpay' ) . '?</label></p>';
echo '<p><select name="bookandpay_instant_booking">
		<option selected="selected" value="'.get_post_meta($_GET['post'],'bookandpay_instant_booking',TRUE).'">'.get_post_meta($_GET['post'],'bookandpay_instant_booking',true).'</option>
		<option value="on">on</option>
		<option value="off">off</option>';
echo '</select>
	 </p>';

  echo '<p><label for="bookandpay_emailowner">' . __("Owner email", 'bookandpay' ) . '</label></p>';
  echo '<input type="text" name="bookandpay_emailowner" value="'.get_post_meta($_GET['post'], 'bookandpay_emailowner', TRUE).'" size="25" />';

  echo '<br /><p><label for="bookandpay_owner_phone">' . __("Owner address", 'bookandpay' ) . '</label></p>';
  echo '<input type="text" name="bookandpay_owner_phone" value="'.get_post_meta($_GET['post'], 'bookandpay_owner_phone', TRUE).'" size="25" />';

  echo '<br /><p><label for="bookandpay_owner_address">' . __("exact Address", 'bookandpay' ) . '</label></p>';
  echo '<input type="text" name="bookandpay_owner_address" value="'.get_post_meta($_GET['post'], 'bookandpay_owner_address', TRUE).'" size="25" />';

  echo '<br /><p><label for="bookandpay_owner_notes">' . __("Owner Notes", 'bookandpay' ) . '</label></p>';
  echo '<input type="text" name="bookandpay_owner_notes" value="'.get_post_meta($_GET['post'], 'bookandpay_owner_notes', TRUE).'" size="25" />';

  echo '<br /><p><label for="bookandpay_owner_truename">' . __("Apartment real name", 'bookandpay' ) . '</label></p>';
  echo '<input type="text" name="bookandpay_owner_truename" value="'.get_post_meta($_GET['post'], 'bookandpay_owner_truename', TRUE).'" size="25" />';
  
    echo '<br /><p><label for="bookandpay_maxpeople">' . __("Max pax", 'bookandpay' ) . '</label></p>';
  echo '<input type="text" name="bookandpay_maxpeople" value="'.get_post_meta($_GET['post'], 'bookandpay_maxpeople', TRUE).'" size="25" />';


 }
 
 
 
 /* Prints the inner fields for the custom post/page section */
function bookandpay_inner_room_box() {

  // Use nonce for verification

  echo '<input type="hidden" name="bookandpay_noncename" id="bookandpay_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // The actual fields for data entry

  echo '<label for="bookandpay_room">' . __("rooms", 'bookandpay' ) . '</label> ';
  echo '<input type="text" name="bookandpay_room" value="'.get_post_meta($_GET['post'], 'bookandpay_room', TRUE).'" size="50" />';
 }
 
 

/* Prints the edit form for pre-WordPress 2.5 post/page */
function bookandpay_old_custom_box() {

  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="bookandpay_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'My Post Section Title', 'bookandpay' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

  bookandpay_inner_custom_box();

  // end wrapper

  echo "</div></div></fieldset></div>\n";
}

/* When the post is saved, saves our custom data as codex wants! */
function bookandpay_save_postdata( $post_id ) {
//print_r($_POST);
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['bookandpay_noncename'], plugin_basename(__FILE__) )) {
    return $post_id;
  }

  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  // to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;

  
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
      return $post_id;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
      return $post_id;
  }

  // OK, we're authenticated: we need to find and save the data


  
  update_post_meta($_POST['ID'], 'bookandpay_instant_booking',$_POST['bookandpay_instant_booking']);
  update_post_meta($_POST['ID'], 'bookandpay_enabled',$_POST['bookandpay_enabled']);
  update_post_meta($_POST['ID'], 'bookandpay_emailowner',$_POST['bookandpay_emailowner']);
  update_post_meta($_POST['ID'], 'bookandpay_room',$_POST['bookandpay_room']);
  
  update_post_meta($_POST['ID'], 'bookandpay_owner_phone',$_POST['bookandpay_owner_phone']);
  update_post_meta($_POST['ID'], 'bookandpay_owner_address',$_POST['bookandpay_owner_address']);
  update_post_meta($_POST['ID'], 'bookandpay_owner_notes',$_POST['bookandpay_owner_notes']);
  update_post_meta($_POST['ID'], 'bookandpay_owner_truename',$_POST['bookandpay_owner_truename']);
  update_post_meta($_POST['ID'], 'bookandpay_maxpeople',$_POST['bookandpay_maxpeople']);



 
  
  // Do something with $mydata 
  // probably using add_post_meta(), update_post_meta(), or 
  // a custom table (see Further Reading section below)

   return $mydata;
} ?>