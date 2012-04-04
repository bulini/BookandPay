<?php
/*
*Installazione Book and Pay
*/

function bookandpay_install() { 
		
		global $wpdb;
		$table_name_request = $wpdb->prefix . "request";
   		
   		
   		if ($wpdb->get_var("show tables like wp_requestessss") != $table_name_request) 
   		{ 
   			add_option("bp_db_version", "0.1"); 	
   			
   			add_option("bookandpay_formerror","Please check your form and fill all required data");		
   			add_option("bookandpay_mailerror","not valid email address");		
   			add_option("bookandpay_mailsuccess","Thank you for your booking request. You will receive an email shortly with confirmation link!");		
   			
		      $sql = "CREATE TABLE " . $table_name_request . " (
			  id_request int(11) NOT NULL AUTO_INCREMENT,
			  `contactname` varchar(250) default NULL,
			  `contactsurname` varchar(250) default NULL,
			  `created_at` datetime NULL,
			  `email` varchar(250) default NULL,
			  `phonenumber` varchar(250) default NULL,
			  `post_id` mediumint(9) NULL,
			  `post_name` varchar(250) default NULL,
			  `post_url` varchar(250) default NULL,
			  `post_email` varchar(100) default NULL,
			  `people` mediumint(9) NULL,
			  `room` varchar(250) default NULL,
			  `room_number` mediumint(9) NULL,
			  `checkin` date NULL,
			  `checkout` date NULL,
			  `notes` mediumtext NULL,
			  `magic_string` varchar(250) default NULL,
			  `owner_notes` varchar(250) default NULL,
			  `total_price` decimal(10,2) NULL,
			  `advanced_price` decimal(10,2) NULL,
			  `accepted` varchar(1) default NULL,
			  `quoted_date` datetime NULL,
			  `payment_status` varchar(100) default 'pending',
			  `ipaddress` VARCHAR(255)   NULL,
			 
			   PRIMARY KEY  (`id_request`),
						   KEY `post_id` (`post_id`)
						   
						 
			);";

				
				   			
		 	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
		
			
   		} 
   		
   		//creo tabella booking_messages
   		$table_name_messages = $wpdb->prefix . "booking_messages";
   		


   		if($wpdb->get_var("show tables like '$table_name_messages'") != $table_name_messages) 
   		{
   			$sql_liste = "CREATE TABLE ".$table_name_messages." (
  				  `id_message` int(11) NOT NULL auto_increment,
  				  `id_request` int(11) NOT NULL,				  
				  `msg_from` varchar(250) default NULL,
				  `msg_to` varchar(250) default NULL,
				  `message` varchar(250) default NULL,
				  `created_at` datetime NULL,
				  `footer` mediumtext NULL,
				   PRIMARY KEY  (`id_message`));";
   			
		 	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql_liste);
	  
			$admin_email=bloginfo('admin_email');	

   		}
}
?>