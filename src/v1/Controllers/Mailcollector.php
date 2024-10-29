<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;

final class Mailcollector extends Common
{
  protected $model = '\App\Models\Mailcollector';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Mailcollector();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Mailcollector();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Mailcollector();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function collect()
  {
    $cm = new ClientManager($options = []);
    $client = $cm->account('account_identifier');
    $client = $cm->make([
      'host'          => 'outlook.office365.com',
      'port'          => 993,
      'encryption'    => 'ssl',
      'validate_cert' => true,
      'username'      => 'd.durieux@dcsit-group.com',
      // 'password'      => 'password',
      'password'       => 'e2ce19b4-f43b-4db4-aafc-be07f2d71cd9',
      'authentication' => "oauth",

      'protocol'      => 'imap'
    ]);

    //Connect to the IMAP Server
    $client->connect();

    //Loop through every Mailbox
    /** @var \Webklex\PHPIMAP\Folder $folder */
    // foreach($folders as $folder)
    // {
    //   //Get all Messages of the current Mailbox $folder
    //   /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
    //   $messages = $folder->messages()->all()->get();

    //   /** @var \Webklex\PHPIMAP\Message $message */
    //   foreach($messages as $message)
    //   {
    //     echo $message->getSubject() . '<br />';
    //     // echo 'Attachments: ' . $message->getAttachments()->count() . '<br />';
    //     echo $message->getHTMLBody();

    //     //Move the current Message to 'INBOX.read'
    //     // if($message->move('INBOX.read') == true)
    //     // {
    //     //   echo 'Message has ben moved';
    //     // } else {
    //     //   echo 'Message could not be moved';
    //     // }
    //   }
    // }

// end
// $mailbox = new \PhpImap\Mailbox(
//   '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX', // IMAP server and mailbox folder
//   'david.durieux.dcs@gmail.com', // Username for the before configured mailbox
//   'jvaXvMzKx5zZX2xu5jVVf3JmKvTQ5inyfezM5QDp', // Password for the before configured username
//   false,
// );

// try {
//   // Search in mailbox folder for specific emails
//   // PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
//   // Here, we search for "all" emails
//   $mails_ids = $mailbox->searchMailbox('SINCE "20240901"');
// } catch(\PhpImap\Exceptions\ConnectionException $ex) {
//   echo "IMAP connection failed: " . $ex;
//   die();
// }

// // TODO
// // https://github.com/barbushin/php-imap/wiki/Getting-Started


// // Disconnect from mailbox
// $mailbox->disconnect();
  }
}
