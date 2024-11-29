<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use LdapRecord\Container;
use LdapRecord\Models\Entry;

final class Authldap extends Common
{
  protected $model = '\App\Models\Authldap';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Authldap();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Authldap();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Authldap();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public static function importUsers(\App\Models\Authldap $ldap, $login)
  {
    $connection = new \LdapRecord\Connection([
      'hosts'     => [$ldap->host],
      'port'      => (int) $ldap->post,
      'base_dn'   => $ldap->basedn,
      'username'  => $ldap->rootdn,
      'password'  => $ldap->rootdn_passwd,
      'timeout'   => 1,
    ]);

    try
    {
      $connection->connect();
    }
    catch (\Throwable $e)
    {
      return false;
    }

    // Add the connection into the container:
    Container::addConnection($connection);

    // Get all entries:
    // $entries = Entry::find('cn=John Doe,dc=local,dc=com');
    $entries = Entry::where('cn', $login)->get();
    if (count($entries) > 0)
    {
      return current($entries)[0]->getDn();
    }
    return false;
  }

  public static function tryAuth($authldapId, $userdn, $passowrd)
  {
    $authldap = \App\Models\Authldap::find($authldapId);
    if (is_null($authldap) or !$authldap->is_active)
    {
      return false;
    }

    $connection = new \LdapRecord\Connection([
      'hosts'     => [$authldap->host],
      'port'      => (int) $authldap->post,
      'timeout'   => 1,
    ]);

    if ($connection->auth()->attempt($userdn, $passowrd, $stayAuthenticated = true))
    {
      echo 'FOUND';
      return true;
    }
    return false;
  }
}
