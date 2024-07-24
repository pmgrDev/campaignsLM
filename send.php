<?php

//* Imports
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/home/pmgr/Desktop/vetmanager/monthlyCampaigns/PHPMailer/src/Exception.php';
require '/home/pmgr/Desktop/vetmanager/monthlyCampaigns/PHPMailer/src/PHPMailer.php';
require '/home/pmgr/Desktop/vetmanager/monthlyCampaigns/PHPMailer/src/SMTP.php';

include 'myHelper.php';

$filename = $argv[1];
if (empty($filename)) die("Empty mandatory parameter 'filename'!\n");

//* Get databases to loop through
$connection = connectToDatabase('vetbizzmanager', null, 'vetbizzm_generalizacao');
$databaseFilter = (!empty($argv[2])) ? "AND t1.clinica = '" . $argv[2] . "'" : "";
$query = executeQuery($connection, str_replace('DATABASE_FILTER', $databaseFilter, file_get_contents('databasesQuery.sql')));
if ($query->num_rows === 0) {
    $connection->close();
    die("No databases found...\n");
}
$databases = [];
while ($database = $query->fetch_object()) $databases[] = ['camv' => $database->camv, 'emails' => $database->emails, 'token' => $database->token];
$connection->close();

//! Failsafe
if (empty($databases)) die("No databases found...\n");

foreach ($databases as $camv) {
    $emails = [];
    if (stripos($camv['emails'], '::') !== FALSE) $emails = explode('::', $camv['emails']);
    else $emails[] = $camv['emails'];

    if (!sendEmail($filename, $emails, $camv['token'])) die("Error on line 32 => Could not send email...\n");
}

function sendEmail($filename, $emails, $token)
{
    if (empty($filename) || empty($emails) || empty($token)) return false;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.vetbizz.pt';
        $mail->SMTPAuth = true;
        $mail->Username = 'helpdesk@vetbizz.pt';
        $mail->Password = 'g%Fv=4{(@)Tu';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'utf-8';
        $mail->isHTML(true);

        //* Recipients
        $mail->setFrom('helpdesk@vetbizz.pt', "VetBizz Consulting");
        // $mail->addBCC("logs@vetbizz.pt", "Logs");

        //* Loop through emails addresses
        foreach ($emails as $email) $mail->addAddress($email);

        //! Failsafe
        //* Add .html extension if it's forgotten
        $filename = (stripos($filename, '.html') === FALSE) ? $filename . '.html' : $filename;

        //Content
        $mail->Subject = "Teste";
        $mail->Body = str_replace('TOKEN', $token, file_get_contents($filename));
        if (empty($mail->Body)) die("Empty file " . $filename . "\n");

        if ($mail->send()) echo "Sent successfully\n";
        else echo "Error sending email...\n";

        return true;
    } catch (Exception $e) {
        echo "Error! Could not sent email!\n";
        return false;
    }
}
