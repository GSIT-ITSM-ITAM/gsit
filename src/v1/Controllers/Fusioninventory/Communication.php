<?php

namespace App\v1\Controllers\Fusioninventory;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spatie\ArrayToXml\ArrayToXml;

final class Communication extends \App\v1\Controllers\Common
{
  public function getConfig(Request $request, Response $response, $args): Response
  {
    // define default user
    $GLOBALS['user_id'] = 92368;

    $data = gzinflate(substr($request->getBody(), 2));
    if (strstr($data, '<QUERY>INVENTORY</QUERY>'))
    {
      $computer = new Computer();
      $computer->importComputer($data);
      $payload = [];
    }
    else
    {
      $payload = [
        "PROLOG_FREQ" => 24,
        "RESPONSE" => "SEND",
      ];
    }

    $response->getBody()->write(ArrayToXml::convert($payload, 'REPLY'));
    return $response->withHeader('Content-Type', 'application/xml');
  }

  public function null(Request $request, Response $response, $args): Response
  {
    $response->getBody()->write(json_encode([]));
    return $response->withHeader('Content-Type', 'application/json');
  }
}
