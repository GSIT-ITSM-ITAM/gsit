<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Devicenetworkcard extends Common
{
  protected $model = '\App\Models\Devicenetworkcard';
  protected $rootUrl2 = '/devicenetworkcards/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicenetworkcard();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicenetworkcard();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicenetworkcard();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
