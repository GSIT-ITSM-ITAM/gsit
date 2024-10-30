<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Login extends Common
{
  public function getLogin(Request $request, Response $response, $args): Response
  {
    global $basePath;

    $view = Twig::fromRequest($request);

    $viewData = [
      'title'    => 'GSIT - ' . 'Login page',
      'rootpath' => \App\v1\Controllers\Toolbox::getRootPath($request),
      'basePath' => $basePath,
      'sso' => [],
    ];

    $authsso = \App\Models\Authsso::where('is_active', true)->get();
    foreach ($authsso as $sso)
    {
      $viewData['sso'][] = [
        'id'    => $sso->callbackid,
        'name'  => $sso->name,
      ];
    }

    return $view->render($response, 'login.html.twig', $viewData);
  }

  public function postLogin(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    $token = new \App\v1\Controllers\Token();

    // Validate data
    if (!property_exists($data, 'login') || !property_exists($data, 'password'))
    {
      throw new \Exception('Login error', 401);
    }

    if (gettype($data->login) != 'string' || gettype($data->password) != 'string')
    {
      throw new \Exception('Login error', 401);
    }

    // check if account exists in local
    $user = \App\Models\User::
        where('name', $data->login)
      ->where('is_active', true)
      ->where('auth_id', 0)
      ->first();
    if (!is_null($user))
    {
      // check passwords
      $check = $token->checkPassword($data->password, $user->password);
      if (!$check)
      {
        throw new \Exception('Login or password error', 401);
      }
      $this->authOkAndRedirect($user);
      exit;
    }

    if (is_null($user))
    {
      // Search in LDAP
      $users = \App\Models\User::
          where('name', $data->login)
      ->where('is_active', true)
      ->where('auth_id', '>', 0)
      ->get();
      foreach ($users as $user)
      {
        $authRet = \App\v1\Controllers\Authldap::tryAuth($user->auth_id, $user->user_dn, $data->password);
        if ($authRet)
        {
          $this->authOkAndRedirect($user);
          exit;
        }
      }
    }

    // Not found in database, so now try to find into ldaps
    $ldaps = \App\Models\Authldap::where('is_active', true)->get();
    foreach ($ldaps as $ldap)
    {
      $foundDN = \App\v1\Controllers\Authldap::importUsers($ldap, $data->login);
      if ($foundDN != false)
      {
        // Create user
        $user = new \App\Models\User();
        $user->name = $data->login;
        $user->is_active = true;
        $user->auth_id = $ldap->id;
        $user->user_dn = $foundDN;
        $user->save();
        $this->authOkAndRedirect($user);
        exit;
      }
    }
    throw new \Exception('Login or password error', 401);
  }

  private function authOkAndRedirect($user)
  {
    global $basePath;

    $token = new \App\v1\Controllers\Token();

    // generate token
    // put into cookie, key token

    $jwt = $token->generateJWTToken($user);

    // Set Cookie
    // $cookie_lifetime = empty($cookie_value) ? time() - 3600 : time() + $CFG_GLPI['login_remember_time'];
    // $cookie_path     = ini_get('session.cookie_path');
    // $cookie_domain   = ini_get('session.cookie_domain');
    // $cookie_secure   = (bool)ini_get('session.cookie_secure');

    setcookie('token', $jwt['token'], 0, $basePath . '/view');
    //, $cookie_lifetime, $cookie_path, $cookie_domain, $cookie_secure, true);

    header('Location: ' . $basePath . '/view/home');
    exit();
  }

  public function doSSO(Request $request, Response $response, $args)
  {
    $provider = $this->prepareSSOService($request, $args);

    try {
      header('Location: ' . $provider->makeAuthUrl());
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    exit;
  }

  public function callbackSSO(Request $request, Response $response, $args)
  {
    $provider = $this->prepareSSOService($request, $args);
    $accessToken = $provider->getAccessTokenByRequestParameters($_GET);
    $ssoUser = $provider->getIdentity($accessToken);

    $user = \App\Models\User::firstOrCreate(['name' => $ssoUser->email]);

    $this->authOkAndRedirect($user);
  }

  private function prepareSSOService($request, $args)
  {
    global $basePath;

    $uri = $request->getUri();

    $callbackid = $args['callbackid'];

    $authsso = \App\Models\Authsso::where('callbackid', $callbackid)->where('is_active', true)->first();
    if (is_null($authsso))
    {
      echo 'error';
      exit;
    }
    $providers = \App\Models\Definitions\Authsso::getProviderArray();
    $dataProvider = [];
    if (!isset($providers[$authsso->provider]))
    {
      echo "error";
      exit;
    }
    $item = $providers[$authsso->provider];
    foreach ($item['fields'] as $field)
    {
      if ($field == 'scope')
      {
        $dataProvider['scope'] = [];
        $scopes = \App\Models\Authssoscope::where('authsso_id', $authsso->id)->get();
        foreach ($scopes as $scope)
        {
          $dataProvider['scope'][] = $scope->name;
        }
      }
      elseif ($field == 'options')
      {
        $dataProvider['options'] = [];
        $options = \App\Models\Authssooption::where('authsso_id', $authsso->id)->get();
        if (isset($item['suboption']))
        {
          $dataProvider['options'][$item['suboption']] = [];
          foreach ($options as $option)
          {
            if (is_null($option->key))
            {
              $dataProvider['options'][$item['suboption']][] = $option->value;
            } else {
              $dataProvider['options'][$item['suboption']][$option->key] = $option->value;
            }
          }
        } else {
          $dataProvider['options'] = [];
          foreach ($options as $option)
          {
            if (is_null($option->key))
            {
              $dataProvider['options'][] = $option->value;
            } else {
              $dataProvider['options'][$option->key] = $option->value;
            }
          }
        }
      } else {
        $dataProvider[$field] = $authsso->{strtolower($field)};
      }
    }

    $configureProviders = [
      'redirectUri' => $uri->getScheme() . '://' . $uri->getHost() . $basePath . '/view/login/sso/' .
        $callbackid . '/cb',
      'provider' => [
        $authsso->provider => $dataProvider,
      ],
    ];
    return \App\v1\Controllers\Authsso::getProviderInstance($authsso->provider, $configureProviders);
  }

  public function logout(Request $request, Response $response, $args)
  {
    global $basePath;

    setcookie('token', '', -1, $basePath . '/view');

    header('Location: ' . $basePath);
    exit();
  }

  public function changeProfileEntity(Request $request, Response $response, $args)
  {
    global $basePath;

    $data = (object) $request->getParsedBody();
    $token = new \App\v1\Controllers\Token();

    $user = \App\Models\User::find($GLOBALS['user_id']);

    if ($data->changeEntityRecursive == 'on')
    {
      $data->changeEntityRecursive = true;
    } else {
      $data->changeEntityRecursive = false;
    }

    // Check if the entity is associated to this profile
    // check if the recursive is associated to the entity and profile
    $validation = false;
    foreach ($user->profiles()->get() as $profile)
    {
      if ($profile->id == $data->changeProfile)
      {
        if ($profile->pivot->entity_id == $data->changeEntity)
        {
          $validation = true;
          if ($data->changeEntityRecursive == true && !$profile->pivot->is_recursive)
          {
            $data->changeEntityRecursive = false;
          }
          break;
        } elseif ($profile->pivot->is_recursive) {
          // search if $data->changeEntity is in sub
          $profileEntity = \App\Models\Entity::find($profile->pivot->entity_id);
          $entity = \App\Models\Entity::
              where('id', $data->changeEntity)
            ->where('treepath', 'LIKE', $profileEntity->treepath . '%')
            ->first();
          if (!is_null($entity))
          {
            $validation = true;
          }
        }
      }
    }

    if ($validation)
    {
      $jwt = $token->generateJWTToken($user, $data->changeProfile, $data->changeEntity, $data->changeEntityRecursive);

      setcookie('token', $jwt['token'], 0, $basePath . '/view');
    }

    header('Location: ' . $data->redirectURL);
    exit();
  }
}
