<?php

/**
 * Mail Class
 * Used for generating and sending emails.
 * Replaces the old `fn.mail.php` file
 *
 * @package Cornerstone
 */

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendMail
{

  // Set properties
  private $useMailer = FALSE;
  private $mailOptions;
  private $attachmentPath;
  private $attachmentName;
  private $stringAttachment;

  // Construct class
  public function __construct()
  {

    // Check if PHPMailer is enabled
    if (get_option("enable_phpmailer") == "1") {

      // Load database
      $csMailOptions = new CornerstoneDBH;

      // Get the options from the databse
      $result = $csMailOptions->dbh->selecting(DB_PREFIX . 'options', array('option_name', 'option_value'), eq('option_type', 'mail'));

      // Check if any options are available
      if ($csMailOptions->dbh->getNum_Rows() > 0) {
        // Options available

        // Create array to return
        $returnArray = array();

        // Loop through options to output
        foreach ($result as $row) {

          // Set $key=>$value from table
          $returnArray[strtolower($row->option_name)] = trim($row->option_value);
        }

        // Set options as an object in `mailOptions`
        $this->mailOptions = (object)$returnArray;

        // Set `useMailer`
        $this->useMailer = TRUE;
      } // No mail options available.

    }
  }

  /**
   * Set attachment
   *
   * @param string $absolutePath The absolute path to the attachment to include in the email
   * @param string $name `[optional]` The name to display on the attachment in the email
   *
   * @return bool Return bool of success
   */
  public function setAttachment(
    string $absolutePath,
    string $name = null
  ) {
    // Check the attachment path is not empty
    if (!empty($absolutePath)) {
      // Set the attachment path
      $this->attachmentPath = trim($absolutePath);

      // Check for the attachment name
      if (!empty($name)) {
        // Set the attachment name
        $this->attachmentName = trim($name);
      }
      // Return TRUE
      return TRUE;
    } // No attachment specified. Return FALSE
    return FALSE;
  }

  /**
   * Set string attachment
   *
   * @param string $attachmentString The string of the attachment to include in the email
   * @param string $name The name to display on the attachment in the email
   *
   * @return bool Return bool of success
   */
  public function setStringAttachment(
    string $attachmentString,
    string $name
  ) {
    // Check the string is not empty
    if (!empty($attachmentString) && !empty($name)) {
      // Set the attachment string
      $this->stringAttachment = trim($attachmentString);
      // Set the attachment name
      $this->attachmentName = trim($name);
      // Return TRUE
      return TRUE;
    } // No attachment specified. Return FALSE
    return FALSE;
  }

  /**
   * Set string attachment
   *
   * @param string $attachmentString The string of the attachment to include in the email
   * @param string $name The name to display on the attachment in the email
   *
   * @return bool Return bool of success
   */
  public function clearAttachments()
  {
    // Set the attachment path
    $this->attachmentPath = '';
    // Set the attachment string
    $this->stringAttachment = '';
    // Set the attachment name
    $this->attachmentName = '';
    // Return TRUE
    return TRUE;
  }

  /**
   * Send PHP(Mailer) Email
   *
   * Will send email using PHPMailer if enabled in the site settings, otherwise, will send using the default php 'mail()' function
   *
   * @param string $replyToEmail Email that replies will be sent to. Will default to $from if not set. (optional)
   * @param string $from Email address to send the email "from". Will default to site admin email if not set. (optional)
   * @param string $fromName Name to send the email "from". Will default to site name if not set. (optional)
   * @param string $to Email address to send the email to.
   * @param string $toName Name to send the email to. Will default to $to if not set. (optional)
   * @param string $subject Subject line of email. Will default to "An Email from Site Name" if not set. (optional)
   * @param string $htmlMessage HTML copy of email. (optional, but $textMessage should be set if this is empty.)
   * @param string $textMessage Plain text copy of email. (optional, but $htmlMessage should be set if this is empty.)
   * @param array $ccArray Array of emails to cc email to. In format email => name. (optional)
   *
   * @return bool Return bool of success
   *
   * @link For plain text conversion, check out http://www.webtoolhub.com/tn561393-html-to-text-converter.aspx
   */
  public function sendPHPMail($replyToEmail = '', $from = ADMIN_EMAIL, $fromName = SITE_NAME, $to, $toName = '', $subject, $htmlMessage = '', $textMessage = '', $ccArray = '')
  {
    // Set Default Options
    if (empty($toName)) {
      $toName = $to;
    }
    if (empty($subject)) {
      $subject = "An Email from " . SITE_NAME;
    }
    if (empty($replyToEmail)) {
      $replyToEmail = $from;
    }
    if (empty($textMessage)) {
      $textMessage = "Sorry there was no plain text version of this email sent. Please contact " . $from . " for more information.";
    }
    // Send mail using PHPMailer if 'enablePHPMailer' is set, otherwise send using mail()
    if ($this->useMailer) {
      try {
        // Set PHPMailer
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();        // Set mailer to use SMTP
        $mail->Host = $this->mailOptions->smtp_host;    // Specify main and backup server
        $mail->SMTPAuth = $this->mailOptions->smtp_auth;  // Enable SMTP authentication
        $mail->Username = $this->mailOptions->smtp_username;  // SMTP username
        $mail->Password = $this->mailOptions->smtp_password;    // SMTP password
        $mail->SMTPSecure = $this->mailOptions->smtp_secure;  // Enable encryption, 'ssl' also ac
        $mail->Port = $this->mailOptions->smtp_port;    // SMTP Port
        // Set Email Values
        $mail->setFrom($from, $fromName); // Set "From" Email & Name
        $mail->addReplyTo($replyToEmail); // Set "Reply to" Email
        $mail->addAddress($to, $toName);  // Add a recipient. Name is optional
        if (!empty($ccArray)) {
          $mail->ClearReplyTos();
          foreach ($ccArray as $email => $name) {
            $mail->addReplyTo($email, $name);
            $mail->AddCC($email, $name);
          }
        }
        if (empty($htmlMessage)) {
          $mail->isHTML(false);        // Set email format to text
          $htmlMessage = $textMessage; // Set HTML message to Plain Text message
        } else {
          $mail->isHTML(true);        // Set email format to HTML
        }
        $mail->Subject = $subject;      // Set Email Subject
        $mail->Body    = $htmlMessage;    // Set HTML Message
        $mail->AltBody = $textMessage;    // Set Plain Text Message
        // Check if any attachments
        if (!empty($this->attachmentPath)) {
          // Check if the name is set
          if (!empty($this->attachmentName)) {
            // Add attachment with name
            $mail->addAttachment($this->attachmentPath, $this->attachmentName);
          } else {
            // Add attachment with no name
            $mail->addAttachment($this->attachmentPath);
          }
        } else if (!empty($this->stringAttachment)) {
          // Add attachment with name
          $mail->addStringAttachment($this->stringAttachment, $this->attachmentName);
        }
        $mail->send(); // Send the email
        return 1;
        exit();
      } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
      }
    } else {
      // Start Message
      // Generate a random boundary string
      $mime_boundary = '_x' . sha1(time()) . 'x';
      // Headers - specify your from email address and name here
      // and specify the boundary for the email
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= 'From: "' . $from . '" <' . $fromName . '>' . "\r\n";
      if (!empty($ccArray)) {
        foreach ($ccArray as $email => $name) {
          $headers .= 'Cc: "' . $name . '" <' . $email . '>' . "\r\n";
        }
      }
      $headers .= "Content-Type: multipart/alternative;boundary=" . $mime_boundary . "\r\n";
      // Plain text body
      $message = $textMessage;
      $message .= "\r\n\r\n--" . $mime_boundary . "\r\n";
      // Check if HTML and start HTML
      if (!empty($htmlMessage)) {
        $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
        $message .= $htmlMessage;
        $message .= "\r\n\r\n--" . $mime_boundary . "--";
      }
      // Send Email
      if (mail($to, $subject, $message, $headers)) {
        return 1;
        exit();
      } else {
        return 0;
        exit();
      }
    }
  }

  /**
   * Send Swiftmailer Email
   *
   * Will send email using Swiftmailer.
   * Note: this does not fall-over to the default php 'mail()' function like 'send_phpmail()' does.
   *
   * @param string $replyToEmail Email that replies will be sent to. Will default to $from if not set. (optional)
   * @param string $from Email address to send the email "from". Will default to site admin email if not set. (optional)
   * @param string $fromName Name to send the email "from". Will default to site name if not set. (optional)
   * @param string $to Email address to send the email to.
   * @param string $toName Name to send the email to. Will default to $to if not set. (optional)
   * @param string $subject Subject line of email. Will default to "An Email from Site Name" if not set. (optional)
   * @param string $htmlMessage HTML copy of email. (optional, but $textMessage should be set if this is empty.)
   * @param string $textMessage Plain text copy of email. (optional, but $htmlMessage should be set if this is empty.)
   * @param array $ccArray Array of emails to cc email to. In format email => name. (optional)
   *
   * @return bool Return bool of success
   *
   * @link For plain text conversion, check out http://www.webtoolhub.com/tn561393-html-to-text-converter.aspx
   */
  public function sendSwiftmail($replyToEmail = '', $from = ADMIN_EMAIL, $fromName = SITE_NAME, $to, $toName = '', $subject, $htmlMessage = '', $textMessage = '', $ccArray = '')
  {
    // Set Default Options
    if (empty($toName)) {
      $toName = $to;
    }
    if (empty($subject)) {
      $subject = "An Email from " . SITE_NAME;
    }
    if (empty($replyToEmail)) {
      $replyToEmail = $from;
    }
    if (empty($textMessage)) {
      $textMessage = "Sorry there was no plain text version of this email sent. Please contact " . $from . " for more information.";
    }
    try {

      // Create new Transport
      $transport = (new Swift_SmtpTransport($this->mailOptions->smtp_host, $this->mailOptions->smtp_port))->setUsername($this->mailOptions->smtp_username)->setPassword($this->mailOptions->smtp_password);

      // Create the Mailer using created Transport
      $mailer = new Swift_Mailer($transport);

      // Create a message
      // Set "From" Email & Name, "Reply to" Email, and Add a recipient. Name is optional
      $message = (new Swift_Message($subject))->setFrom([$from => $fromName])->setReplyTo($replyToEmail)->setTo([$to => $toName]);

      // Add CC if set
      if (!empty($ccArray)) {
        foreach ($ccArray as $email => $name) {
          // Set name to email if name is empty
          $name = (empty($name)) ? $email : $name;
          // Add CC
          $message->AddCC($email, $name);
        }
      }

      // Set HTML Message
      $message->setBody($htmlMessage, 'text/html');

      // Set Plain Text Message
      $message->addPart($textMessage, 'text/plain');

      // Send the message
      return $mailer->send($message);
      exit;
    } catch (\Swift_TransportException $e) {

      // Echo message
      echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
    }
  }

  /**
   * Create email using template
   *
   * @param string $templateFile File name of email template to be used
   * @param array $arrayReplace Array of items to replace in the template in Associative array format ("key"=>"value") (optional, dependant on template)
   *
   * @return bool|string Returns FALSE if there was an error, otherwise returns the created email template
   */
  public function createEmailTemplate($templateFile, $arrayReplace = array())
  {

    // Set file path
    $filePath = DIR_SYSTEM . 'emails' . _DS . ltrim($templateFile, '/');
    // Check the file exists
    if (!empty($templateFile) && file_exists($filePath)) {

      // Get file contents
      $file = file_get_contents($filePath);

      // Check if the file has contents
      if (!empty($file)) {

        // Add generic options to array
        $arrayReplace['site_url'] = get_site_url();
        $arrayReplace['site_name'] = SITE_NAME;
        $arrayReplace['support_url'] = get_site_url('support');
        $arrayReplace['current_year'] = date('Y');

        // Loop through and replace items from array
        foreach ($arrayReplace as $key => $value) {
          $file = str_replace("{{" . $key . "}}", $value, $file);
        }

        // Return the created email
        return $file;
        exit;
      } else { // File was empty. Return FALSE

        return FALSE;
        exit;
      }
    } else { // Template doesn't exist. Return FALSE for error

      return FALSE;
      exit;
    }
  }
}
