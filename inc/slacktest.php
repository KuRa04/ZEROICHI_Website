<?php

if($_POST) {

$horizon = "---------------------------"."\n";
$contactName = trim(stripslashes($_POST['contactName']));
$contactEmail = trim(stripslashes($_POST['contactEmail']));
$contactSubject = trim(stripslashes($_POST['contactSubject']));
$contactMessage = trim(stripslashes($_POST['contactMessage']));


   // Check Name
   if (strlen($contactName) < 2) {
		$error['contactName'] = "名前を入力してください。";
	}
	// Check Email
	if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $contactEmail)) {
		$error['contactEmail'] = "メールアドレスを入力してください";
	}
	// Check Message
	if (strlen($contactMessage) < 15) {
		$error['contactMessage'] = "文字を入力してください。または文字が少なすぎます。15文字以上入力してください。";
	}
   // Subject
	if ($contactSubject == '') { $contactSubject = "Contact Form Submission"; }

  // Set Message
$message .= "Email from: " . $contactName . "\n";
$message .= "Email address: " . $contactEmail . "\n";
 $message .= "Message:". "\n";
 $message .= $contactMessage;
 $message .= "\n"."\n"."This email was sent from your site's contact form."."\n";

 // Set From: header
 $from =  $contactName. " <" . $contactEmail . ">";

  // Email Headers
	$headers = "From: " . $from . "\r\n";
	$headers .= "Reply-To: ". $contactEmail  . "\r\n";
 	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


  if (!$error) {

    function slack($payload) {
      $webhook_url = 'https://hooks.slack.com/services/';
    
    
      $ch = curl_init($webhook_url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
      $res = curl_exec($ch);
      curl_close($ch);
    
      return $res;
    }
    
    $res = slack(array(
      'username' => 'Message Notification',
      'channel' => '#general',
      'text' => $horizon.$message.$horizon
    ));
  
    
    header('Location:../index.php?'.$res);
    exit();
  
} # end if - no validation error

else {

  $response["contactName"] = $error['contactName'];
  $response["contactEmail"] .= $error['contactEmail'];
  $response["contactMessage"] .= $error['contactMessage'];
  
  // echo $response;

     
  header('Location:../index.php?'.http_build_query($response));
  exit();
  
} # end if - there was a validation error

}

?>