<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Softwareversion extends Common
{
  protected $model = '\App\Models\Softwareversion';
  protected $rootUrl2 = '/softwareversions/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwareversion();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwareversion();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwareversion();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
