<?php
define('WP_USE_THEMES', false);
require("../../../wp-blog-header.php"); 
status_header(200);
nocache_headers();
require_once('class.phpmailer.php');
include_once('class.smtp.php');
require('send_confirmation.php');


//Mi faccio restituire i parametri di request
$custom = $_REQUEST["custom"]; 
$payer_id = $_REQUEST["payer_id"]; 
$thx_id = $_REQUEST["thx_id"]; 
$verify_sign = $_REQUEST["verify_sign"]; 
$amount = $_REQUEST["amount"];
$codice_segreto = 'BECEB4038BAA5E33E3FCF7D614E576B79AACACBAAC21F71AEEDB698336682B6E';

//Inserire il merchant_key indicato all'interno del sito IWSMILE su POS VIRTUALE/Configurazione/Notifica Pagamento.
$str = "thx_id=".$thx_id."&amount=".$amount."&verify_sign=".$verify_sign; $str .= "&payer_id=".$payer_id; $str .= "&merchant_key=".$codice_segreto; 
$url = "https://checkout.iwsmile.it/Pagamenti/trx.check";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); curl_setopt($ch, CURLOPT_POSTFIELDS, $str); curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); curl_setopt($ch, CURLOPT_POST, TRUE);
$content = curl_exec ($ch);
$c_error = "NONE";
$ret = 'NON DISPONIBILE'; if (curl_errno($ch) != 0) {
$c_error = curl_error($ch); }else{
if(strstr($content,"OK")) {
// Ordine di pagamento VERIFICATO
$ret='VERIFICATO';
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
		$id=$custom;
		$update=$wpdb->query("update $table_request set payment_status = 'Completed' where id_request = '$custom'");
	
		// yes valid, f.e. change payment status and send confirmation voucher 
		send_confirmation($custom);


} elseif(strstr($content,"KO")) {
// Ordine di pagamento NON VERIFICATO
$ret='NON VERIFICATO';
} elseif(strstr($content,"IR")) {
// Richiesta di conferma non VALIDA: controllare i dati e la stringa dei parametri creata per la richiesta
$ret='RICHIESTA NON VALIDA';
} elseif(strstr($content,"EX")) {
// Parametro "verify_sign" scaduto
$ret='RICHIESTA SCADUTA';
}
}
curl_close ($ch);
$handle = fopen($_SERVER['DOCUMENT_ROOT']."/risultati.html", "a");
fwrite($handle, "NOTIFICA VERSO ".$url."<br>\n"); 
fwrite($handle, "merchant_key: ".$codice_segreto."<br>\n"); 
fwrite($handle, "custom: ".$custom."<br>\n"); 
fwrite($handle, "payer_id: ".$payer_id."<br>\n"); 
fwrite($handle, "thx_id: ".$thx_id."<br>\n"); 
fwrite($handle, "verify_sign: ".$verify_sign."<br>\n"); 
fwrite($handle, "amount: ".$amount."<br>\n"); 
fwrite($handle, "STRING: ".$str."<br>\n"); 
fwrite($handle, "CURL Error: ".$c_error."<br>\n"); 
fwrite($handle, "DECODIFICA RISULTATO: ".$ret." \n<br>\n [".$content."]<br><br>\n"); fclose($handle);
echo "Correttamente elaborato."; ?>