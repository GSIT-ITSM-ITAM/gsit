<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Dropdown extends Common
{
  public function getAll(Request $request, Response $response, $args): Response
  {
    // $data = json_decode($request->getBody());
    $data = (object) $request->getQueryParams();

    // POST
    // itemtype
    // filter
    // entity_restrict
    // page (option)

    // GLPI values
    // itemtype "ITILCategory"
    // display_emptychoice  "1"
    // emptylabel "-----"
    // condition  "fc20cd4dd8f56c07f744c2684be72be48c64dc53"
    // entity_restrict  "0"
    // permit_select_parent "0"
    // _idor_toke "5e4a3ab5e4137a71daccbaf311c2ad8f0cdccaefc09cbd0d363efd70cfebbc58"
    // page_limit "100"
    // page "1"

    // response format - JSON
    //   {
    //     "success": true,
    //     "results": [
    //       {
    //         // name displayed in dropdown
    //         "name"  : "Choice 1",
    //          // selected value
    //         "value" : "value1",
    //          // name displayed after selection (optional)
    //         "text"  : "Choice 1"
    //          // whether field should be displayed as disabled (optional)
    //         "disabled"  : false
    //        },
    //       {
    //         "name"  : "Choice 2",
    //         "value" : "value2",
    //         "text"  : "Choice 2"
    //       }
    //     ]
    // }

    $dropData = [];
    $success = false;

    if (property_exists($data, 'itemtype') && class_exists($data->itemtype))
    {
      $item = new $data->itemtype();
      $dropData = $item->getDropdownValues($data->q);
      $success = true;
    }
    else
    {
      $dropData = [];
      $success = false;
    }

    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];

    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function getRuleCriteria(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();

    $dropData = [];
    $success = false;

    $classname = '\\App\\v1\\Controllers\\Rules\\Criteria\\' . $data->itemtype;

    $criteria = $classname::get();
    foreach ($criteria as $id => $crit)
    {
      $dropData[] = [
        'name'  => $crit['title'],
        'value' => $id,
      ];
    }

    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];

    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function getRuleCriteriaCondition(Request $request, Response $response, $args): Response
  {
    global $translator;
    $data = (object) $request->getQueryParams();

    $dropData = [
      [
        'name'  => $translator->translate('is'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_IS,
      ],
      [
        'name'  => $translator->translate('is not'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT,
      ],
      [
        'name'  => $translator->translate('contains'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN,
      ],
      [
        'name'  => $translator->translate('does not contains'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_NOT_CONTAIN,
      ],
      [
        'name'  => $translator->translate('starting with'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_BEGIN,
      ],
      [
        'name'  => $translator->translate('finished by'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_END,
      ],
      [
        'name'  => $translator->translate('regular expression matches'),
        'value' => \App\v1\Controllers\Rules\Common::REGEX_MATCH,
      ],
      [
        'name'  => $translator->translate('regular expression does not match'),
        'value' => \App\v1\Controllers\Rules\Common::REGEX_NOT_MATCH,
      ],
      [
        'name'  => $translator->translate('exists'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_EXISTS,
      ],
      [
        'name'  => $translator->translate('does not exist'),
        'value' => \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS,
      ],
    ];
    $success = false;

    $classname = '\\App\\v1\\Controllers\\Rules\\Criteria\\' . $data->itemtype;

    // $criteria = $classname::get();
    // $crit = $criteria[$data->criteria]

    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];

    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }
}
