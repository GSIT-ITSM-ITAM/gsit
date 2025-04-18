<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Projecttasktemplate extends Common
{
  protected $model = '\App\Models\Projecttasktemplate';
  protected $rootUrl2 = '/projecttasktemplates/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Projecttasktemplate();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Projecttasktemplate();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Projecttasktemplate();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
