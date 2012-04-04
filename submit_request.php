<?php 
include("../../../wp-blog-header.php");
require_once('bookingclass.php');
status_header(200);
nocache_headers();
//cookie
setcookie("bookandpay_checkin", $_POST['checkin'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));
setcookie("bookandpay_checkout", $_POST['checkout'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));

setcookie("bookandpay_contactname", $_POST['contactname'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));
setcookie("bookandpay_contactsurname", $_POST['contactsurname'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));
setcookie("bookandpay_email", $_POST['email'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));
setcookie("bookandpay_phonenumber", $_POST['phonenumber'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));
setcookie("bookandpay_room_number", $_POST['room_number'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));
setcookie("bookandpay_people", $_POST['people'], time()+3600, "/", str_replace('http://www','',get_bloginfo('url')));


/**AZIONE DI INSERIMENTO BOOKING DALLA FORM DEL SITO**/


$booking = new Booking();

$booking->NewRequest();



?>