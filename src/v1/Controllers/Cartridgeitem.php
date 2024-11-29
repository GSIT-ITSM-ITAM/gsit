<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Cartridgeitem extends Common
{
  protected $model = '\App\Models\Cartridgeitem';
  protected $rootUrl2 = '/cartridgeitems/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Cartridgeitem();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Cartridgeitem();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Cartridgeitem();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubCartridges(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('cartridges')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/cartridges');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCartridges_use = [];
    $myCartridges_out = [];
    $total = 0;
    $total_new = 0;
    $total_use = 0;
    $total_out = 0;
    $pages = [];
    foreach ($myItem->cartridges as $cartridge)
    {
      $status = '';
      $date_add = $cartridge->date_in;
      $date_use = $cartridge->date_use;
      $date_end = $cartridge->date_out;
      $use_on = '';
      $url = '';

      if ($date_end !== null)
      {
        $status = $translator->translatePlural('cartridge' . "\004" . 'Worn', 'cartridge' . "\004" . 'Worn', 1);
        $total_out = $total_out + 1;
      }
      elseif ($date_use !== null)
      {
        $status = $translator->translatePlural('cartridge' . "\004" . 'Used', 'cartridge' . "\004" . 'Used', 1);
        $total_use = $total_use + 1;
      }
      else
      {
        $status = $translator->translatePlural('cartridge' . "\004" . 'New', 'cartridge' . "\004" . 'New', 1);
        $total_new = $total_new + 1;
      }
      $total = $total + 1;

      $use_on = '';
      $printer_counter = '';
      if ($cartridge->printer !== null)
      {
        $use_on = $cartridge->printer->name;

        $url = $this->genereRootUrl2Link($rootUrl2, '/printers/', $cartridge->printer->id);

        if (array_key_exists($cartridge->printer->id, $pages) !== true)
        {
          $pages[$cartridge->printer->id] = $cartridge->printer->init_pages_counter;
        }

        if ($pages[$cartridge->printer->id] < $cartridge->pages)
        {
          $pp = $cartridge->pages - $pages[$cartridge->printer->id];
          $printer_counter = sprintf($translator->translatePlural('%d printed page', '%d printed pages', $pp), $pp);
          $pages[$cartridge->printer->id] = $cartridge->pages;
        }
      }

      if ($cartridge->date_out == null)
      {
        $myCartridges_use[] = [
          'status'       => $status,
          'url'          => $url,
          'date_add'     => $date_add,
          'date_use'     => $date_use,
          'use_on'       => $use_on,
        ];
      }
      else
      {
        $myCartridges_out[] = [
          'status'            => $status,
          'url'               => $url,
          'date_add'          => $date_add,
          'date_use'          => $date_use,
          'date_end'          => $date_end,
          'use_on'            => $use_on,
          'printer_counter'   => $printer_counter,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('cartridges_use', $myCartridges_use);
    $viewData->addData('cartridges_out', $myCartridges_out);
    $viewData->addData('total', $total);
    $viewData->addData('total_new', $total_new);
    $viewData->addData('total_use', $total_use);
    $viewData->addData('total_out', $total_out);

    $viewData->addTranslation('status', $translator->translate('item' . "\004" . 'State'));
    $viewData->addTranslation('date_add', $translator->translate('Add date'));
    $viewData->addTranslation('date_use', $translator->translate('Use date'));
    $viewData->addTranslation('date_end', $translator->translate('End date'));
    $viewData->addTranslation('use_on', $translator->translate('Used on'));
    $viewData->addTranslation('printer_counter', $translator->translate('Printer counter'));
    $viewData->addTranslation('cartridges_use', $translator->translate('Used cartridges'));
    $viewData->addTranslation('cartridges_out', $translator->translate('Worn cartridges'));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation(
      'total_new',
      $translator->translatePlural('cartridge' . "\004" . 'New', 'cartridge' . "\004" . 'New', $total_new)
    );
    $viewData->addTranslation(
      'total_use',
      $translator->translatePlural('cartridge' . "\004" . 'Used', 'cartridge' . "\004" . 'Used', $total_use)
    );
    $viewData->addTranslation(
      'total_out',
      $translator->translatePlural('cartridge' . "\004" . 'Worn', 'cartridge' . "\004" . 'Worn', $total_out)
    );

    return $view->render($response, 'subitem/cartridges.html.twig', (array)$viewData);
  }

  public function showSubPrintermodels(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('printermodels')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/printermodels');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myPrintermodels = [];
    foreach ($myItem->printermodels as $printermodel)
    {
      $name = $printermodel->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/printermodels/', $printermodel->id);

      $myPrintermodels[$printermodel->id] = [
        'name'      => $name,
        'url'       => $url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('printermodels', $myPrintermodels);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/printermodels.html.twig', (array)$viewData);
  }
}
