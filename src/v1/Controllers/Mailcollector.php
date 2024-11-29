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

  public function collect(\App\Models\Mailcollector $collector)
  {
    $createdTickets = 0;

    $cm = new ClientManager($options = []);
    $client = $cm->account('account_identifier');
    $client = $cm->make([
      'host'            => 'outlook.office365.com',
      'port'            => 993,
      'encryption'      => 'ssl',
      'validate_cert'   => true,
      'username'        => 'd.durieux@dcsit-group.com',
      // 'password'      => 'password',
      'password'        => $collector->oauth_token,
      'authentication'  => "oauth",
      'protocol'        => 'imap'
    ]);

    //Connect to the IMAP Server
    $retry = false;
    try
    {
      $client->connect();
    }
    catch (\Exception $e)
    {
      $retry = true;
    }

    if ($retry)
    {
      $this->refreshToken($collector);

      $collector->refresh();

      $client = $cm->make([
        'host'            => 'outlook.office365.com',
        'port'            => 993,
        'encryption'      => 'ssl',
        'validate_cert'   => true,
        'username'        => 'd.durieux@dcsit-group.com',
        // 'password'      => 'password',
        'password'        => $collector->oauth_token,
        'authentication'  => "oauth",
        'protocol'        => 'imap'
      ]);
      $client->connect();
    }
    // $status = $client->isConnected();


    // Code for folders
    // $folders = $client->getFolders(false);
    // foreach($folders as $folder)
    // {
    //   echo 'folder: ' . $folder->path . ' | ' . $folder->name;
    //   echo "\n";
    // }


    $folder = $client->getFolderByPath('Tests gsit'); // INBOX');
    $messages = $folder->messages()->unseen()->setFetchOrder('desc')->all()->limit($limit = 10, $page = 0)->get();
    //                             ->all()->count();


    /** @var \Webklex\PHPIMAP\Message $message */
    foreach($messages as $message)
    {
      // echo 'Attachments: ' . $message->getAttachments()->count() . '<br />';

      //Move the current Message to 'INBOX.read'
      // if($message->move('INBOX.read') == true)
      // {
      //   echo 'Message has ben moved';
      // }
      // else
      // {
      //   echo 'Message could not be moved';
      // }

      // TODO: Create tickets

      $requesters = [];

      $from = $message->get("from");
      $values = $from->toArray();

      foreach ($values as $sender)
      {
        $user = \App\Models\User::where('name', $sender->mail)->first();
        if (!is_null($user))
        {
          $requesters[] = $user->id;
        }
      }

      // get priority => urgency
      $urgency = 3;
      $prio = $message->get("priority");
      $values = $prio->toArray();
      foreach ($values as $priority)
      {
        switch ($priority)
        {
          case '1':
            $urgency = 5;
              break;

          case '2':
            $urgency = 4;
              break;

          case '4':
            $urgency = 2;
              break;

          case '5':
            $urgency = 1;
              break;
        }
      }


      $data = (object) [
        'name'        => $message->getSubject(),
        'content'     => \App\v1\Controllers\Toolbox::convertHtmlToMarkdown($message->getHTMLBody()),
        'requester'   => implode(',', $requesters),
        'urgency'     => $urgency,
      ];

      $t = new \App\v1\Controllers\Ticket();
      $data = $t->prepareDataSave($data);
      $t->saveItem($data);
      $createdTickets++;
      $message->setFlag('Seen');
    }
    return $createdTickets;
  }

  protected function getInformationTop($item, $request)
  {
    global $translator, $basePath;

    $uri = $request->getUri();

    $information = [];
    if ($item->is_oauth)
    {
      $information[] = [
        'key'   => 'callbackurl',
        'value' => $translator->translate('Redirect URL') . ' ' . $uri->getScheme() . '://' . $uri->getHost() .
                   $basePath . '/view/mailcollectors/' . $item->id . '/oauth/cb',
        'link'  => null,
      ];
      $information[] = [
        'key'   => 'loginoauth',
        'value' => $translator->translate('Authenticate with oauth'),
        'link'  => $basePath . '/view/mailcollectors/' . $item->id . '/oauth',
      ];

      if ($item->oauth_provider == 'azure')
      {
        $information[] = [
          'key'   => 'documentation',
          'value' => $translator->translate('Provider documentation'),
          'link'  => 'https://learn.microsoft.com/en-us/exchange/client-developer/legacy-protocols/' .
                     'how-to-authenticate-an-imap-pop-smtp-application-by-using-oauth',
        ];
      }
      elseif ($item->oauth_provider == 'google')
      {
        $information[] = [
          'key'   => 'documentation',
          'value' => $translator->translate('Provider documentation'),
          'link'  => 'https://developers.google.com/gmail/imap/xoauth2-protocol',
        ];
      }
    }

    return $information;

    return [
      [
        'key'   => 'callbackurl',
        'value' => $translator->translate('Redirect URL') . ' ' . $uri->getScheme() . '://' . $uri->getHost() .
                   $basePath . '/view/mailcollectors/' . $item->id . '/oauth/cb',
        'link'  => null,
      ],
      [
        'key'   => 'loginoauth',
        'value' => $translator->translate('Go login with oauth'),
        'link'  => $basePath . '/view/mailcollectors/' . $item->id . '/oauth',
      ],
    ];
  }

  public function doOauth(Request $request, Response $response, $args)
  {
    $provider = $this->getMyProvider($request, $args);
    header('Location: ' . $provider->makeAuthUrl());
    exit;
  }

  public function getMyProvider($request, $args)
  {
    global $basePath;

    $collector = \App\Models\Mailcollector::find($args['id']);

    $dataProvider = [
      'title'             => 'Azure AD',
      'applicationId'     => $collector->oauth_applicationid,
      'directoryId'       => $collector->oauth_directoryid,
      'applicationSecret' => $collector->oauth_applicationsecret,
      'scope'             => [
        'openid',
        'https://outlook.office365.com/IMAP.AccessAsUser.All',
        // 'https://outlook.office.com/POP.AccessAsUser.All',
        // 'https://outlook.office.com/SMTP.Send',
        'offline_access',
      ],
    ];
    $uri = $request->getUri();

    $configureProviders = [
      'redirectUri' => $uri->getScheme() . '://' . $uri->getHost() . $basePath . '/view/mailcollectors/' .
      $collector->id . '/oauth/cb',
      'provider' => [
        'azure-ad' => $dataProvider,
      ],
    ];
    return \App\v1\Controllers\Authsso::getProviderInstance('azure-ad', $configureProviders);
  }

  public function callbackOauth(Request $request, Response $response, $args)
  {
    global $basePath;

    $provider = $this->getMyProvider($request, $args);
    $accessToken = $provider->getAccessTokenByRequestParameters($_GET);
    $token = $accessToken->getToken();
    $refreshtoken = $accessToken->getRefreshToken();
    if (!is_null($token) && !is_null($refreshtoken))
    {
      $mailcollector = \App\Models\Mailcollector::find($args['id']);
      $mailcollector->oauth_token = $token;
      $mailcollector->oauth_refresh_token = $refreshtoken;
      $mailcollector->save();

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('Authentication done with success');

      $uri = $request->getUri();

      header('Location: ' . $uri->getScheme() . '://' . $uri->getHost() . $basePath . '/view/mailcollectors/' .
             $mailcollector->id);
      exit;
    }
    echo "Error :/";
    exit;
  }

  public function refreshToken(\App\Models\Mailcollector $collector)
  {
    $parameters = [
      'refresh_token' => $collector->oauth_refresh_token,
      'client_id'     => $collector->oauth_applicationid,
      'client_secret' => $collector->oauth_applicationsecret,
      'grant_type'    => 'refresh_token',
    ];

    $streamFactory = new \SocialConnect\HttpClient\StreamFactory();

    $request = new \SocialConnect\HttpClient\Request(
      'POST',
      'https://login.microsoftonline.com/' . $collector->oauth_directoryid . '/oauth2/v2.0/token'
    );
    $request = $request->withBody(
      $streamFactory->createStream(http_build_query($parameters, '', '&'))
    );

    $client = new \SocialConnect\HttpClient\Curl();
    $response = $client->sendRequest($request);
    $content = $response->getBody()->getContents();
    $result = json_decode($content, true);
    $collector->oauth_token = $result['access_token'];
    $collector->oauth_refresh_token = $result['refresh_token'];
    $collector->save();
  }

  /**
   * Run the scheduled collect mails
   */
  public static function scheduleCollects()
  {
    $crontask = \App\Models\Crontask::where('name', 'mailgate')->first();
    if (is_null($crontask))
    {
      return false;
    }

    $crontaskexecution = new \App\v1\Controllers\Crontaskexecution();
    $executionId = $crontaskexecution->createExecution($crontask);

    $collectors = \App\Models\Mailcollector::where('is_active', true)->get();
    $mailcollector = new \App\v1\Controllers\Mailcollector();
    foreach ($collectors as $collector)
    {
      $nbTickets = $mailcollector->collect($collector);

      $executionlog = new \App\Models\Crontaskexecutionlog();
      $executionlog->crontaskexecution_id = $executionId;
      $executionlog->volume = $nbTickets;
      $executionlog->content = $collector->name . ' (' . $collector->id . ')';
      $executionlog->save();
    }

    $crontaskexecution->endExecution($executionId);

    return true;
  }
}
