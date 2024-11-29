<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

final class Queuednotification extends Common
{
  protected $model = '\App\Models\Queuednotification';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Queuednotification();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Queuednotification();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Queuednotification();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  /**
   * Run the scheduled to send mails
   */
  public static function scheduleSendmails()
  {
    $crontask = \App\Models\Crontask::where('name', 'queuednotification')->first();
    if (is_null($crontask))
    {
      return false;
    }

    $crontaskexecution = new \App\v1\Controllers\Crontaskexecution();
    $executionId = $crontaskexecution->createExecution($crontask);

    $queuenotification = new self();
    $nbMails = $queuenotification->sendMails();

    $executionlog = new \App\Models\Crontaskexecutionlog();
    $executionlog->crontaskexecution_id = $executionId;
    $executionlog->volume = $nbMails;
    $executionlog->content = $nbMails . ' mail(s) sent';
    $executionlog->save();

    $crontaskexecution->endExecution($executionId);

    return true;
  }

  private function sendMails()
  {
    $mailer = new PHPMailer(true);

    $nbMailsSend = 0;
    $mails = \App\Models\Queuednotification::get();
    foreach ($mails as $mail)
    {
      // TODO send mail

      try
      {
        //Server settings
        // $mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mailer->isSMTP();                                            //Send using SMTP
        $mailer->Host       = '127.0.0.1';                     //Set the SMTP server to send through
        // $mailer->SMTPAuth   = true;                                   //Enable SMTP authentication
        // $mailer->Username   = 'user@example.com';                     //SMTP username
        // $mailer->Password   = 'secret';                               //SMTP password
        // $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mailer->Port = 2525;

        //Recipients
        $mailer->setFrom($mail->sender, $mail->sendername);
        $mailer->addAddress($mail->recipient, $mail->recipientname);
        $mailer->addReplyTo($mail->replyto, $mail->replytoname);

        //Attachments
        // $mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mailer->isHTML(true);                                  //Set email format to HTML
        $mailer->Subject = $mail->name;
        $mailer->Body    = \App\v1\Controllers\Toolbox::convertHtmlToMarkdown($mail->body_html);
        $mailer->AltBody = $mail->body_html;

        // $mail->addCustomHeader('X-GSIT-instance', 'custom-value');
        // $mail->addCustomHeader('X-GSIT-type', 'App\\Models\\Ticket');
        // $mail->addCustomHeader('X-GSIT-id', '546');
        // "Auto-Submitted":"auto-generated","X-Auto-Response-Suppress":"OOF, DR, NDR, RN, NRN" => $mail->headers

        if (empty($mailer->Body))
        {
          // $mail->delete();
          // continue;
        }

        $mailer->send();

        $mail->messageid = $mailer->getLastMessageID();
        $mail->save();

        $mail->delete();

        $nbMailsSend++;
      }
      catch (Exception $e)
      {
        echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
      }
    }
    return $nbMailsSend;
  }
}
