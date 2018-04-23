<?php

require 'vendor/autoload.php';
/*
 *  CONFIGURE EVERYTHING HERE
 */

// an email address that will be in the From field of the email.
// $from = 'Demo contact form <demo@domain.com>';
$from = new SendGrid\Email(null, $_POST['email']);

// an email address that will receive the email with the output of the form
// $sendTo = 'Demo contact form <demo@domain.com>';
$sendTo = new SendGrid\Email(null, 'shubosha.kuro@gmail.com');

// subject of the email
$subject = 'Neue Nachricht von XP4U [' . $_POST['name'] . ']';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Name', 'surname' => 'Surname', 'phone' => 'Phone', 'email' => 'Email', 'message' => 'Message'); 

// message that will be displayed when everything is OK :)
$okMessage = 'Kontakt erfolgreich hergestellt!';

// If something goes wrong, we will display this message.
$errorMessage = 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später noch einmal';

/*
 *  LET'S DO THE SENDING
 */

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try
{
    if(count($_POST) == 0) throw new \Exception('Form is empty');
    
    $emailText = 
        new SendGrid\Content(
            "text/html", 
            "<i>Eine neue Nachricht ist von XP4U.de eingetroffen.<\i>\n
            <hr/>\n
            <b>Name:</b> " . $_POST['name'] . "<br/>" .
            "<b>Email:</b> " . $_POST['email'] . "<br/>" . 
            "<b>Nachricht:</b> " . $_POST['message'] . "<br/>");

    // Send email
    $mail = new SendGrid\Mail($from, $subject, $sendTo, $emailText);
    $apiKey = getenv('SENDGRID_API_KEY');
    $sg = new \SendGrid($apiKey);

    $response = $sg->client->mail()->send()->post($mail);

    $responseArray = array('type' => 'success', 'message' => $okMessage);
}
catch (\Exception $e)
{
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
}


// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
}
// else just display the message
else {
    echo "<script>document.getElementById('contactResult').innerHTML += ". $responseArray['message'] . ";</script>";
}