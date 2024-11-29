<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Authsso extends Common
{
  protected $model = '\App\Models\Authsso';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Authsso();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Authsso();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Authsso();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }


  public static function initScopesForProvider(\App\Models\Authsso $item)
  {
    $providers = \App\Models\Definitions\Authsso::getProviderArray();
    if (isset($providers[$item->provider]) && isset($providers[$item->provider]['default_scope']))
    {
      foreach ($providers[$item->provider]['default_scope'] as $scope)
      {
        $authssoscope = new \App\Models\Authssoscope();
        $authssoscope->name = $scope;
        $authssoscope->authsso_id = $item->id;
        $authssoscope->save();
      }
    }
  }

  public static function initOptionsForProvider(\App\Models\Authsso $item)
  {
    $providers = \App\Models\Definitions\Authsso::getProviderArray();
    if (isset($providers[$item->provider]) && isset($providers[$item->provider]['default_options']))
    {
      foreach ($providers[$item->provider]['default_options'] as $key => $option)
      {
        $authssooption = new \App\Models\Authssooption();
        $authssooption->authsso_id = $item->id;
        if (is_numeric($key))
        {
          // It's only value
          $authssooption->value = $option;
        }
        else
        {
          $authssooption->key = $key;
          $authssooption->value = $option;
        }
        $authssooption->save();
      }
    }
  }

  protected function getInformationTop($item, $request)
  {
    global $translator, $basePath;

    $uri = $request->getUri();
    return [
      [
        'key'   => 'callbackurl',
        'value' => $translator->translate('Redirect URL') . ' ' . $uri->getScheme() . '://' . $uri->getHost() .
                   $basePath . '/view/login/sso/' . $item->callbackid . '/cb',
        'link'  => null,
      ],
    ];
  }

  public static function getProviderInstance($providerName, $configuration)
  {
    $httpClient = new \SocialConnect\HttpClient\Curl();

    $collectionFactory = null;
    $service =  new \SocialConnect\Auth\Service(
      new \SocialConnect\Common\HttpStack(
        $httpClient,
        new \SocialConnect\HttpClient\RequestFactory(),
        new \SocialConnect\HttpClient\StreamFactory()
      ),
      new \SocialConnect\Provider\Session\Session(),
      $configuration,
      $collectionFactory
    );
    return $service->getProvider($providerName);
  }
}
