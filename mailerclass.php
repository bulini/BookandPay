<?

class minimail { 

// Private variable. 
// Assigns email parameters. 
var $data = array(); 

// Constructs a minimail object. 
function minimail($to, $subject, $message, $headers){ 

// Creates message. 
// Adds a "To" address. 
$this->data['to'] = $to; 
// Adds a "Subject" line. 
$this->data['subject'] = $subject; 
// Adds a "body" line. 
$this->data['message'] = $message; 
// Adds a "From" address. 
$this->data['headers'] = $headers; 

// Call send() function. 
$this->send(); 
} 

// Sends mail using the mail program. 
// Return true, otherwise call mail_error()! 
function send(){ 
$mail = @mail($this->data['to'], $this->data['subject'], $this->data['message'], $this->data['headers']); 

if(!$mail){ 
$error = 'Error sending message!'; 
$this->mail_error($error); 
} 
return true; 
} 

// Output the error message and terminate the script! 
function mail_error($error){ 
die($error); 
} 
}

?>
