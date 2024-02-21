<?php
/*
This first bit sets the email address that you want the form to be submitted to.
You will need to change this value to a valid email address that you can access.
*/


$webmaster_email = "kg5jpl@kg5jpl.net";

/*
This bit sets the URLs of the supporting pages.
If you change the names of any of the pages, you will need to change the values here.
*/
$feedback_page = "contact_form.html";
$error_page = "contact_error_message.html";
$thankyou_page = "contact_thank_you.html";

/*
This next bit loads the form field data into variables.
If you add a form field, you will need to add it here.
*/
$email_address = $_REQUEST['email_address'] ;
$comments = $_REQUEST['comments'] ;
$captcha=$_REQUEST['g-recaptcha-response'];

$msg = 
"Email: " . $email_address . "\r\n" . 
"Comments: " . $comments ;

if(!$captcha){
        echo '<h2>Please check the the captcha form.</h2>';
        exit;
}


$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfAQnspAAAAAA_Q0M-cU8fgo5nJlJdqqIjwrxtr&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
if($response['success'] == false)
{
    echo '<h2>You are spammer!</h2>';
    exit;
}

/*
The following function checks for email injection.
Specifically, it checks for carriage returns - typically used by spammers to inject a CC list.
*/
function isInjected($str) {
	$injections = array('(\n+)',
	'(\r+)',
	'(\t+)',
	'(%0A+)',
	'(%0D+)',
	'(%08+)',
	'(%09+)'
	);
	$inject = join('|', $injections);
	$inject = "/$inject/i";
	if(preg_match($inject,$str)) {
		return true;
	}
	else {
		return false;
	}
}

// If the user tries to access this script directly, redirect them to the feedback form,
if (!isset($_REQUEST['email_address'])) {
header( "Location: $feedback_page" );
}

// If the form fields are empty, redirect to the error page.
elseif (empty($comments) || empty($email_address)) {
header( "Location: $error_page" );
}

/* 
If email injection is detected, redirect to the error page.
If you add a form field, you should add it here.
*/
elseif ( isInjected($email_address) || isInjected($comments) ) {
header( "Location: $error_page" );
}

// If we passed all previous tests, send the email then redirect to the thank you page.
else {

	mail( "$webmaster_email", "KG5JPL contact", $msg );

	header( "Location: $thankyou_page" );
}
?>
