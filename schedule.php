<?php

$officeNotifierEmail    = "info@eegdonation.com";
$subjectPrefix          = "New Inquiry from EEGD site";
$successMessage         = "Thanks you! Your message has been sent!";
$officeEmail            = "info@eegdonation.com";

$errors                 = array();
$data                   = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $person     = stripslashes(trim($_POST['person']));
    $respond    = stripslashes(trim($_POST['respond']));
    $phone      = stripslashes(trim($_POST['phone']));
    $email      = stripslashes(trim($_POST['honeypot']));
    $address      = stripslashes(trim($_POST['address']));
    $comments    = stripslashes(trim($_POST['comments']));

    if (empty($person)) {
        $errors['person'] = "Your name is required.";
    }

    if (empty($respond)) {
        $errors['respond'] = "Your email is required.";
    }

    if (!preg_match('/^[^0-9][A-z0-9._%+-]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', $respond)) {
        $errors['respond'] = "Your email is invalid.";
    }

    if (empty($phone)) {
        $errors['phone'] = "Your phone number is required.";
    }

    if (!empty($email)) {
        $data['message'] = "Your request could not be sent at this time.";
        $data['success'] = false;
    }


    // if we have errors or success is false due to honeypot...
    if (!empty($errors) || (isset($data['success']) && $data['success'] === false)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // send email

        $subject = "{$subjectPrefix}";
        $body = "
            <p>An inquiry was submitted from the <strong>Eternal Education Gift Donation Website</strong> on ".date('M d, Y')."</p>
            <hr />
            <table border='1' cellpadding='2' cellspacing='0' style='border: 1px solid #333; border-bottom: none; width: 100%;'>
                <tr><td width='1' style='border-bottom: 1px solid #333; padding: 5px; width: 1px; white-space:nowrap; font-weight: bold; background-color: #dedede;'><strong>Name: </strong></td><td style='border-bottom: 1px solid #333; padding: 5px;'>&nbsp;{$person}</td></tr>
                <tr><td width='1'style='border-bottom: 1px solid #333; padding: 5px; width: 1px; white-space:nowrap; font-weight: bold; background-color: #dedede;'><strong>Email: </strong></td><td style='border-bottom: 1px solid #333; padding: 5px;'>&nbsp;{$respond}</td></tr>
                <tr><td width='1'style='border-bottom: 1px solid #333; padding: 5px; width: 1px; white-space:nowrap; font-weight: bold; background-color: #dedede;'><strong>Phone: </strong></td><td style='border-bottom: 1px solid #333; padding: 5px;'>&nbsp;{$phone}</td></tr>
                <tr><td width='1'style='border-bottom: 1px solid #333; padding: 5px; width: 1px; white-space:nowrap; font-weight: bold; background-color: #dedede;'><strong>Address: </strong></td><td style='border-bottom: 1px solid #333; padding: 5px;'>&nbsp;{$address}</td></tr>
                <tr><td width='1'style='border-bottom: 1px solid #333; padding: 5px; width: 1px; white-space:nowrap; font-weight: bold; background-color: #dedede;'><strong>Inquiry: </strong></td><td style='border-bottom: 1px solid #333; padding: 5px;'>&nbsp;{$comments}</td></tr>
            </table>
        ";

        $headers  = 'MIME-Version: 1.1' . PHP_EOL;
        $headers .= 'Content-type: text/html; charset=UTF-8' . PHP_EOL;
        $headers .= "From: Schedule Request Notifier <$officeNotifierEmail>" . PHP_EOL;
        $headers .= "Reply-To: $officeEmail" . PHP_EOL;
        $headers .= "X-Mailer: PHP/". phpversion() . PHP_EOL;

        mail($officeEmail, $subject, $body, $headers);

        $data['success'] = true;
        $data['message'] = $successMessage;
    }

    // return all our data to the AJAX call
    echo json_encode($data);
}
