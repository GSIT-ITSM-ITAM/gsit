<?php

namespace App;

use Slim\Routing\RouteCollectorProxy;

final class Route
{
  public static function setRoutes(&$app)
  {
    // Enable OPTIONS method for all routes
    $app->options('/{routes:.+}', function ($request, $response, $args)
    {
      return $response;
    });

    // The ping - pong ;)
    // $app->get($prefix . '/ping', \App\v1\Controllers\Ping::class . ':getPing');

    $app->group('/api/v1', function (RouteCollectorProxy $v1)
    {
      $v1->group('/fusioninventory', function (RouteCollectorProxy $fusioninventory)
      {
        $fusioninventory->map(['GET'], '', \App\v1\Controllers\Fusioninventory\Communication::class . ':null');
        $fusioninventory->map(['POST'], '', \App\v1\Controllers\Fusioninventory\Communication::class . ':getConfig');
      });
    });

    $app->group('/view', function (RouteCollectorProxy $view)
    {
      $view->map(['GET'], '/dropdown', \App\v1\Controllers\Dropdown::class . ':getAll');
      $view->map(['GET'], '/dropdown/rule/criteria', \App\v1\Controllers\Dropdown::class . ':getRuleCriteria');
      $view->map(
        ['GET'],
        '/dropdown/rule/criteria/condition',
        \App\v1\Controllers\Dropdown::class . ':getRuleCriteriaCondition'
      );

      $view->group('/login', function (RouteCollectorProxy $login)
      {
        $login->map(['GET'], '', \App\v1\Controllers\Login::class . ':getLogin');
        $login->map(['POST'], '', \App\v1\Controllers\Login::class . ':postLogin');

        $login->group('/sso/{callbackid:[a-z0-9]+}', function (RouteCollectorProxy $loginsso)
        {
          $loginsso->map(['GET'], '', \App\v1\Controllers\Login::class . ':doSSO');
          $loginsso->map(['GET'], '/cb', \App\v1\Controllers\Login::class . ':callbackSSO');
        });
      });

      $view->group('/columns', function (RouteCollectorProxy $columns)
      {
        $columns->map(['GET'], '', \App\v1\Controllers\Displaypreference::class . ':manageColumnsOfModel');
        $columns->map(['POST'], '', \App\v1\Controllers\Displaypreference::class . ':postColumnOfModel');
        $columns->map(['GET'], '/createuser', \App\v1\Controllers\Displaypreference::class . ':viewCreateUserColumn');
        $columns->map(['GET'], '/deleteuser', \App\v1\Controllers\Displaypreference::class . ':viewDeleteUserColumn');
        $columns->group("/{id:[0-9]+}", function (RouteCollectorProxy $columnId)
        {
          $columnId->map(['GET'], '/delete', \App\v1\Controllers\Displaypreference::class . ':deleteColumn');
          $columnId->map(['GET'], '/up', \App\v1\Controllers\Displaypreference::class . ':viewUpColumn');
          $columnId->map(['GET'], '/down', \App\v1\Controllers\Displaypreference::class . ':viewDownColumn');
        });
      });

      $view->map(['GET'], '/logout', \App\v1\Controllers\Login::class . ':logout');
      $view->map(['POST'], '/changeprofileentity', \App\v1\Controllers\Login::class . ':changeProfileEntity');

      $view->map(['GET'], '/home', \App\v1\Controllers\Home::class . ':homepage');

      $view->group('/computers', function (RouteCollectorProxy $computers)
      {
        $computers->map(['GET'], '', \App\v1\Controllers\Computer::class . ':getAll');
        $computers->map(['POST'], '', \App\v1\Controllers\Computer::class . ':postItem');

        $computers->group("/new", function (RouteCollectorProxy $computerNew)
        {
          $computerNew->map(['GET'], '', \App\v1\Controllers\Computer::class . ':showNewItem');
          $computerNew->map(['POST'], '', \App\v1\Controllers\Computer::class . ':newItem');
        });

        $computers->group("/{id:[0-9]+}", function (RouteCollectorProxy $computerId)
        {
          $computerId->map(['GET'], '', \App\v1\Controllers\Computer::class . ':showItem');
          $computerId->map(['POST'], '', \App\v1\Controllers\Computer::class . ':updateItem');

          $computerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Computer::class . ':showSubOperatingSystem');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Computer::class . ':showSubSoftwares');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Computer::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Computer::class . ':showSubVolumes');
            $sub->map(['GET'], 'virtualization', \App\v1\Controllers\Computer::class . ':showSubVirtualization');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Computer::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Computer::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Computer::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Computer::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Computer::class . ':showSubNotes');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Computer::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Computer::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Computer::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Computer::class . ':showSubItil');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Computer::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Computer::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Computer::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Computer::class . ':showSubHistory');
          });
        });
      });

      $view->group('/computerantivirus', function (RouteCollectorProxy $computerantivirus)
      {
        $computerantivirus->map(['GET'], '', \App\v1\Controllers\Computerantivirus::class . ':getAll');
        $computerantivirus->map(['POST'], '', \App\v1\Controllers\Computerantivirus::class . ':postItem');
        $computerantivirus->group("/{id:[0-9]+}", function (RouteCollectorProxy $computerantivirusId)
        {
          $computerantivirusId->map(['GET'], '', \App\v1\Controllers\Computerantivirus::class . ':showItem');
          $computerantivirusId->map(['POST'], '', \App\v1\Controllers\Computerantivirus::class . ':updateItem');
        });
      });

      $view->group('/monitors', function (RouteCollectorProxy $monitors)
      {
        $monitors->map(['GET'], '', \App\v1\Controllers\Monitor::class . ':getAll');
        $monitors->map(['POST'], '', \App\v1\Controllers\Monitor::class . ':postItem');

        $monitors->group("/new", function (RouteCollectorProxy $monitorNew)
        {
          $monitorNew->map(['GET'], '', \App\v1\Controllers\Monitor::class . ':showNewItem');
          $monitorNew->map(['POST'], '', \App\v1\Controllers\Monitor::class . ':newItem');
        });

        $monitors->group("/{id:[0-9]+}", function (RouteCollectorProxy $monitorId)
        {
          $monitorId->map(['GET'], '', \App\v1\Controllers\Monitor::class . ':showItem');
          $monitorId->map(['POST'], '', \App\v1\Controllers\Monitor::class . ':updateItem');

          $monitorId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Monitor::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Monitor::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Monitor::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Monitor::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Monitor::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Monitor::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Monitor::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Monitor::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Monitor::class . ':showSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Monitor::class . ':showSubItil');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Monitor::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Monitor::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Monitor::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Monitor::class . ':showSubHistory');
          });
        });
      });
      $view->group('/softwares', function (RouteCollectorProxy $softwares)
      {
        $softwares->map(['GET'], '', \App\v1\Controllers\Software::class . ':getAll');
        $softwares->map(['POST'], '', \App\v1\Controllers\Software::class . ':postItem');

        $softwares->group("/new", function (RouteCollectorProxy $softwareNew)
        {
          $softwareNew->map(['GET'], '', \App\v1\Controllers\Software::class . ':showNewItem');
          $softwareNew->map(['POST'], '', \App\v1\Controllers\Software::class . ':newItem');
        });

        $softwares->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwareId)
        {
          $softwareId->map(['GET'], '', \App\v1\Controllers\Software::class . ':showItem');
          $softwareId->map(['POST'], '', \App\v1\Controllers\Software::class . ':updateItem');

          $softwareId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Software::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Software::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Software::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Software::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Software::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Software::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Software::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Software::class . ':showSubItil');
            $sub->map(['GET'], 'versions', \App\v1\Controllers\Software::class . ':showSubVersions');
            $sub->map(['GET'], 'licenses', \App\v1\Controllers\Software::class . ':showSubLicenses');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Software::class . ':showSubInfocoms');
            $sub->map(['GET'], 'softwareinstall', \App\v1\Controllers\Software::class . ':showSubSoftwareInstall');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Software::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Software::class . ':showSubHistory');
          });
        });
      });
      $view->group('/softwareversions', function (RouteCollectorProxy $softwareversions)
      {
        $softwareversions->map(['GET'], '', \App\v1\Controllers\Softwareversion::class . ':getAll');
        $softwareversions->map(['POST'], '', \App\v1\Controllers\Softwareversion::class . ':postItem');
        $softwareversions->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwareversionId)
        {
          $softwareversionId->map(['GET'], '', \App\v1\Controllers\Softwareversion::class . ':showItem');
          $softwareversionId->map(['POST'], '', \App\v1\Controllers\Softwareversion::class . ':updateItem');
        });
      });
      $view->group('/networkequipments', function (RouteCollectorProxy $networkequipments)
      {
        $networkequipments->map(['GET'], '', \App\v1\Controllers\Networkequipment::class . ':getAll');
        $networkequipments->map(['POST'], '', \App\v1\Controllers\Networkequipment::class . ':postItem');

        $networkequipments->group("/new", function (RouteCollectorProxy $networkequipmentNew)
        {
          $networkequipmentNew->map(['GET'], '', \App\v1\Controllers\Networkequipment::class . ':showNewItem');
          $networkequipmentNew->map(['POST'], '', \App\v1\Controllers\Networkequipment::class . ':newItem');
        });

        $networkequipments->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkequipmentId)
        {
          $networkequipmentId->map(['GET'], '', \App\v1\Controllers\Networkequipment::class . ':showItem');
          $networkequipmentId->map(['POST'], '', \App\v1\Controllers\Networkequipment::class . ':updateItem');

          $networkequipmentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Networkequipment::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Networkequipment::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Networkequipment::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Networkequipment::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Networkequipment::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Networkequipment::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Networkequipment::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Networkequipment::class . ':showSubContracts');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Networkequipment::class . ':showSubInfocoms');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Networkequipment::class . ':showSubSoftwares');
            $sub->map(
              ['GET'],
              'operatingsystem',
              \App\v1\Controllers\Networkequipment::class . ':showSubOperatingSystem'
            );
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Networkequipment::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Networkequipment::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Networkequipment::class . ':showSubVolumes');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Networkequipment::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Networkequipment::class . ':showSubHistory');
          });
        });
      });
      $view->group('/peripherals', function (RouteCollectorProxy $peripherals)
      {
        $peripherals->map(['GET'], '', \App\v1\Controllers\Peripheral::class . ':getAll');
        $peripherals->map(['POST'], '', \App\v1\Controllers\Peripheral::class . ':postItem');
        $peripherals->group("/{id:[0-9]+}", function (RouteCollectorProxy $peripheralId)
        {
          $peripheralId->map(['GET'], '', \App\v1\Controllers\Peripheral::class . ':showItem');
          $peripheralId->map(['POST'], '', \App\v1\Controllers\Peripheral::class . ':updateItem');

          $peripheralId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Peripheral::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Peripheral::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Peripheral::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Peripheral::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Peripheral::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Peripheral::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Peripheral::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Peripheral::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Peripheral::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Peripheral::class . ':showSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Peripheral::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Peripheral::class . ':showSubComponents');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Peripheral::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Peripheral::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Peripheral::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Peripheral::class . ':showSubHistory');
          });
        });
      });
      $view->group('/printers', function (RouteCollectorProxy $printers)
      {
        $printers->map(['GET'], '', \App\v1\Controllers\Printer::class . ':getAll');
        $printers->map(['POST'], '', \App\v1\Controllers\Printer::class . ':postItem');
        $printers->group("/{id:[0-9]+}", function (RouteCollectorProxy $printerId)
        {
          $printerId->map(['GET'], '', \App\v1\Controllers\Printer::class . ':showItem');
          $printerId->map(['POST'], '', \App\v1\Controllers\Printer::class . ':updateItem');

          $printerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Printer::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Printer::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Printer::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Printer::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Printer::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Printer::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Printer::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Printer::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Printer::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Printer::class . ':showSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Printer::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Printer::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Printer::class . ':showSubVolumes');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Printer::class . ':showSubConnections');
            $sub->map(['GET'], 'cartridges', \App\v1\Controllers\Printer::class . ':showSubCartridges');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Printer::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Printer::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Printer::class . ':showSubHistory');
          });
        });
      });
      $view->group('/cartridgeitems', function (RouteCollectorProxy $cartridgeitems)
      {
        $cartridgeitems->map(['GET'], '', \App\v1\Controllers\Cartridgeitem::class . ':getAll');
        $cartridgeitems->map(['POST'], '', \App\v1\Controllers\Cartridgeitem::class . ':postItem');
        $cartridgeitems->group("/{id:[0-9]+}", function (RouteCollectorProxy $cartridgeitemId)
        {
          $cartridgeitemId->map(['GET'], '', \App\v1\Controllers\Cartridgeitem::class . ':showItem');
          $cartridgeitemId->map(['POST'], '', \App\v1\Controllers\Cartridgeitem::class . ':updateItem');

          $cartridgeitemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Cartridgeitem::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Cartridgeitem::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Cartridgeitem::class . ':showSubDocuments');
            $sub->map(['GET'], 'cartridges', \App\v1\Controllers\Cartridgeitem::class . ':showSubCartridges');
            $sub->map(['GET'], 'printermodels', \App\v1\Controllers\Cartridgeitem::class . ':showSubPrintermodels');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Cartridgeitem::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Cartridgeitem::class . ':showSubHistory');
          });
        });
      });
      $view->group('/consumableitems', function (RouteCollectorProxy $consumableitems)
      {
        $consumableitems->map(['GET'], '', \App\v1\Controllers\Consumableitem::class . ':getAll');
        $consumableitems->map(['POST'], '', \App\v1\Controllers\Consumableitem::class . ':postItem');
        $consumableitems->group("/{id:[0-9]+}", function (RouteCollectorProxy $consumableitemId)
        {
          $consumableitemId->map(['GET'], '', \App\v1\Controllers\Consumableitem::class . ':showItem');
          $consumableitemId->map(['POST'], '', \App\v1\Controllers\Consumableitem::class . ':updateItem');
          $consumableitemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Consumableitem::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Consumableitem::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Consumableitem::class . ':showSubDocuments');
            $sub->map(['GET'], 'consumables', \App\v1\Controllers\Consumableitem::class . ':showSubConsumables');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Consumableitem::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Consumableitem::class . ':showSubHistory');
          });
        });
      });
      $view->group('/phones', function (RouteCollectorProxy $phones)
      {
        $phones->map(['GET'], '', \App\v1\Controllers\Phone::class . ':getAll');
        $phones->map(['POST'], '', \App\v1\Controllers\Phone::class . ':postItem');
        $phones->group("/{id:[0-9]+}", function (RouteCollectorProxy $phoneId)
        {
          $phoneId->map(['GET'], '', \App\v1\Controllers\Phone::class . ':showItem');
          $phoneId->map(['POST'], '', \App\v1\Controllers\Phone::class . ':updateItem');

          $phoneId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Phone::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Phone::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Phone::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Phone::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Phone::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Phone::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Phone::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Phone::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Phone::class . ':showSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Phone::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Phone::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Phone::class . ':showSubVolumes');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Phone::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Phone::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Phone::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Phone::class . ':showSubHistory');
          });
        });
      });
      $view->group('/racks', function (RouteCollectorProxy $racks)
      {
        $racks->map(['GET'], '', \App\v1\Controllers\Rack::class . ':getAll');
        $racks->map(['POST'], '', \App\v1\Controllers\Rack::class . ':postItem');
        $racks->group("/{id:[0-9]+}", function (RouteCollectorProxy $rackId)
        {
          $rackId->map(['GET'], '', \App\v1\Controllers\Rack::class . ':showItem');
          $rackId->map(['POST'], '', \App\v1\Controllers\Rack::class . ':updateItem');
          $rackId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Rack::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Rack::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Rack::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Rack::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Rack::class . ':showSubHistory');
          });
        });
      });
      $view->group('/enclosures', function (RouteCollectorProxy $enclosures)
      {
        $enclosures->map(['GET'], '', \App\v1\Controllers\Enclosure::class . ':getAll');
        $enclosures->map(['POST'], '', \App\v1\Controllers\Enclosure::class . ':postItem');
        $enclosures->group("/{id:[0-9]+}", function (RouteCollectorProxy $enclosureId)
        {
          $enclosureId->map(['GET'], '', \App\v1\Controllers\Enclosure::class . ':showItem');
          $enclosureId->map(['POST'], '', \App\v1\Controllers\Enclosure::class . ':updateItem');
          $enclosureId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Enclosure::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Enclosure::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Enclosure::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Enclosure::class . ':showSubComponents');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Enclosure::class . ':showSubItems');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Enclosure::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Enclosure::class . ':showSubHistory');
          });
        });
      });
      $view->group('/pdus', function (RouteCollectorProxy $pdus)
      {
        $pdus->map(['GET'], '', \App\v1\Controllers\Pdu::class . ':getAll');
        $pdus->map(['POST'], '', \App\v1\Controllers\Pdu::class . ':postItem');
        $pdus->group("/{id:[0-9]+}", function (RouteCollectorProxy $pduId)
        {
          $pduId->map(['GET'], '', \App\v1\Controllers\Pdu::class . ':showItem');
          $pduId->map(['POST'], '', \App\v1\Controllers\Pdu::class . ':updateItem');
          $pduId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Pdu::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Pdu::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Pdu::class . ':showSubItil');
            $sub->map(['GET'], 'plugs', \App\v1\Controllers\Pdu::class . ':showSubPlugs');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Pdu::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Pdu::class . ':showSubHistory');
          });
        });
      });
      $view->group('/passivedcequipments', function (RouteCollectorProxy $passivedcequipments)
      {
        $passivedcequipments->map(['GET'], '', \App\v1\Controllers\Passivedcequipment::class . ':getAll');
        $passivedcequipments->map(['POST'], '', \App\v1\Controllers\Passivedcequipment::class . ':postItem');
        $passivedcequipments->group("/{id:[0-9]+}", function (RouteCollectorProxy $passivedcequipmentId)
        {
          $passivedcequipmentId->map(['GET'], '', \App\v1\Controllers\Passivedcequipment::class . ':showItem');
          $passivedcequipmentId->map(['POST'], '', \App\v1\Controllers\Passivedcequipment::class . ':updateItem');
          $passivedcequipmentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Passivedcequipment::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Passivedcequipment::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Passivedcequipment::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Passivedcequipment::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Passivedcequipment::class . ':showSubHistory');
          });
        });
      });
      $view->group('/itemdevicesimcards', function (RouteCollectorProxy $item_devicesimcards)
      {
        $item_devicesimcards->map(['GET'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':getAll');
        $item_devicesimcards->map(['POST'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':postItem');
        $item_devicesimcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $item_devicesimcardId)
        {
          $item_devicesimcardId->map(['GET'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':showItem');
          $item_devicesimcardId->map(['POST'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':updateItem');
          $item_devicesimcardId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubContracts');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubHistory');
          });
        });
      });

      $view->group('/tickets', function (RouteCollectorProxy $tickets)
      {
        $tickets->map(['GET'], '', \App\v1\Controllers\Ticket::class . ':getAll');
        $tickets->map(['POST'], '', \App\v1\Controllers\Ticket::class . ':postItem');

        $tickets->group("/new", function (RouteCollectorProxy $ticketNew)
        {
          $ticketNew->map(['GET'], '', \App\v1\Controllers\Ticket::class . ':showNewItem');
          $ticketNew->map(['POST'], '', \App\v1\Controllers\Ticket::class . ':newItem');
        });

        $tickets->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketId)
        {
          $ticketId->map(['GET'], '', \App\v1\Controllers\Ticket::class . ':showItem');
          $ticketId->map(['POST'], '', \App\v1\Controllers\Ticket::class . ':updateItem');
          $ticketId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'criteria', \App\v1\Controllers\Rules\Ticket::class . ':showCriteria');
            $sub->map(['POST'], 'followups', \App\v1\Controllers\Followup::class . ':postItem');
            $sub->map(['POST'], 'solutions', \App\v1\Controllers\Solution::class . ':postItem');
            $sub->map(
              ['GET'],
              'solutions/{solutionid:[0-9]+}/accept',
              \App\v1\Controllers\Solution::class . ':postAccept'
            );
            $sub->map(
              ['GET'],
              'solutions/{solutionid:[0-9]+}/refuse',
              \App\v1\Controllers\Solution::class . ':postRefuse'
            );
            $sub->map(['GET'], 'stats', \App\v1\Controllers\Ticket::class . ':showStats');
            $sub->map(['GET'], 'problem', \App\v1\Controllers\Ticket::class . ':showProblem');
            $sub->map(['POST'], 'problem', \App\v1\Controllers\Ticket::class . ':postProblem');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Ticket::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Ticket::class . ':showSubCosts');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Ticket::class . ':showSubItems');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Ticket::class . ':showSubProjects');
            $sub->map(['GET'], 'projecttasks', \App\v1\Controllers\Ticket::class . ':showSubProjecttasks');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\Ticket::class . ':showSubChanges');
            $sub->map(['GET'], 'approvals', \App\v1\Controllers\Ticket::class . ':showSubApprovals');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Ticket::class . ':showSubHistory');
          });
        });
      });
      $view->group('/problems', function (RouteCollectorProxy $problems)
      {
        $problems->map(['GET'], '', \App\v1\Controllers\Problem::class . ':getAll');
        $problems->map(['POST'], '', \App\v1\Controllers\Problem::class . ':postItem');

        $problems->group("/{id:[0-9]+}", function (RouteCollectorProxy $problemId)
        {
          $problemId->map(['GET'], '', \App\v1\Controllers\Problem::class . ':showItem');
          $problemId->map(['POST'], '', \App\v1\Controllers\Problem::class . ':updateItem');
          $problemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Problem::class . ':showSubNotes');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Problem::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\Problem::class . ':showSubChanges');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Problem::class . ':showSubCosts');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Problem::class . ':showSubProjects');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Problem::class . ':showSubTickets');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Problem::class . ':showSubItems');
            $sub->map(['GET'], 'stats', \App\v1\Controllers\Problem::class . ':showStats');
            $sub->map(['GET'], 'analysis', \App\v1\Controllers\Problem::class . ':showAnalysis');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Problem::class . ':showSubHistory');
          });
        });
      });
      $view->group('/changes', function (RouteCollectorProxy $changes)
      {
        $changes->map(['GET'], '', \App\v1\Controllers\Change::class . ':getAll');
        $changes->map(['POST'], '', \App\v1\Controllers\Change::class . ':postItem');

        $changes->group("/{id:[0-9]+}", function (RouteCollectorProxy $changeId)
        {
          $changeId->map(['GET'], '', \App\v1\Controllers\Change::class . ':showItem');
          $changeId->map(['POST'], '', \App\v1\Controllers\Change::class . ':updateItem');
          $changeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Change::class . ':showSubNotes');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Change::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Change::class . ':showSubCosts');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Change::class . ':showSubProjects');
            $sub->map(['GET'], 'problem', \App\v1\Controllers\Change::class . ':showProblem');
            $sub->map(['POST'], 'problem', \App\v1\Controllers\Change::class . ':postProblem');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Change::class . ':showSubTickets');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Change::class . ':showSubItems');
            $sub->map(['GET'], 'stats', \App\v1\Controllers\Change::class . ':showStats');
            $sub->map(['GET'], 'analysis', \App\v1\Controllers\Change::class . ':showAnalysis');
            $sub->map(['GET'], 'plans', \App\v1\Controllers\Change::class . ':showPlans');
            $sub->map(['GET'], 'approvals', \App\v1\Controllers\Change::class . ':showSubApprovals');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Change::class . ':showSubHistory');
          });
        });
      });
      $view->group('/ticketrecurrents', function (RouteCollectorProxy $ticketrecurrents)
      {
        $ticketrecurrents->map(['GET'], '', \App\v1\Controllers\Ticketrecurrent::class . ':getAll');
        $ticketrecurrents->map(['POST'], '', \App\v1\Controllers\Ticketrecurrent::class . ':postItem');

        $ticketrecurrents->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketrecurrentId)
        {
          $ticketrecurrentId->map(['GET'], '', \App\v1\Controllers\Ticketrecurrent::class . ':showItem');
          $ticketrecurrentId->map(['POST'], '', \App\v1\Controllers\Ticketrecurrent::class . ':updateItem');
          $ticketrecurrentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Ticketrecurrent::class . ':showSubHistory');
          });
        });
      });

      $view->group('/softwarelicenses', function (RouteCollectorProxy $softwarelicenses)
      {
        $softwarelicenses->map(['GET'], '', \App\v1\Controllers\Softwarelicense::class . ':getAll');
        $softwarelicenses->map(['POST'], '', \App\v1\Controllers\Softwarelicense::class . ':postItem');
        $softwarelicenses->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwarelicenseId)
        {
          $softwarelicenseId->map(['GET'], '', \App\v1\Controllers\Softwarelicense::class . ':showItem');
          $softwarelicenseId->map(['POST'], '', \App\v1\Controllers\Softwarelicense::class . ':updateItem');
          $softwarelicenseId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Softwarelicense::class . ':showSubCertificates');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Softwarelicense::class . ':showSubNotes');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Softwarelicense::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Softwarelicense::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Softwarelicense::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Softwarelicense::class . ':showSubItil');
            $sub->map(['GET'], 'licenses', \App\v1\Controllers\Softwarelicense::class . ':showSubSoftwarelicenses');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Softwarelicense::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Softwarelicense::class . ':showSubHistory');
          });
        });
      });
      $view->group('/budgets', function (RouteCollectorProxy $budgets)
      {
        $budgets->map(['GET'], '', \App\v1\Controllers\Budget::class . ':getAll');
        $budgets->map(['POST'], '', \App\v1\Controllers\Budget::class . ':postItem');
        $budgets->group("/{id:[0-9]+}", function (RouteCollectorProxy $budgetId)
        {
          $budgetId->map(['GET'], '', \App\v1\Controllers\Budget::class . ':showItem');
          $budgetId->map(['POST'], '', \App\v1\Controllers\Budget::class . ':updateItem');
          $budgetId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Budget::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Budget::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Budget::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Budget::class . ':showSubDocuments');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Budget::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'budgetmain', \App\v1\Controllers\Budget::class . ':showSubBudgetMain');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Budget::class . ':showSubHistory');
          });
        });
      });
      $view->group('/suppliers', function (RouteCollectorProxy $suppliers)
      {
        $suppliers->map(['GET'], '', \App\v1\Controllers\Supplier::class . ':getAll');
        $suppliers->map(['POST'], '', \App\v1\Controllers\Supplier::class . ':postItem');
        $suppliers->group("/{id:[0-9]+}", function (RouteCollectorProxy $supplierId)
        {
          $supplierId->map(['GET'], '', \App\v1\Controllers\Supplier::class . ':showItem');
          $supplierId->map(['POST'], '', \App\v1\Controllers\Supplier::class . ':updateItem');
          $supplierId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Supplier::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Supplier::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Supplier::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Supplier::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Supplier::class . ':showSubContracts');
            $sub->map(['GET'], 'contacts', \App\v1\Controllers\Supplier::class . ':showSubContacts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Supplier::class . ':showSubItil');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Supplier::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Supplier::class . ':showSubHistory');
          });
        });
      });
      $view->group('/contacts', function (RouteCollectorProxy $contacts)
      {
        $contacts->map(['GET'], '', \App\v1\Controllers\Contact::class . ':getAll');
        $contacts->map(['POST'], '', \App\v1\Controllers\Contact::class . ':postItem');
        $contacts->group("/{id:[0-9]+}", function (RouteCollectorProxy $contactId)
        {
          $contactId->map(['GET'], '', \App\v1\Controllers\Contact::class . ':showItem');
          $contactId->map(['POST'], '', \App\v1\Controllers\Contact::class . ':updateItem');
          $contactId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Contact::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Contact::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Contact::class . ':showSubDocuments');
            $sub->map(['GET'], 'suppliers', \App\v1\Controllers\Contact::class . ':showSubSuppliers');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Contact::class . ':showSubHistory');
          });
        });
      });
      $view->group('/contracts', function (RouteCollectorProxy $contracts)
      {
        $contracts->map(['GET'], '', \App\v1\Controllers\Contract::class . ':getAll');
        $contracts->map(['POST'], '', \App\v1\Controllers\Contract::class . ':postItem');
        $contracts->group("/{id:[0-9]+}", function (RouteCollectorProxy $contractId)
        {
          $contractId->map(['GET'], '', \App\v1\Controllers\Contract::class . ':showItem');
          $contractId->map(['POST'], '', \App\v1\Controllers\Contract::class . ':updateItem');
          $contractId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Contract::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Contract::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Contract::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Contract::class . ':showSubDocuments');
            $sub->map(['GET'], 'suppliers', \App\v1\Controllers\Contract::class . ':showSubSuppliers');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Contract::class . ':showSubCosts');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Contract::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Contract::class . ':showSubHistory');
          });
        });
      });
      $view->group('/documents', function (RouteCollectorProxy $documents)
      {
        $documents->map(['GET'], '', \App\v1\Controllers\Document::class . ':getAll');
        $documents->map(['POST'], '', \App\v1\Controllers\Document::class . ':postItem');
        $documents->group("/{id:[0-9]+}", function (RouteCollectorProxy $documentId)
        {
          $documentId->map(['GET'], '', \App\v1\Controllers\Document::class . ':showItem');
          $documentId->map(['POST'], '', \App\v1\Controllers\Document::class . ':updateItem');
          $documentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Document::class . ':showSubNotes');
            $sub->map(['GET'], 'associateditems', \App\v1\Controllers\Document::class . ':showSubAssociatedItems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Document::class . ':showSubDocuments');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Document::class . ':showSubHistory');
          });
        });
      });
      $view->group('/lines', function (RouteCollectorProxy $lines)
      {
        $lines->map(['GET'], '', \App\v1\Controllers\Line::class . ':getAll');
        $lines->map(['POST'], '', \App\v1\Controllers\Line::class . ':postItem');
        $lines->group("/{id:[0-9]+}", function (RouteCollectorProxy $lineId)
        {
          $lineId->map(['GET'], '', \App\v1\Controllers\Line::class . ':showItem');
          $lineId->map(['POST'], '', \App\v1\Controllers\Line::class . ':updateItem');
          $lineId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Line::class . ':showSubNotes');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Line::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Line::class . ':showSubContracts');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Line::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Line::class . ':showSubHistory');
          });
        });
      });
      $view->group('/certificates', function (RouteCollectorProxy $certificates)
      {
        $certificates->map(['GET'], '', \App\v1\Controllers\Certificate::class . ':getAll');
        $certificates->map(['POST'], '', \App\v1\Controllers\Certificate::class . ':postItem');
        $certificates->group("/{id:[0-9]+}", function (RouteCollectorProxy $certificateId)
        {
          $certificateId->map(['GET'], '', \App\v1\Controllers\Certificate::class . ':showItem');
          $certificateId->map(['POST'], '', \App\v1\Controllers\Certificate::class . ':updateItem');
          $certificateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Certificate::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Certificate::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Certificate::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Certificate::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Certificate::class . ':showSubContracts');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Certificate::class . ':showSubDomains');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Certificate::class . ':showSubItil');
            $sub->map(['GET'], 'associateditems', \App\v1\Controllers\Certificate::class . ':showSubAssociatedItems');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Certificate::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Certificate::class . ':showSubHistory');
          });
        });
      });
      $view->group('/datacenters', function (RouteCollectorProxy $datacenters)
      {
        $datacenters->map(['GET'], '', \App\v1\Controllers\Datacenter::class . ':getAll');
        $datacenters->map(['POST'], '', \App\v1\Controllers\Datacenter::class . ':postItem');
        $datacenters->group("/{id:[0-9]+}", function (RouteCollectorProxy $datacenterId)
        {
          $datacenterId->map(['GET'], '', \App\v1\Controllers\Datacenter::class . ':showItem');
          $datacenterId->map(['POST'], '', \App\v1\Controllers\Datacenter::class . ':updateItem');
          $datacenterId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'dcrooms', \App\v1\Controllers\Datacenter::class . ':showSubDcrooms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Datacenter::class . ':showSubHistory');
          });
        });
      });
      $view->group('/dcrooms', function (RouteCollectorProxy $dcrooms)
      {
        $dcrooms->map(['GET'], '', \App\v1\Controllers\Dcroom::class . ':getAll');
        $dcrooms->map(['POST'], '', \App\v1\Controllers\Dcroom::class . ':postItem');
        $dcrooms->group("/{id:[0-9]+}", function (RouteCollectorProxy $dcroomId)
        {
          $dcroomId->map(['GET'], '', \App\v1\Controllers\Dcroom::class . ':showItem');
          $dcroomId->map(['POST'], '', \App\v1\Controllers\Dcroom::class . ':updateItem');
          $dcroomId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Dcroom::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Dcroom::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Dcroom::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Dcroom::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Dcroom::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Datacenter::class . ':showSubHistory');
          });
        });
      });
      $view->group('/clusters', function (RouteCollectorProxy $clusters)
      {
        $clusters->map(['GET'], '', \App\v1\Controllers\Cluster::class . ':getAll');
        $clusters->map(['POST'], '', \App\v1\Controllers\Cluster::class . ':postItem');
        $clusters->group("/{id:[0-9]+}", function (RouteCollectorProxy $clusterId)
        {
          $clusterId->map(['GET'], '', \App\v1\Controllers\Cluster::class . ':showItem');
          $clusterId->map(['POST'], '', \App\v1\Controllers\Cluster::class . ':updateItem');
          $clusterId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Cluster::class . ':showSubAppliances');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Cluster::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Cluster::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Cluster::class . ':showSubItil');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Cluster::class . ':showSubItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Cluster::class . ':showSubHistory');
          });
        });
      });
      $view->group('/domains', function (RouteCollectorProxy $domains)
      {
        $domains->map(['GET'], '', \App\v1\Controllers\Domain::class . ':getAll');
        $domains->map(['POST'], '', \App\v1\Controllers\Domain::class . ':postItem');
        $domains->group("/{id:[0-9]+}", function (RouteCollectorProxy $domainId)
        {
          $domainId->map(['GET'], '', \App\v1\Controllers\Domain::class . ':showItem');
          $domainId->map(['POST'], '', \App\v1\Controllers\Domain::class . ':updateItem');
          $domainId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Domain::class . ':showSubCertificates');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Domain::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Domain::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Domain::class . ':showSubContracts');
            $sub->map(['GET'], 'records', \App\v1\Controllers\Domain::class . ':showSubRecords');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Domain::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Domain::class . ':showSubInfocoms');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Domain::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Domain::class . ':showSubHistory');
          });
        });
      });
      $view->group('/appliances', function (RouteCollectorProxy $viewliances)
      {
        $viewliances->map(['GET'], '', \App\v1\Controllers\Appliance::class . ':getAll');
        $viewliances->map(['POST'], '', \App\v1\Controllers\Appliance::class . ':postItem');
        $viewliances->group("/{id:[0-9]+}", function (RouteCollectorProxy $viewlianceId)
        {
          $viewlianceId->map(['GET'], '', \App\v1\Controllers\Appliance::class . ':showItem');
          $viewlianceId->map(['POST'], '', \App\v1\Controllers\Appliance::class . ':updateItem');
          $viewlianceId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Appliance::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Appliance::class . ':showSubDomains');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Appliance::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Appliance::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Appliance::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Appliance::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Appliance::class . ':showSubItil');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Appliance::class . ':showSubItems');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Appliance::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Appliance::class . ':showSubHistory');
          });
        });
      });


      $view->group('/projects', function (RouteCollectorProxy $projects)
      {
        $projects->map(['GET'], '', \App\v1\Controllers\Project::class . ':getAll');
        $projects->map(['POST'], '', \App\v1\Controllers\Project::class . ':postItem');
        $projects->group("/{id:[0-9]+}", function (RouteCollectorProxy $projectId)
        {
          $projectId->map(['GET'], '', \App\v1\Controllers\Project::class . ':showItem');
          $projectId->map(['POST'], '', \App\v1\Controllers\Project::class . ':updateItem');
          $projectId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Project::class . ':showSubNotes');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Project::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Project::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Project::class . ':showSubContracts');
            $sub->map(['GET'], 'projecttasks', \App\v1\Controllers\Project::class . ':showSubProjecttasks');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Project::class . ':showSubProjects');
            $sub->map(['GET'], 'projectteams', \App\v1\Controllers\Project::class . ':showSubProjectteams');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Project::class . ':showSubItems');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Project::class . ':showSubCosts');
            $sub->map(['GET'], 'itilitems', \App\v1\Controllers\Project::class . ':showSubItilitems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Project::class . ':showSubHistory');
          });
        });
      });
      $view->group('/reminders', function (RouteCollectorProxy $reminders)
      {
        $reminders->map(['GET'], '', \App\v1\Controllers\Reminder::class . ':getAll');
        $reminders->map(['POST'], '', \App\v1\Controllers\Reminder::class . ':postItem');
        $reminders->group("/{id:[0-9]+}", function (RouteCollectorProxy $reminderId)
        {
          $reminderId->map(['GET'], '', \App\v1\Controllers\Reminder::class . ':showItem');
          $reminderId->map(['POST'], '', \App\v1\Controllers\Reminder::class . ':updateItem');
          $reminderId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Reminder::class . ':showSubDocuments');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Reminder::class . ':showSubHistory');
          });
        });
      });
      $view->group('/rssfeeds', function (RouteCollectorProxy $rssfeeds)
      {
        $rssfeeds->map(['GET'], '', \App\v1\Controllers\Rssfeed::class . ':getAll');
        $rssfeeds->map(['POST'], '', \App\v1\Controllers\Rssfeed::class . ':postItem');
        $rssfeeds->group("/{id:[0-9]+}", function (RouteCollectorProxy $rssfeedId)
        {
          $rssfeedId->map(['GET'], '', \App\v1\Controllers\Rssfeed::class . ':showItem');
          $rssfeedId->map(['POST'], '', \App\v1\Controllers\Rssfeed::class . ':updateItem');
          $rssfeedId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Rssfeed::class . ':showSubHistory');
          });
        });
      });
      $view->group('/savedsearchs', function (RouteCollectorProxy $savedsearchs)
      {
        $savedsearchs->map(['GET'], '', \App\v1\Controllers\Savedsearch::class . ':getAll');
        $savedsearchs->map(['POST'], '', \App\v1\Controllers\Savedsearch::class . ':postItem');
        $savedsearchs->group("/{id:[0-9]+}", function (RouteCollectorProxy $savedsearchId)
        {
          $savedsearchId->map(['GET'], '', \App\v1\Controllers\Savedsearch::class . ':showItem');
          $savedsearchId->map(['POST'], '', \App\v1\Controllers\Savedsearch::class . ':updateItem');
        });
      });
      $view->group('/news', function (RouteCollectorProxy $news)
      {
        $news->map(['GET'], '', \App\v1\Controllers\News::class . ':getAll');
        $news->map(['POST'], '', \App\v1\Controllers\News::class . ':postItem');
        $news->group("/{id:[0-9]+}", function (RouteCollectorProxy $newsId)
        {
          $newsId->map(['GET'], '', \App\v1\Controllers\News::class . ':showItem');
          $newsId->map(['POST'], '', \App\v1\Controllers\News::class . ':updateItem');
        });
      });


      $view->group('/users', function (RouteCollectorProxy $users)
      {
        $users->map(['GET'], '', \App\v1\Controllers\User::class . ':getAll');
        $users->map(['POST'], '', \App\v1\Controllers\User::class . ':postItem');
        $users->group("/{id:[0-9]+}", function (RouteCollectorProxy $userId)
        {
          $userId->map(['GET'], '', \App\v1\Controllers\User::class . ':showItem');
          $userId->map(['POST'], '', \App\v1\Controllers\User::class . ':updateItem');

          $userId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'authorization', \App\v1\Controllers\User::class . ':showSubAuthorization');
            $sub->map(['POST'], 'authorization', \App\v1\Controllers\User::class . ':itemSubAuthorization');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\User::class . ':showSubCertificates');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\User::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\User::class . ':showSubDocuments');
            $sub->map(['GET'], 'groups', \App\v1\Controllers\User::class . ':showSubGroups');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\User::class . ':showSubItilTicketsCreated');
            $sub->map(['GET'], 'problems', \App\v1\Controllers\User::class . ':showSubItilProblems');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\User::class . ':showSubItilChanges');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\User::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\User::class . ':showSubHistory');
          });
        });
      });
      $view->group('/groups', function (RouteCollectorProxy $groups)
      {
        $groups->map(['GET'], '', \App\v1\Controllers\Group::class . ':getAll');
        $groups->map(['POST'], '', \App\v1\Controllers\Group::class . ':postItem');
        $groups->group("/{id:[0-9]+}", function (RouteCollectorProxy $groupId)
        {
          $groupId->map(['GET'], '', \App\v1\Controllers\Group::class . ':showItem');
          $groupId->map(['POST'], '', \App\v1\Controllers\Group::class . ':updateItem');
          $groupId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Group::class . ':showSubNotes');
            $sub->map(['GET'], 'users', \App\v1\Controllers\Group::class . ':showSubUsers');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Group::class . ':showSubItilTicketsCreated');
            $sub->map(['GET'], 'problems', \App\v1\Controllers\Group::class . ':showSubItilProblems');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\Group::class . ':showSubItilChanges');
            $sub->map(['GET'], 'groups', \App\v1\Controllers\Group::class . ':showSubGroups');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Group::class . ':showSubHistory');
          });
        });
      });
      $view->group('/entities', function (RouteCollectorProxy $entities)
      {
        $entities->map(['GET'], '', \App\v1\Controllers\Entity::class . ':getAll');
        $entities->map(['POST'], '', \App\v1\Controllers\Entity::class . ':postItem');
        $entities->group("/{id:[0-9]+}", function (RouteCollectorProxy $entityId)
        {
          $entityId->map(['GET'], '', \App\v1\Controllers\Entity::class . ':showItem');
          $entityId->map(['POST'], '', \App\v1\Controllers\Entity::class . ':updateItem');
          $entityId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Entity::class . ':showSubNotes');
            $sub->map(['GET'], 'knowbaseitems', \App\v1\Controllers\Entity::class . ':showSubKnowbaseitems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Entity::class . ':showSubDocuments');
            $sub->map(['GET'], 'address', \App\v1\Controllers\Entity::class . ':showSubAddress');
            $sub->map(['GET'], 'entities', \App\v1\Controllers\Entity::class . ':showSubEntities');
            $sub->map(['GET'], 'users', \App\v1\Controllers\Entity::class . ':showSubUsers');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Entity::class . ':showSubHistory');
          });
        });
      });
      $view->group('/rules', function (RouteCollectorProxy $rules)
      {
        $rules->group("/tickets", function (RouteCollectorProxy $tickets)
        {
          $tickets->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':getAll');
          $tickets->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketId)
          {
            $ticketId->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':showItem');

            $ticketId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'criteria', \App\v1\Controllers\Rules\Ticket::class . ':showCriteria');
            });
          });
        });
      });
      $view->group('/profiles', function (RouteCollectorProxy $profiles)
      {
        $profiles->map(['GET'], '', \App\v1\Controllers\Profile::class . ':getAll');
        $profiles->map(['POST'], '', \App\v1\Controllers\Profile::class . ':postItem');
        $profiles->group("/{id:[0-9]+}", function (RouteCollectorProxy $profileId)
        {
          $profileId->map(['GET'], '', \App\v1\Controllers\Profile::class . ':showItem');
          $profileId->map(['POST'], '', \App\v1\Controllers\Profile::class . ':updateItem');

          $profileId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'assets', \App\v1\Controllers\Profile::class . ':showSubAssets');
            $sub->map(['POST'], 'assets', \App\v1\Controllers\Profile::class . ':itemSubAssets');
            $sub->map(['GET'], 'assistance', \App\v1\Controllers\Profile::class . ':showSubAssistance');
            $sub->map(['POST'], 'assistance', \App\v1\Controllers\Profile::class . ':itemSubAssistance');
            $sub->map(['GET'], 'forms', \App\v1\Controllers\Profile::class . ':showSubForms');
            $sub->map(['POST'], 'forms', \App\v1\Controllers\Profile::class . ':itemSubForms');
            $sub->map(['GET'], 'management', \App\v1\Controllers\Profile::class . ':showSubManagement');
            $sub->map(['POST'], 'management', \App\v1\Controllers\Profile::class . ':itemSubManagement');
            $sub->map(['GET'], 'tools', \App\v1\Controllers\Profile::class . ':showSubTools');
            $sub->map(['POST'], 'tools', \App\v1\Controllers\Profile::class . ':itemSubTools');
            $sub->map(['GET'], 'administration', \App\v1\Controllers\Profile::class . ':showSubAdministration');
            $sub->map(['POST'], 'administration', \App\v1\Controllers\Profile::class . ':itemSubAdministration');
            $sub->map(['GET'], 'setup', \App\v1\Controllers\Profile::class . ':showSubSetup');
            $sub->map(['POST'], 'setup', \App\v1\Controllers\Profile::class . ':itemSubSetup');
            $sub->map(['GET'], 'users', \App\v1\Controllers\Profile::class . ':showSubUsers');

            $sub->map(['GET'], 'history', \App\v1\Controllers\Profile::class . ':showSubHistory');
          });
        });
      });
      $view->group('/queuednotifications', function (RouteCollectorProxy $queuednotifications)
      {
        $queuednotifications->map(['GET'], '', \App\v1\Controllers\Queuednotification::class . ':getAll');
        $queuednotifications->map(['POST'], '', \App\v1\Controllers\Queuednotification::class . ':postItem');
        $queuednotifications->group("/{id:[0-9]+}", function (RouteCollectorProxy $queuednotificationId)
        {
          $queuednotificationId->map(['GET'], '', \App\v1\Controllers\Queuednotification::class . ':showItem');
          $queuednotificationId->map(['POST'], '', \App\v1\Controllers\Queuednotification::class . ':updateItem');
        });
      });
      $view->group('/events', function (RouteCollectorProxy $events)
      {
        $events->map(['GET'], '', \App\v1\Controllers\Event::class . ':getAll');
        $events->map(['POST'], '', \App\v1\Controllers\Event::class . ':postItem');
        $events->group("/{id:[0-9]+}", function (RouteCollectorProxy $eventId)
        {
          $eventId->map(['GET'], '', \App\v1\Controllers\Event::class . ':showItem');
          $eventId->map(['POST'], '', \App\v1\Controllers\Event::class . ':updateItem');
        });
      });

      $view->group('/dropdowns', function (RouteCollectorProxy $dropdowns)
      {
        $dropdowns->group('/locations', function (RouteCollectorProxy $locations)
        {
          $locations->map(['GET'], '', \App\v1\Controllers\Location::class . ':getAll');
          $locations->map(['POST'], '', \App\v1\Controllers\Location::class . ':postItem');
          $locations->group("/{id:[0-9]+}", function (RouteCollectorProxy $locationId)
          {
            $locationId->map(['GET'], '', \App\v1\Controllers\Location::class . ':showItem');
            $locationId->map(['POST'], '', \App\v1\Controllers\Location::class . ':updateItem');
            $locationId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Location::class . ':showSubDocuments');
              $sub->map(['GET'], 'locations', \App\v1\Controllers\Location::class . ':showSubLocations');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Location::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/states', function (RouteCollectorProxy $states)
        {
          $states->map(['GET'], '', \App\v1\Controllers\State::class . ':getAll');
          $states->map(['POST'], '', \App\v1\Controllers\State::class . ':postItem');
          $states->group("/{id:[0-9]+}", function (RouteCollectorProxy $stateId)
          {
            $stateId->map(['GET'], '', \App\v1\Controllers\State::class . ':showItem');
            $stateId->map(['POST'], '', \App\v1\Controllers\State::class . ':updateItem');
            $stateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'states', \App\v1\Controllers\State::class . ':showSubStates');
              $sub->map(['GET'], 'history', \App\v1\Controllers\State::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/manufacturers', function (RouteCollectorProxy $manufacturers)
        {
          $manufacturers->map(['GET'], '', \App\v1\Controllers\Manufacturer::class . ':getAll');
          $manufacturers->map(['POST'], '', \App\v1\Controllers\Manufacturer::class . ':postItem');
          $manufacturers->group("/{id:[0-9]+}", function (RouteCollectorProxy $manufacturerId)
          {
            $manufacturerId->map(['GET'], '', \App\v1\Controllers\Manufacturer::class . ':showItem');
            $manufacturerId->map(['POST'], '', \App\v1\Controllers\Manufacturer::class . ':updateItem');
            $manufacturerId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Manufacturer::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/blacklists', function (RouteCollectorProxy $blacklists)
        {
          $blacklists->map(['GET'], '', \App\v1\Controllers\Blacklist::class . ':getAll');
          $blacklists->map(['POST'], '', \App\v1\Controllers\Blacklist::class . ':postItem');
          $blacklists->group("/{id:[0-9]+}", function (RouteCollectorProxy $blacklistId)
          {
            $blacklistId->map(['GET'], '', \App\v1\Controllers\Blacklist::class . ':showItem');
            $blacklistId->map(['POST'], '', \App\v1\Controllers\Blacklist::class . ':updateItem');
            $blacklistId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Blacklist::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/blacklistedmailcontents', function (RouteCollectorProxy $blacklistedmailcontents)
        {
          $blacklistedmailcontents->map(['GET'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':getAll');
          $blacklistedmailcontents->map(['POST'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':postItem');
          $blacklistedmailcontents->group("/{id:[0-9]+}", function (RouteCollectorProxy $blacklistedmailcontentId)
          {
            $blacklistedmailcontentId->map(
              ['GET'],
              '',
              \App\v1\Controllers\Blacklistedmailcontent::class . ':showItem'
            );
            $blacklistedmailcontentId->map(
              ['POST'],
              '',
              \App\v1\Controllers\Blacklistedmailcontent::class . ':updateItem'
            );
          });
        });
        $dropdowns->group('/categories', function (RouteCollectorProxy $categories)
        {
          $categories->map(['GET'], '', \App\v1\Controllers\Category::class . ':getAll');
          $categories->map(['POST'], '', \App\v1\Controllers\Category::class . ':postItem');
          $categories->group("/{id:[0-9]+}", function (RouteCollectorProxy $categoryId)
          {
            $categoryId->map(['GET'], '', \App\v1\Controllers\Category::class . ':showItem');
            $categoryId->map(['POST'], '', \App\v1\Controllers\Category::class . ':updateItem');
            $categoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'categories', \App\v1\Controllers\Category::class . ':showSubCategories');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Category::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/ticketemplates', function (RouteCollectorProxy $ticketemplates)
        {
          $ticketemplates->map(['GET'], '', \App\v1\Controllers\Tickettemplate::class . ':getAll');
          $ticketemplates->map(['POST'], '', \App\v1\Controllers\Tickettemplate::class . ':postItem');
          $ticketemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketemplateId)
          {
            $ticketemplateId->map(['GET'], '', \App\v1\Controllers\Tickettemplate::class . ':showItem');
            $ticketemplateId->map(['POST'], '', \App\v1\Controllers\Tickettemplate::class . ':updateItem');
            $ticketemplateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'mandatoryfields',
                \App\v1\Controllers\Tickettemplate::class . ':showSubMandatoryFields'
              );
              $sub->map(
                ['GET'],
                'predefinedfields',
                \App\v1\Controllers\Tickettemplate::class . ':showSubPredefinedFields'
              );
              $sub->map(['GET'], 'hiddenfields', \App\v1\Controllers\Tickettemplate::class . ':showSubHiddenFields');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Tickettemplate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/taskcategories', function (RouteCollectorProxy $taskcategories)
        {
          $taskcategories->map(['GET'], '', \App\v1\Controllers\Taskcategory::class . ':getAll');
          $taskcategories->map(['POST'], '', \App\v1\Controllers\Taskcategory::class . ':postItem');
          $taskcategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $taskcategoryId)
          {
            $taskcategoryId->map(['GET'], '', \App\v1\Controllers\Taskcategory::class . ':showItem');
            $taskcategoryId->map(['POST'], '', \App\v1\Controllers\Taskcategory::class . ':updateItem');
            $taskcategoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Taskcategory::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/tasktemplates', function (RouteCollectorProxy $tasktemplates)
        {
          $tasktemplates->map(['GET'], '', \App\v1\Controllers\Tasktemplate::class . ':getAll');
          $tasktemplates->map(['POST'], '', \App\v1\Controllers\Tasktemplate::class . ':postItem');
          $tasktemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $tasktemplateId)
          {
            $tasktemplateId->map(['GET'], '', \App\v1\Controllers\Tasktemplate::class . ':showItem');
            $tasktemplateId->map(['POST'], '', \App\v1\Controllers\Tasktemplate::class . ':updateItem');
            $tasktemplateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Tasktemplate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/solutiontypes', function (RouteCollectorProxy $solutiontypes)
        {
          $solutiontypes->map(['GET'], '', \App\v1\Controllers\Solutiontype::class . ':getAll');
          $solutiontypes->map(['POST'], '', \App\v1\Controllers\Solutiontype::class . ':postItem');
          $solutiontypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $solutiontypeId)
          {
            $solutiontypeId->map(['GET'], '', \App\v1\Controllers\Solutiontype::class . ':showItem');
            $solutiontypeId->map(['POST'], '', \App\v1\Controllers\Solutiontype::class . ':updateItem');
            $solutiontypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Solutiontype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/solutiontemplates', function (RouteCollectorProxy $solutiontemplates)
        {
          $solutiontemplates->map(['GET'], '', \App\v1\Controllers\Solutiontemplate::class . ':getAll');
          $solutiontemplates->map(['POST'], '', \App\v1\Controllers\Solutiontemplate::class . ':postItem');
          $solutiontemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $solutiontemplateId)
          {
            $solutiontemplateId->map(['GET'], '', \App\v1\Controllers\Solutiontemplate::class . ':showItem');
            $solutiontemplateId->map(['POST'], '', \App\v1\Controllers\Solutiontemplate::class . ':updateItem');
            $solutiontemplateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Solutiontemplate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/requesttypes', function (RouteCollectorProxy $requesttypes)
        {
          $requesttypes->map(['GET'], '', \App\v1\Controllers\Requesttype::class . ':getAll');
          $requesttypes->map(['POST'], '', \App\v1\Controllers\Requesttype::class . ':postItem');
          $requesttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $requesttypeId)
          {
            $requesttypeId->map(['GET'], '', \App\v1\Controllers\Requesttype::class . ':showItem');
            $requesttypeId->map(['POST'], '', \App\v1\Controllers\Requesttype::class . ':updateItem');
            $requesttypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Requesttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/followuptemplates', function (RouteCollectorProxy $followuptemplates)
        {
          $followuptemplates->map(['GET'], '', \App\v1\Controllers\Followuptemplate::class . ':getAll');
          $followuptemplates->map(['POST'], '', \App\v1\Controllers\Followuptemplate::class . ':postItem');
          $followuptemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $itilfollowuptemplateId)
          {
            $itilfollowuptemplateId->map(['GET'], '', \App\v1\Controllers\Followuptemplate::class . ':showItem');
            $itilfollowuptemplateId->map(['POST'], '', \App\v1\Controllers\Followuptemplate::class . ':updateItem');
            $itilfollowuptemplateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Followuptemplate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/projectstates', function (RouteCollectorProxy $projectstates)
        {
          $projectstates->map(['GET'], '', \App\v1\Controllers\Projectstate::class . ':getAll');
          $projectstates->map(['POST'], '', \App\v1\Controllers\Projectstate::class . ':postItem');
          $projectstates->group("/{id:[0-9]+}", function (RouteCollectorProxy $projectstateId)
          {
            $projectstateId->map(['GET'], '', \App\v1\Controllers\Projectstate::class . ':showItem');
            $projectstateId->map(['POST'], '', \App\v1\Controllers\Projectstate::class . ':updateItem');
            $projectstateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Projectstate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/projecttypes', function (RouteCollectorProxy $projecttypes)
        {
          $projecttypes->map(['GET'], '', \App\v1\Controllers\Projecttype::class . ':getAll');
          $projecttypes->map(['POST'], '', \App\v1\Controllers\Projecttype::class . ':postItem');
          $projecttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttypeId)
          {
            $projecttypeId->map(['GET'], '', \App\v1\Controllers\Projecttype::class . ':showItem');
            $projecttypeId->map(['POST'], '', \App\v1\Controllers\Projecttype::class . ':updateItem');
            $projecttypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/projecttasks', function (RouteCollectorProxy $projecttasks)
        {
          $projecttasks->map(['GET'], '', \App\v1\Controllers\Projecttask::class . ':getAll');
          $projecttasks->map(['POST'], '', \App\v1\Controllers\Projecttask::class . ':postItem');
          $projecttasks->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttaskId)
          {
            $projecttaskId->map(['GET'], '', \App\v1\Controllers\Projecttask::class . ':showItem');
            $projecttaskId->map(['POST'], '', \App\v1\Controllers\Projecttask::class . ':updateItem');
            $projecttaskId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'projecttasks', \App\v1\Controllers\Projecttask::class . ':showSubProjecttasks');
              $sub->map(
                ['GET'],
                'projecttaskteams',
                \App\v1\Controllers\Projecttask::class . ':showSubProjecttaskteams'
              );
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Projecttask::class . ':showSubDocuments');
              $sub->map(['GET'], 'notes', \App\v1\Controllers\Projecttask::class . ':showSubNotes');
              $sub->map(['GET'], 'tickets', \App\v1\Controllers\Projecttask::class . ':showSubTickets');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttask::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/projecttasktypes', function (RouteCollectorProxy $projecttasktypes)
        {
          $projecttasktypes->map(['GET'], '', \App\v1\Controllers\Projecttasktype::class . ':getAll');
          $projecttasktypes->map(['POST'], '', \App\v1\Controllers\Projecttasktype::class . ':postItem');
          $projecttasktypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttasktypeId)
          {
            $projecttasktypeId->map(['GET'], '', \App\v1\Controllers\Projecttasktype::class . ':showItem');
            $projecttasktypeId->map(['POST'], '', \App\v1\Controllers\Projecttasktype::class . ':updateItem');
            $projecttasktypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttasktype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/projecttasktemplates', function (RouteCollectorProxy $projecttasktemplates)
        {
          $projecttasktemplates->map(['GET'], '', \App\v1\Controllers\Projecttasktemplate::class . ':getAll');
          $projecttasktemplates->map(['POST'], '', \App\v1\Controllers\Projecttasktemplate::class . ':postItem');
          $projecttasktemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttasktemplateId)
          {
            $projecttasktemplateId->map(['GET'], '', \App\v1\Controllers\Projecttasktemplate::class . ':showItem');
            $projecttasktemplateId->map(['POST'], '', \App\v1\Controllers\Projecttasktemplate::class . ':updateItem');
            $projecttasktemplateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Projecttasktemplate::class . ':showSubDocuments');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttasktemplate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/planningeventcategories', function (RouteCollectorProxy $planningeventcategories)
        {
          $planningeventcategories->map(['GET'], '', \App\v1\Controllers\Planningeventcategory::class . ':getAll');
          $planningeventcategories->map(['POST'], '', \App\v1\Controllers\Planningeventcategory::class . ':postItem');
          $planningeventcategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $planningeventcategoryId)
          {
            $planningeventcategoryId->map(['GET'], '', \App\v1\Controllers\Planningeventcategory::class . ':showItem');
            $planningeventcategoryId->map(
              ['POST'],
              '',
              \App\v1\Controllers\Planningeventcategory::class . ':updateItem'
            );
            $planningeventcategoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Planningeventcategory::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/planningexternaleventtemplates', function (RouteCollectorProxy $pe_eventtemplates)
        {
          $pe_eventtemplates->map(['GET'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':getAll');
          $pe_eventtemplates->map(['POST'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':postItem');
          $pe_eventtemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $peetId)
          {
            $peetId->map(['GET'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':showItem');
            $peetId->map(['POST'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':updateItem');
            $peetId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'history',
                \App\v1\Controllers\Planningexternaleventtemplate::class . ':showSubHistory'
              );
            });
          });
        });
        $dropdowns->group('/computertypes', function (RouteCollectorProxy $computertypes)
        {
          $computertypes->map(['GET'], '', \App\v1\Controllers\Computertype::class . ':getAll');
          $computertypes->map(['POST'], '', \App\v1\Controllers\Computertype::class . ':postItem');
          $computertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $computertypeId)
          {
            $computertypeId->map(['GET'], '', \App\v1\Controllers\Computertype::class . ':showItem');
            $computertypeId->map(['POST'], '', \App\v1\Controllers\Computertype::class . ':updateItem');
            $computertypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Computertype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/networkequipmenttypes', function (RouteCollectorProxy $networkequipmenttypes)
        {
          $networkequipmenttypes->map(['GET'], '', \App\v1\Controllers\Networkequipmenttype::class . ':getAll');
          $networkequipmenttypes->map(['POST'], '', \App\v1\Controllers\Networkequipmenttype::class . ':postItem');
          $networkequipmenttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkequipmenttypeId)
          {
            $networkequipmenttypeId->map(['GET'], '', \App\v1\Controllers\Networkequipmenttype::class . ':showItem');
            $networkequipmenttypeId->map(['POST'], '', \App\v1\Controllers\Networkequipmenttype::class . ':updateItem');
            $networkequipmenttypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Networkequipmenttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/printertypes', function (RouteCollectorProxy $printertypes)
        {
          $printertypes->map(['GET'], '', \App\v1\Controllers\Printertype::class . ':getAll');
          $printertypes->map(['POST'], '', \App\v1\Controllers\Printertype::class . ':postItem');
          $printertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $printertypeId)
          {
            $printertypeId->map(['GET'], '', \App\v1\Controllers\Printertype::class . ':showItem');
            $printertypeId->map(['POST'], '', \App\v1\Controllers\Printertype::class . ':updateItem');
            $printertypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Printertype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/monitortypes', function (RouteCollectorProxy $monitortypes)
        {
          $monitortypes->map(['GET'], '', \App\v1\Controllers\Monitortype::class . ':getAll');
          $monitortypes->map(['POST'], '', \App\v1\Controllers\Monitortype::class . ':postItem');
          $monitortypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $monitortypeId)
          {
            $monitortypeId->map(['GET'], '', \App\v1\Controllers\Monitortype::class . ':showItem');
            $monitortypeId->map(['POST'], '', \App\v1\Controllers\Monitortype::class . ':updateItem');
            $monitortypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Monitortype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/peripheraltypes', function (RouteCollectorProxy $peripheraltypes)
        {
          $peripheraltypes->map(['GET'], '', \App\v1\Controllers\Peripheraltype::class . ':getAll');
          $peripheraltypes->map(['POST'], '', \App\v1\Controllers\Peripheraltype::class . ':postItem');
          $peripheraltypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $peripheraltypeId)
          {
            $peripheraltypeId->map(['GET'], '', \App\v1\Controllers\Peripheraltype::class . ':showItem');
            $peripheraltypeId->map(['POST'], '', \App\v1\Controllers\Peripheraltype::class . ':updateItem');
            $peripheraltypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Peripheraltype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/phonetypes', function (RouteCollectorProxy $phonetypes)
        {
          $phonetypes->map(['GET'], '', \App\v1\Controllers\Phonetype::class . ':getAll');
          $phonetypes->map(['POST'], '', \App\v1\Controllers\Phonetype::class . ':postItem');
          $phonetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $phonetypeId)
          {
            $phonetypeId->map(['GET'], '', \App\v1\Controllers\Phonetype::class . ':showItem');
            $phonetypeId->map(['POST'], '', \App\v1\Controllers\Phonetype::class . ':updateItem');
            $phonetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Phonetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/softwarelicensetypes', function (RouteCollectorProxy $softwarelicensetypes)
        {
          $softwarelicensetypes->map(['GET'], '', \App\v1\Controllers\Softwarelicensetype::class . ':getAll');
          $softwarelicensetypes->map(['POST'], '', \App\v1\Controllers\Softwarelicensetype::class . ':postItem');
          $softwarelicensetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwarelicensetypeId)
          {
            $softwarelicensetypeId->map(['GET'], '', \App\v1\Controllers\Softwarelicensetype::class . ':showItem');
            $softwarelicensetypeId->map(['POST'], '', \App\v1\Controllers\Softwarelicensetype::class . ':updateItem');
            $softwarelicensetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'licencetypes',
                \App\v1\Controllers\Softwarelicensetype::class . ':showSubLicencetypes'
              );
              $sub->map(['GET'], 'history', \App\v1\Controllers\Softwarelicensetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/cartridgeitemtypes', function (RouteCollectorProxy $cartridgeitemtypes)
        {
          $cartridgeitemtypes->map(['GET'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':getAll');
          $cartridgeitemtypes->map(['POST'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':postItem');
          $cartridgeitemtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $cartridgeitemtypeId)
          {
            $cartridgeitemtypeId->map(['GET'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':showItem');
            $cartridgeitemtypeId->map(['POST'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':updateItem');
            $cartridgeitemtypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Cartridgeitemtype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/consumableitemtypes', function (RouteCollectorProxy $consumableitemtypes)
        {
          $consumableitemtypes->map(['GET'], '', \App\v1\Controllers\Consumableitemtype::class . ':getAll');
          $consumableitemtypes->map(['POST'], '', \App\v1\Controllers\Consumableitemtype::class . ':postItem');
          $consumableitemtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $consumableitemtypeId)
          {
            $consumableitemtypeId->map(['GET'], '', \App\v1\Controllers\Consumableitemtype::class . ':showItem');
            $consumableitemtypeId->map(['POST'], '', \App\v1\Controllers\Consumableitemtype::class . ':updateItem');
            $consumableitemtypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Consumableitemtype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/contracttypes', function (RouteCollectorProxy $contracttypes)
        {
          $contracttypes->map(['GET'], '', \App\v1\Controllers\Contracttype::class . ':getAll');
          $contracttypes->map(['POST'], '', \App\v1\Controllers\Contracttype::class . ':postItem');
          $contracttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $contracttypeId)
          {
            $contracttypeId->map(['GET'], '', \App\v1\Controllers\Contracttype::class . ':showItem');
            $contracttypeId->map(['POST'], '', \App\v1\Controllers\Contracttype::class . ':updateItem');
            $contracttypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Contracttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/contacttypes', function (RouteCollectorProxy $contacttypes)
        {
          $contacttypes->map(['GET'], '', \App\v1\Controllers\Contacttype::class . ':getAll');
          $contacttypes->map(['POST'], '', \App\v1\Controllers\Contacttype::class . ':postItem');
          $contacttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $contacttypeId)
          {
            $contacttypeId->map(['GET'], '', \App\v1\Controllers\Contacttype::class . ':showItem');
            $contacttypeId->map(['POST'], '', \App\v1\Controllers\Contacttype::class . ':updateItem');
            $contacttypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Contacttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicegenerictype', function (RouteCollectorProxy $devicegenerictype)
        {
          $devicegenerictype->map(['GET'], '', \App\v1\Controllers\Devicegenerictype::class . ':getAll');
          $devicegenerictype->map(['POST'], '', \App\v1\Controllers\Devicegenerictype::class . ':postItem');
          $devicegenerictype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegenerictypeId)
          {
            $devicegenerictypeId->map(['GET'], '', \App\v1\Controllers\Devicegenerictype::class . ':showItem');
            $devicegenerictypeId->map(['POST'], '', \App\v1\Controllers\Devicegenerictype::class . ':updateItem');
            $devicegenerictypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegenerictype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicesensortype', function (RouteCollectorProxy $devicesensortype)
        {
          $devicesensortype->map(['GET'], '', \App\v1\Controllers\Devicesensortype::class . ':getAll');
          $devicesensortype->map(['POST'], '', \App\v1\Controllers\Devicesensortype::class . ':postItem');
          $devicesensortype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesensortypeId)
          {
            $devicesensortypeId->map(['GET'], '', \App\v1\Controllers\Devicesensortype::class . ':showItem');
            $devicesensortypeId->map(['POST'], '', \App\v1\Controllers\Devicesensortype::class . ':updateItem');
            $devicesensortypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesensortype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicememorytype', function (RouteCollectorProxy $devicememorytype)
        {
          $devicememorytype->map(['GET'], '', \App\v1\Controllers\Devicememorytype::class . ':getAll');
          $devicememorytype->map(['POST'], '', \App\v1\Controllers\Devicememorytype::class . ':postItem');
          $devicememorytype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicememorytypeId)
          {
            $devicememorytypeId->map(['GET'], '', \App\v1\Controllers\Devicememorytype::class . ':showItem');
            $devicememorytypeId->map(['POST'], '', \App\v1\Controllers\Devicememorytype::class . ':updateItem');
            $devicememorytypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicememorytype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/suppliertypes', function (RouteCollectorProxy $suppliertypes)
        {
          $suppliertypes->map(['GET'], '', \App\v1\Controllers\Suppliertype::class . ':getAll');
          $suppliertypes->map(['POST'], '', \App\v1\Controllers\Suppliertype::class . ':postItem');
          $suppliertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $suppliertypeId)
          {
            $suppliertypeId->map(['GET'], '', \App\v1\Controllers\Suppliertype::class . ':showItem');
            $suppliertypeId->map(['POST'], '', \App\v1\Controllers\Suppliertype::class . ':updateItem');
            $suppliertypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Suppliertype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/interfacetypes', function (RouteCollectorProxy $interfacetypes)
        {
          $interfacetypes->map(['GET'], '', \App\v1\Controllers\Interfacetype::class . ':getAll');
          $interfacetypes->map(['POST'], '', \App\v1\Controllers\Interfacetype::class . ':postItem');
          $interfacetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $interfacetypeId)
          {
            $interfacetypeId->map(['GET'], '', \App\v1\Controllers\Interfacetype::class . ':showItem');
            $interfacetypeId->map(['POST'], '', \App\v1\Controllers\Interfacetype::class . ':updateItem');
            $interfacetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Interfacetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicecasetype', function (RouteCollectorProxy $devicecasetype)
        {
          $devicecasetype->map(['GET'], '', \App\v1\Controllers\Devicecasetype::class . ':getAll');
          $devicecasetype->map(['POST'], '', \App\v1\Controllers\Devicecasetype::class . ':postItem');
          $devicecasetype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecasetypeId)
          {
            $devicecasetypeId->map(['GET'], '', \App\v1\Controllers\Devicecasetype::class . ':showItem');
            $devicecasetypeId->map(['POST'], '', \App\v1\Controllers\Devicecasetype::class . ':updateItem');
            $devicecasetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecasetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/phonepowersupplies', function (RouteCollectorProxy $phonepowersupplies)
        {
          $phonepowersupplies->map(['GET'], '', \App\v1\Controllers\Phonepowersupply::class . ':getAll');
          $phonepowersupplies->map(['POST'], '', \App\v1\Controllers\Phonepowersupply::class . ':postItem');
          $phonepowersupplies->group("/{id:[0-9]+}", function (RouteCollectorProxy $phonepowersupplyId)
          {
            $phonepowersupplyId->map(['GET'], '', \App\v1\Controllers\Phonepowersupply::class . ':showItem');
            $phonepowersupplyId->map(['POST'], '', \App\v1\Controllers\Phonepowersupply::class . ':updateItem');
            $phonepowersupplyId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Phonepowersupply::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/filesystems', function (RouteCollectorProxy $filesystems)
        {
          $filesystems->map(['GET'], '', \App\v1\Controllers\Filesystem::class . ':getAll');
          $filesystems->map(['POST'], '', \App\v1\Controllers\Filesystem::class . ':postItem');
          $filesystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $filesystemId)
          {
            $filesystemId->map(['GET'], '', \App\v1\Controllers\Filesystem::class . ':showItem');
            $filesystemId->map(['POST'], '', \App\v1\Controllers\Filesystem::class . ':updateItem');
            $filesystemId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Filesystem::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/certificatetypes', function (RouteCollectorProxy $certificatetypes)
        {
          $certificatetypes->map(['GET'], '', \App\v1\Controllers\Certificatetype::class . ':getAll');
          $certificatetypes->map(['POST'], '', \App\v1\Controllers\Certificatetype::class . ':postItem');
          $certificatetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $certificatetypeId)
          {
            $certificatetypeId->map(['GET'], '', \App\v1\Controllers\Certificatetype::class . ':showItem');
            $certificatetypeId->map(['POST'], '', \App\v1\Controllers\Certificatetype::class . ':updateItem');
            $certificatetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Certificatetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/budgettypes', function (RouteCollectorProxy $budgettypes)
        {
          $budgettypes->map(['GET'], '', \App\v1\Controllers\Budgettype::class . ':getAll');
          $budgettypes->map(['POST'], '', \App\v1\Controllers\Budgettype::class . ':postItem');
          $budgettypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $budgettypeId)
          {
            $budgettypeId->map(['GET'], '', \App\v1\Controllers\Budgettype::class . ':showItem');
            $budgettypeId->map(['POST'], '', \App\v1\Controllers\Budgettype::class . ':updateItem');
            $budgettypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Budgettype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicesimcardtypes', function (RouteCollectorProxy $devicesimcardtypes)
        {
          $devicesimcardtypes->map(['GET'], '', \App\v1\Controllers\Devicesimcardtype::class . ':getAll');
          $devicesimcardtypes->map(['POST'], '', \App\v1\Controllers\Devicesimcardtype::class . ':postItem');
          $devicesimcardtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesimcardtypeId)
          {
            $devicesimcardtypeId->map(['GET'], '', \App\v1\Controllers\Devicesimcardtype::class . ':showItem');
            $devicesimcardtypeId->map(['POST'], '', \App\v1\Controllers\Devicesimcardtype::class . ':updateItem');
            $devicesimcardtypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesimcardtype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/linetypes', function (RouteCollectorProxy $linetypes)
        {
          $linetypes->map(['GET'], '', \App\v1\Controllers\Linetype::class . ':getAll');
          $linetypes->map(['POST'], '', \App\v1\Controllers\Linetype::class . ':postItem');
          $linetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $linetypeId)
          {
            $linetypeId->map(['GET'], '', \App\v1\Controllers\Linetype::class . ':showItem');
            $linetypeId->map(['POST'], '', \App\v1\Controllers\Linetype::class . ':updateItem');
            $linetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Linetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/racktypes', function (RouteCollectorProxy $racktypes)
        {
          $racktypes->map(['GET'], '', \App\v1\Controllers\Racktype::class . ':getAll');
          $racktypes->map(['POST'], '', \App\v1\Controllers\Racktype::class . ':postItem');
          $racktypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $racktypeId)
          {
            $racktypeId->map(['GET'], '', \App\v1\Controllers\Racktype::class . ':showItem');
            $racktypeId->map(['POST'], '', \App\v1\Controllers\Racktype::class . ':updateItem');
            $racktypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Racktype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/pdutypes', function (RouteCollectorProxy $pdutypes)
        {
          $pdutypes->map(['GET'], '', \App\v1\Controllers\Pdutype::class . ':getAll');
          $pdutypes->map(['POST'], '', \App\v1\Controllers\Pdutype::class . ':postItem');
          $pdutypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $pdutypeId)
          {
            $pdutypeId->map(['GET'], '', \App\v1\Controllers\Pdutype::class . ':showItem');
            $pdutypeId->map(['POST'], '', \App\v1\Controllers\Pdutype::class . ':updateItem');
            $pdutypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Pdutype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/passivedcequipmenttypes', function (RouteCollectorProxy $passivedcequipmenttypes)
        {
          $passivedcequipmenttypes->map(['GET'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':getAll');
          $passivedcequipmenttypes->map(['POST'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':postItem');
          $passivedcequipmenttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $pcetId)
          {
            $pcetId->map(['GET'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':showItem');
            $pcetId->map(['POST'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':updateItem');
            $pcetId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Passivedcequipmenttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/clustertypes', function (RouteCollectorProxy $clustertypes)
        {
          $clustertypes->map(['GET'], '', \App\v1\Controllers\Clustertype::class . ':getAll');
          $clustertypes->map(['POST'], '', \App\v1\Controllers\Clustertype::class . ':postItem');
          $clustertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $clustertypeId)
          {
            $clustertypeId->map(['GET'], '', \App\v1\Controllers\Clustertype::class . ':showItem');
            $clustertypeId->map(['POST'], '', \App\v1\Controllers\Clustertype::class . ':updateItem');
            $clustertypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Clustertype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/computermodels', function (RouteCollectorProxy $computermodels)
        {
          $computermodels->map(['GET'], '', \App\v1\Controllers\Computermodel::class . ':getAll');
          $computermodels->map(['POST'], '', \App\v1\Controllers\Computermodel::class . ':postItem');
          $computermodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $computermodelId)
          {
            $computermodelId->map(['GET'], '', \App\v1\Controllers\Computermodel::class . ':showItem');
            $computermodelId->map(['POST'], '', \App\v1\Controllers\Computermodel::class . ':updateItem');
            $computermodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Computermodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/networkequipmentmodels', function (RouteCollectorProxy $networkequipmentmodels)
        {
          $networkequipmentmodels->map(['GET'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':getAll');
          $networkequipmentmodels->map(['POST'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':postItem');
          $networkequipmentmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkequipmentmodelId)
          {
            $networkequipmentmodelId->map(['GET'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':showItem');
            $networkequipmentmodelId->map(
              ['POST'],
              '',
              \App\v1\Controllers\Networkequipmentmodel::class . ':updateItem'
            );
            $networkequipmentmodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Networkequipmentmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/printermodels', function (RouteCollectorProxy $printermodels)
        {
          $printermodels->map(['GET'], '', \App\v1\Controllers\Printermodel::class . ':getAll');
          $printermodels->map(['POST'], '', \App\v1\Controllers\Printermodel::class . ':postItem');
          $printermodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $printermodelId)
          {
            $printermodelId->map(['GET'], '', \App\v1\Controllers\Printermodel::class . ':showItem');
            $printermodelId->map(['POST'], '', \App\v1\Controllers\Printermodel::class . ':updateItem');
            $printermodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Printermodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/monitormodels', function (RouteCollectorProxy $monitormodels)
        {
          $monitormodels->map(['GET'], '', \App\v1\Controllers\Monitormodel::class . ':getAll');
          $monitormodels->map(['POST'], '', \App\v1\Controllers\Monitormodel::class . ':postItem');
          $monitormodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $monitormodelId)
          {
            $monitormodelId->map(['GET'], '', \App\v1\Controllers\Monitormodel::class . ':showItem');
            $monitormodelId->map(['POST'], '', \App\v1\Controllers\Monitormodel::class . ':updateItem');
            $monitormodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Monitormodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/peripheralmodels', function (RouteCollectorProxy $peripheralmodels)
        {
          $peripheralmodels->map(['GET'], '', \App\v1\Controllers\Peripheralmodel::class . ':getAll');
          $peripheralmodels->map(['POST'], '', \App\v1\Controllers\Peripheralmodel::class . ':postItem');
          $peripheralmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $peripheralmodelId)
          {
            $peripheralmodelId->map(['GET'], '', \App\v1\Controllers\Peripheralmodel::class . ':showItem');
            $peripheralmodelId->map(['POST'], '', \App\v1\Controllers\Peripheralmodel::class . ':updateItem');
            $peripheralmodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Peripheralmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/phonemodels', function (RouteCollectorProxy $phonemodels)
        {
          $phonemodels->map(['GET'], '', \App\v1\Controllers\Phonemodel::class . ':getAll');
          $phonemodels->map(['POST'], '', \App\v1\Controllers\Phonemodel::class . ':postItem');
          $phonemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $phonemodelId)
          {
            $phonemodelId->map(['GET'], '', \App\v1\Controllers\Phonemodel::class . ':showItem');
            $phonemodelId->map(['POST'], '', \App\v1\Controllers\Phonemodel::class . ':updateItem');
            $phonemodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Phonemodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicecasemodels', function (RouteCollectorProxy $devicecasemodels)
        {
          $devicecasemodels->map(['GET'], '', \App\v1\Controllers\Devicecasemodel::class . ':getAll');
          $devicecasemodels->map(['POST'], '', \App\v1\Controllers\Devicecasemodel::class . ':postItem');
          $devicecasemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecasemodelId)
          {
            $devicecasemodelId->map(['GET'], '', \App\v1\Controllers\Devicecasemodel::class . ':showItem');
            $devicecasemodelId->map(['POST'], '', \App\v1\Controllers\Devicecasemodel::class . ':updateItem');
            $devicecasemodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecasemodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicecontrolmodels', function (RouteCollectorProxy $devicecontrolmodels)
        {
          $devicecontrolmodels->map(['GET'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':getAll');
          $devicecontrolmodels->map(['POST'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':postItem');
          $devicecontrolmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecontrolmodelId)
          {
            $devicecontrolmodelId->map(['GET'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':showItem');
            $devicecontrolmodelId->map(['POST'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':updateItem');
            $devicecontrolmodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecontrolmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicedrivemodels', function (RouteCollectorProxy $devicedrivemodels)
        {
          $devicedrivemodels->map(['GET'], '', \App\v1\Controllers\Devicedrivemodel::class . ':getAll');
          $devicedrivemodels->map(['POST'], '', \App\v1\Controllers\Devicedrivemodel::class . ':postItem');
          $devicedrivemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicedrivemodelId)
          {
            $devicedrivemodelId->map(['GET'], '', \App\v1\Controllers\Devicedrivemodel::class . ':showItem');
            $devicedrivemodelId->map(['POST'], '', \App\v1\Controllers\Devicedrivemodel::class . ':updateItem');
            $devicedrivemodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicedrivemodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicegenericmodels', function (RouteCollectorProxy $devicegenericmodels)
        {
          $devicegenericmodels->map(['GET'], '', \App\v1\Controllers\Devicegenericmodel::class . ':getAll');
          $devicegenericmodels->map(['POST'], '', \App\v1\Controllers\Devicegenericmodel::class . ':postItem');
          $devicegenericmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegenericmodelId)
          {
            $devicegenericmodelId->map(['GET'], '', \App\v1\Controllers\Devicegenericmodel::class . ':showItem');
            $devicegenericmodelId->map(['POST'], '', \App\v1\Controllers\Devicegenericmodel::class . ':updateitem');
            $devicegenericmodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegenericmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicegraphiccardmodels', function (RouteCollectorProxy $devicegraphiccardmodels)
        {
          $devicegraphiccardmodels->map(['GET'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':getAll');
          $devicegraphiccardmodels->map(['POST'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':postItem');
          $devicegraphiccardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dgcmId)
          {
            $dgcmId->map(['GET'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':showItem');
            $dgcmId->map(['POST'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':updateItem');
            $dgcmId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegraphiccardmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/deviceharddrivemodels', function (RouteCollectorProxy $deviceharddrivemodels)
        {
          $deviceharddrivemodels->map(['GET'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':getAll');
          $deviceharddrivemodels->map(['POST'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':postItem');
          $deviceharddrivemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceharddrivemodelId)
          {
            $deviceharddrivemodelId->map(['GET'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':showItem');
            $deviceharddrivemodelId->map(['POST'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':updateItem');
            $deviceharddrivemodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceharddrivemodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicememorymodels', function (RouteCollectorProxy $devicememorymodels)
        {
          $devicememorymodels->map(['GET'], '', \App\v1\Controllers\Devicememorymodel::class . ':getAll');
          $devicememorymodels->map(['POST'], '', \App\v1\Controllers\Devicememorymodel::class . ':postItem');
          $devicememorymodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicememorymodelId)
          {
            $devicememorymodelId->map(['GET'], '', \App\v1\Controllers\Devicememorymodel::class . ':showItem');
            $devicememorymodelId->map(['POST'], '', \App\v1\Controllers\Devicememorymodel::class . ':updateItem');
            $devicememorymodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicememorymodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicemotherboardmodels', function (RouteCollectorProxy $devicemotherboardmodels)
        {
          $devicemotherboardmodels->map(['GET'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':getAll');
          $devicemotherboardmodels->map(['POST'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':postItem');
          $devicemotherboardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dmmId)
          {
            $dmmId->map(['GET'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':showItem');
            $dmmId->map(['POST'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':updateItem');
            $dmmId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicemotherboardmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicenetworkcardmodels', function (RouteCollectorProxy $devicenetworkcardmodels)
        {
          $devicenetworkcardmodels->map(['GET'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':getAll');
          $devicenetworkcardmodels->map(['POST'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':postItem');
          $devicenetworkcardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dncmId)
          {
            $dncmId->map(['GET'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':showItem');
            $dncmId->map(['POST'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':updateItem');
            $dncmId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicenetworkcardmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicepcimodels', function (RouteCollectorProxy $devicepcimodels)
        {
          $devicepcimodels->map(['GET'], '', \App\v1\Controllers\Devicepcimodel::class . ':getAll');
          $devicepcimodels->map(['POST'], '', \App\v1\Controllers\Devicepcimodel::class . ':postItem');
          $devicepcimodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicepcimodelId)
          {
            $devicepcimodelId->map(['GET'], '', \App\v1\Controllers\Devicepcimodel::class . ':showItem');
            $devicepcimodelId->map(['POST'], '', \App\v1\Controllers\Devicepcimodel::class . ':updateItem');
            $devicepcimodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepcimodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicepowersupplymodels', function (RouteCollectorProxy $devicepowersupplymodels)
        {
          $devicepowersupplymodels->map(['GET'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':getAll');
          $devicepowersupplymodels->map(['POST'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':postItem');
          $devicepowersupplymodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dpsmId)
          {
            $dpsmId->map(['GET'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':showItem');
            $dpsmId->map(['POST'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':updateItem');
            $dpsmId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepowersupplymodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/deviceprocessormodels', function (RouteCollectorProxy $deviceprocessormodels)
        {
          $deviceprocessormodels->map(['GET'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':getAll');
          $deviceprocessormodels->map(['POST'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':postItem');
          $deviceprocessormodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceprocessormodelId)
          {
            $deviceprocessormodelId->map(['GET'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':showItem');
            $deviceprocessormodelId->map(['POST'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':updateItem');
            $deviceprocessormodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceprocessormodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicesoundcardmodels', function (RouteCollectorProxy $devicesoundcardmodels)
        {
          $devicesoundcardmodels->map(['GET'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':getAll');
          $devicesoundcardmodels->map(['POST'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':postItem');
          $devicesoundcardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesoundcardmodelId)
          {
            $devicesoundcardmodelId->map(['GET'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':showItem');
            $devicesoundcardmodelId->map(['POST'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':updateItem');
            $devicesoundcardmodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesoundcardmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/devicesensormodels', function (RouteCollectorProxy $devicesensormodels)
        {
          $devicesensormodels->map(['GET'], '', \App\v1\Controllers\Devicesensormodel::class . ':getAll');
          $devicesensormodels->map(['POST'], '', \App\v1\Controllers\Devicesensormodel::class . ':postItem');
          $devicesensormodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesensormodelId)
          {
            $devicesensormodelId->map(['GET'], '', \App\v1\Controllers\Devicesensormodel::class . ':showItem');
            $devicesensormodelId->map(['POST'], '', \App\v1\Controllers\Devicesensormodel::class . ':updateItem');
            $devicesensormodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesensormodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/rackmodels', function (RouteCollectorProxy $rackmodels)
        {
          $rackmodels->map(['GET'], '', \App\v1\Controllers\Rackmodel::class . ':getAll');
          $rackmodels->map(['POST'], '', \App\v1\Controllers\Rackmodel::class . ':postItem');
          $rackmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $rackmodelId)
          {
            $rackmodelId->map(['GET'], '', \App\v1\Controllers\Rackmodel::class . ':showItem');
            $rackmodelId->map(['POST'], '', \App\v1\Controllers\Rackmodel::class . ':updateItem');
            $rackmodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Rackmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/enclosuremodels', function (RouteCollectorProxy $enclosuremodels)
        {
          $enclosuremodels->map(['GET'], '', \App\v1\Controllers\Enclosuremodel::class . ':getAll');
          $enclosuremodels->map(['POST'], '', \App\v1\Controllers\Enclosuremodel::class . ':postItem');
          $enclosuremodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $enclosuremodelId)
          {
            $enclosuremodelId->map(['GET'], '', \App\v1\Controllers\Enclosuremodel::class . ':showItem');
            $enclosuremodelId->map(['POST'], '', \App\v1\Controllers\Enclosuremodel::class . ':updateItem');
            $enclosuremodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Enclosuremodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/pdumodels', function (RouteCollectorProxy $pdumodels)
        {
          $pdumodels->map(['GET'], '', \App\v1\Controllers\Pdumodel::class . ':getAll');
          $pdumodels->map(['POST'], '', \App\v1\Controllers\Pdumodel::class . ':postItem');
          $pdumodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $pdumodelId)
          {
            $pdumodelId->map(['GET'], '', \App\v1\Controllers\Pdumodel::class . ':showItem');
            $pdumodelId->map(['POST'], '', \App\v1\Controllers\Pdumodel::class . ':updateItem');
            $pdumodelId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Pdumodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/passivedcequipmentmodels', function (RouteCollectorProxy $passivedcequipmentmodels)
        {
          $passivedcequipmentmodels->map(['GET'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':getAll');
          $passivedcequipmentmodels->map(
            ['POST'],
            '',
            \App\v1\Controllers\Passivedcequipmentmodel::class . ':postItem'
          );
          $passivedcequipmentmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $pcemId)
          {
            $pcemId->map(['GET'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':showItem');
            $pcemId->map(['POST'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':updateItem');
            $pcemId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Passivedcequipmentmodel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/virtualmachinetypes', function (RouteCollectorProxy $virtualmachinetypes)
        {
          $virtualmachinetypes->map(['GET'], '', \App\v1\Controllers\Virtualmachinetype::class . ':getAll');
          $virtualmachinetypes->map(['POST'], '', \App\v1\Controllers\Virtualmachinetype::class . ':postItem');
          $virtualmachinetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $virtualmachinetypeId)
          {
            $virtualmachinetypeId->map(['GET'], '', \App\v1\Controllers\Virtualmachinetype::class . ':showItem');
            $virtualmachinetypeId->map(['POST'], '', \App\v1\Controllers\Virtualmachinetype::class . ':updateItem');
            $virtualmachinetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Virtualmachinetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/virtualmachinesystems', function (RouteCollectorProxy $virtualmachinesystems)
        {
          $virtualmachinesystems->map(['GET'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':getAll');
          $virtualmachinesystems->map(['POST'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':postItem');
          $virtualmachinesystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $virtualmachinesystemId)
          {
            $virtualmachinesystemId->map(['GET'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':showItem');
            $virtualmachinesystemId->map(['POST'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':updateItem');
            $virtualmachinesystemId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Virtualmachinesystem::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/virtualmachinestates', function (RouteCollectorProxy $virtualmachinestates)
        {
          $virtualmachinestates->map(['GET'], '', \App\v1\Controllers\Virtualmachinestate::class . ':getAll');
          $virtualmachinestates->map(['POST'], '', \App\v1\Controllers\Virtualmachinestate::class . ':postItem');
          $virtualmachinestates->group("/{id:[0-9]+}", function (RouteCollectorProxy $virtualmachinestateId)
          {
            $virtualmachinestateId->map(['GET'], '', \App\v1\Controllers\Virtualmachinestate::class . ':showItem');
            $virtualmachinestateId->map(['POST'], '', \App\v1\Controllers\Virtualmachinestate::class . ':updateItem');
            $virtualmachinestateId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Virtualmachinestate::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/documentcategories', function (RouteCollectorProxy $documentcategories)
        {
          $documentcategories->map(['GET'], '', \App\v1\Controllers\Documentcategory::class . ':getAll');
          $documentcategories->map(['POST'], '', \App\v1\Controllers\Documentcategory::class . ':postItem');
          $documentcategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $documentcategoryId)
          {
            $documentcategoryId->map(['GET'], '', \App\v1\Controllers\Documentcategory::class . ':showItem');
            $documentcategoryId->map(['POST'], '', \App\v1\Controllers\Documentcategory::class . ':updateItem');
            $documentcategoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'categories',
                \App\v1\Controllers\Documentcategory::class . ':showSubDocumentcategories'
              );
              $sub->map(['GET'], 'history', \App\v1\Controllers\Documentcategory::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/documenttypes', function (RouteCollectorProxy $documenttypes)
        {
          $documenttypes->map(['GET'], '', \App\v1\Controllers\Documenttype::class . ':getAll');
          $documenttypes->map(['POST'], '', \App\v1\Controllers\Documenttype::class . ':postItem');
          $documenttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $documenttypeId)
          {
            $documenttypeId->map(['GET'], '', \App\v1\Controllers\Documenttype::class . ':showItem');
            $documenttypeId->map(['POST'], '', \App\v1\Controllers\Documenttype::class . ':updateItem');
            $documenttypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Documenttype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/businesscriticities', function (RouteCollectorProxy $businesscriticities)
        {
          $businesscriticities->map(['GET'], '', \App\v1\Controllers\Businesscriticity::class . ':getAll');
          $businesscriticities->map(['POST'], '', \App\v1\Controllers\Businesscriticity::class . ':postItem');
          $businesscriticities->group("/{id:[0-9]+}", function (RouteCollectorProxy $businesscriticityId)
          {
            $businesscriticityId->map(['GET'], '', \App\v1\Controllers\Businesscriticity::class . ':showItem');
            $businesscriticityId->map(['POST'], '', \App\v1\Controllers\Businesscriticity::class . ':updateItem');
            $businesscriticityId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'businesscriticities',
                \App\v1\Controllers\Businesscriticity::class . ':showSubBusinesscriticities'
              );
              $sub->map(['GET'], 'history', \App\v1\Controllers\Businesscriticity::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/knowbaseitemcategories', function (RouteCollectorProxy $knowbaseitemcategories)
        {
          $knowbaseitemcategories->map(['GET'], '', \App\v1\Controllers\Knowbaseitemcategory::class . ':getAll');
          $knowbaseitemcategories->map(['POST'], '', \App\v1\Controllers\Knowbaseitemcategory::class . ':postItem');
          $knowbaseitemcategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $knowbaseitemcategoryId)
          {
            $knowbaseitemcategoryId->map(['GET'], '', \App\v1\Controllers\Knowbaseitemcategory::class . ':showItem');
            $knowbaseitemcategoryId->map(['POST'], '', \App\v1\Controllers\Knowbaseitemcategory::class . ':updateItem');
            $knowbaseitemcategoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'knowbaseitemcategories',
                \App\v1\Controllers\Knowbaseitemcategory::class . ':showSubKnowbaseitemcategories'
              );
              $sub->map(['GET'], 'history', \App\v1\Controllers\Knowbaseitemcategory::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/calendars', function (RouteCollectorProxy $calendars)
        {
          $calendars->map(['GET'], '', \App\v1\Controllers\Calendar::class . ':getAll');
          $calendars->map(['POST'], '', \App\v1\Controllers\Calendar::class . ':postItem');
          $calendars->group("/{id:[0-9]+}", function (RouteCollectorProxy $calendarId)
          {
            $calendarId->map(['GET'], '', \App\v1\Controllers\Calendar::class . ':showItem');
            $calendarId->map(['POST'], '', \App\v1\Controllers\Calendar::class . ':updateItem');
            $calendarId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'timeranges', \App\v1\Controllers\Calendar::class . ':showSubTimeranges');
              $sub->map(['GET'], 'holidays', \App\v1\Controllers\Calendar::class . ':showSubHolidays');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Calendar::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/holidays', function (RouteCollectorProxy $holidays)
        {
          $holidays->map(['GET'], '', \App\v1\Controllers\Holiday::class . ':getAll');
          $holidays->map(['POST'], '', \App\v1\Controllers\Holiday::class . ':postItem');
          $holidays->group("/{id:[0-9]+}", function (RouteCollectorProxy $holidayId)
          {
            $holidayId->map(['GET'], '', \App\v1\Controllers\Holiday::class . ':showItem');
            $holidayId->map(['POST'], '', \App\v1\Controllers\Holiday::class . ':updateItem');
            $holidayId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Holiday::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystems', function (RouteCollectorProxy $operatingsystems)
        {
          $operatingsystems->map(['GET'], '', \App\v1\Controllers\Operatingsystem::class . ':getAll');
          $operatingsystems->map(['POST'], '', \App\v1\Controllers\Operatingsystem::class . ':postItem');
          $operatingsystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $operatingsystemId)
          {
            $operatingsystemId->map(['GET'], '', \App\v1\Controllers\Operatingsystem::class . ':showItem');
            $operatingsystemId->map(['POST'], '', \App\v1\Controllers\Operatingsystem::class . ':updateItem');
            $operatingsystemId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystem::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystemversions', function (RouteCollectorProxy $operatingsystemversions)
        {
          $operatingsystemversions->map(['GET'], '', \App\v1\Controllers\Operatingsystemversion::class . ':getAll');
          $operatingsystemversions->map(['POST'], '', \App\v1\Controllers\Operatingsystemversion::class . ':postItem');
          $operatingsystemversions->group("/{id:[0-9]+}", function (RouteCollectorProxy $osvId)
          {
            $osvId->map(['GET'], '', \App\v1\Controllers\Operatingsystemversion::class . ':showItem');
            $osvId->map(['POST'], '', \App\v1\Controllers\Operatingsystemversion::class . ':updateItem');
            $osvId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemversion::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystemservicepacks', function (RouteCollectorProxy $ossp)
        {
          $ossp->map(['GET'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':getAll');
          $ossp->map(['POST'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':postItem');
          $ossp->group("/{id:[0-9]+}", function (RouteCollectorProxy $osspId)
          {
            $osspId->map(['GET'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':showItem');
            $osspId->map(['POST'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':updateItem');
            $osspId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemservicepack::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystemarchitectures', function (RouteCollectorProxy $osa)
        {
          $osa->map(['GET'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':getAll');
          $osa->map(['POST'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':postItem');
          $osa->group("/{id:[0-9]+}", function (RouteCollectorProxy $osaId)
          {
            $osaId->map(['GET'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':showItem');
            $osaId->map(['POST'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':updateItem');
            $osaId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemarchitecture::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystemeditions', function (RouteCollectorProxy $ose)
        {
          $ose->map(['GET'], '', \App\v1\Controllers\Operatingsystemedition::class . ':getAll');
          $ose->map(['POST'], '', \App\v1\Controllers\Operatingsystemedition::class . ':postItem');
          $ose->group("/{id:[0-9]+}", function (RouteCollectorProxy $oseId)
          {
            $oseId->map(['GET'], '', \App\v1\Controllers\Operatingsystemedition::class . ':showItem');
            $oseId->map(['POST'], '', \App\v1\Controllers\Operatingsystemedition::class . ':updateItem');
            $oseId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemedition::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystemkernels', function (RouteCollectorProxy $osk)
        {
          $osk->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':getAll');
          $osk->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':postItem');
          $osk->group("/{id:[0-9]+}", function (RouteCollectorProxy $oskId)
          {
            $oskId->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':showItem');
            $oskId->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':updateItem');
            $oskId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemkernel::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/operatingsystemkernelversions', function (RouteCollectorProxy $oskv)
        {
          $oskv->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':getAll');
          $oskv->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':postItem');
          $oskv->group("/{id:[0-9]+}", function (RouteCollectorProxy $oskvId)
          {
            $oskvId->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':showItem');
            $oskvId->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':updateItem');
            $oskvId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'history',
                \App\v1\Controllers\Operatingsystemkernelversion::class . ':showSubHistory'
              );
            });
          });
        });
        $dropdowns->group('/autoupdatesystems', function (RouteCollectorProxy $autoupdatesystems)
        {
          $autoupdatesystems->map(['GET'], '', \App\v1\Controllers\Autoupdatesystem::class . ':getAll');
          $autoupdatesystems->map(['POST'], '', \App\v1\Controllers\Autoupdatesystem::class . ':postItem');
          $autoupdatesystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $autoupdatesystemId)
          {
            $autoupdatesystemId->map(['GET'], '', \App\v1\Controllers\Autoupdatesystem::class . ':showItem');
            $autoupdatesystemId->map(['POST'], '', \App\v1\Controllers\Autoupdatesystem::class . ':updateItem');
            $autoupdatesystemId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Autoupdatesystem::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/networkinterfaces', function (RouteCollectorProxy $networkinterfaces)
        {
          $networkinterfaces->map(['GET'], '', \App\v1\Controllers\Networkinterface::class . ':getAll');
          $networkinterfaces->map(['POST'], '', \App\v1\Controllers\Networkinterface::class . ':postItem');
          $networkinterfaces->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkinterfaceId)
          {
            $networkinterfaceId->map(['GET'], '', \App\v1\Controllers\Networkinterface::class . ':showItem');
            $networkinterfaceId->map(['POST'], '', \App\v1\Controllers\Networkinterface::class . ':updateItem');
            $networkinterfaceId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Networkinterface::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/netpoints', function (RouteCollectorProxy $netpoints)
        {
          $netpoints->map(['GET'], '', \App\v1\Controllers\Netpoint::class . ':getAll');
          $netpoints->map(['POST'], '', \App\v1\Controllers\Netpoint::class . ':postItem');
          $netpoints->group("/{id:[0-9]+}", function (RouteCollectorProxy $netpointId)
          {
            $netpointId->map(['GET'], '', \App\v1\Controllers\Netpoint::class . ':showItem');
            $netpointId->map(['POST'], '', \App\v1\Controllers\Netpoint::class . ':updateItem');
            $netpointId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Netpoint::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/networks', function (RouteCollectorProxy $networks)
        {
          $networks->map(['GET'], '', \App\v1\Controllers\Network::class . ':getAll');
          $networks->map(['POST'], '', \App\v1\Controllers\Network::class . ':postItem');
          $networks->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkId)
          {
            $networkId->map(['GET'], '', \App\v1\Controllers\Network::class . ':showItem');
            $networkId->map(['POST'], '', \App\v1\Controllers\Network::class . ':updateItem');
            $networkId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Network::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/vlans', function (RouteCollectorProxy $vlans)
        {
          $vlans->map(['GET'], '', \App\v1\Controllers\Vlan::class . ':getAll');
          $vlans->map(['POST'], '', \App\v1\Controllers\Vlan::class . ':postItem');
          $vlans->group("/{id:[0-9]+}", function (RouteCollectorProxy $vlanId)
          {
            $vlanId->map(['GET'], '', \App\v1\Controllers\Vlan::class . ':showItem');
            $vlanId->map(['POST'], '', \App\v1\Controllers\Vlan::class . ':updateItem');
            $vlanId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Vlan::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/lineoperators', function (RouteCollectorProxy $lineoperators)
        {
          $lineoperators->map(['GET'], '', \App\v1\Controllers\Lineoperator::class . ':getAll');
          $lineoperators->map(['POST'], '', \App\v1\Controllers\Lineoperator::class . ':postItem');
          $lineoperators->group("/{id:[0-9]+}", function (RouteCollectorProxy $lineoperatorId)
          {
            $lineoperatorId->map(['GET'], '', \App\v1\Controllers\Lineoperator::class . ':showItem');
            $lineoperatorId->map(['POST'], '', \App\v1\Controllers\Lineoperator::class . ':updateItem');
            $lineoperatorId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Lineoperator::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/domaintypes', function (RouteCollectorProxy $domaintypes)
        {
          $domaintypes->map(['GET'], '', \App\v1\Controllers\Domaintype::class . ':getAll');
          $domaintypes->map(['POST'], '', \App\v1\Controllers\Domaintype::class . ':postItem');
          $domaintypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $domaintypeId)
          {
            $domaintypeId->map(['GET'], '', \App\v1\Controllers\Domaintype::class . ':showItem');
            $domaintypeId->map(['POST'], '', \App\v1\Controllers\Domaintype::class . ':updateItem');
            $domaintypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Domaintype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/domainrelations', function (RouteCollectorProxy $domainrelations)
        {
          $domainrelations->map(['GET'], '', \App\v1\Controllers\Domainrelation::class . ':getAll');
          $domainrelations->map(['POST'], '', \App\v1\Controllers\Domainrelation::class . ':postItem');
          $domainrelations->group("/{id:[0-9]+}", function (RouteCollectorProxy $domainrelationId)
          {
            $domainrelationId->map(['GET'], '', \App\v1\Controllers\Domainrelation::class . ':showItem');
            $domainrelationId->map(['POST'], '', \App\v1\Controllers\Domainrelation::class . ':updateItem');
            $domainrelationId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'domains', \App\v1\Controllers\Domainrelation::class . ':showSubDomains');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Domainrelation::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/domainrecordtypes', function (RouteCollectorProxy $domainrecordtypes)
        {
          $domainrecordtypes->map(['GET'], '', \App\v1\Controllers\Domainrecordtype::class . ':getAll');
          $domainrecordtypes->map(['POST'], '', \App\v1\Controllers\Domainrecordtype::class . ':postItem');
          $domainrecordtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $domainrecordtypeId)
          {
            $domainrecordtypeId->map(['GET'], '', \App\v1\Controllers\Domainrecordtype::class . ':showItem');
            $domainrecordtypeId->map(['POST'], '', \App\v1\Controllers\Domainrecordtype::class . ':updateItem');
            $domainrecordtypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Domainrecordtype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/ipnetworks', function (RouteCollectorProxy $ipnetworks)
        {
          $ipnetworks->map(['GET'], '', \App\v1\Controllers\Ipnetwork::class . ':getAll');
          $ipnetworks->map(['POST'], '', \App\v1\Controllers\Ipnetwork::class . ':postItem');
          $ipnetworks->group("/{id:[0-9]+}", function (RouteCollectorProxy $ipnetworkId)
          {
            $ipnetworkId->map(['GET'], '', \App\v1\Controllers\Ipnetwork::class . ':showItem');
            $ipnetworkId->map(['POST'], '', \App\v1\Controllers\Ipnetwork::class . ':updateItem');
            $ipnetworkId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'vlans', \App\v1\Controllers\Ipnetwork::class . ':showSubVlans');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Ipnetwork::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/fqdns', function (RouteCollectorProxy $fqdns)
        {
          $fqdns->map(['GET'], '', \App\v1\Controllers\Fqdn::class . ':getAll');
          $fqdns->map(['POST'], '', \App\v1\Controllers\Fqdn::class . ':postItem');
          $fqdns->group("/{id:[0-9]+}", function (RouteCollectorProxy $fqdnId)
          {
            $fqdnId->map(['GET'], '', \App\v1\Controllers\Fqdn::class . ':showItem');
            $fqdnId->map(['POST'], '', \App\v1\Controllers\Fqdn::class . ':updateItem');
            $fqdnId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'networkalias', \App\v1\Controllers\Fqdn::class . ':showSubNetworkalias');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Fqdn::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/wifinetworks', function (RouteCollectorProxy $wifinetworks)
        {
          $wifinetworks->map(['GET'], '', \App\v1\Controllers\Wifinetwork::class . ':getAll');
          $wifinetworks->map(['POST'], '', \App\v1\Controllers\Wifinetwork::class . ':postItem');
          $wifinetworks->group("/{id:[0-9]+}", function (RouteCollectorProxy $wifinetworkId)
          {
            $wifinetworkId->map(['GET'], '', \App\v1\Controllers\Wifinetwork::class . ':showItem');
            $wifinetworkId->map(['POST'], '', \App\v1\Controllers\Wifinetwork::class . ':updateItem');
            $wifinetworkId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Wifinetwork::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/networknames', function (RouteCollectorProxy $networknames)
        {
          $networknames->map(['GET'], '', \App\v1\Controllers\Networkname::class . ':getAll');
          $networknames->map(['POST'], '', \App\v1\Controllers\Networkname::class . ':postItem');
          $networknames->group("/{id:[0-9]+}", function (RouteCollectorProxy $networknameId)
          {
            $networknameId->map(['GET'], '', \App\v1\Controllers\Networkname::class . ':showItem');
            $networknameId->map(['POST'], '', \App\v1\Controllers\Networkname::class . ':updateItem');
            $networknameId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'networkalias', \App\v1\Controllers\Networkname::class . ':showSubNetworkalias');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Networkname::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/softwarecategories', function (RouteCollectorProxy $softwarecategories)
        {
          $softwarecategories->map(['GET'], '', \App\v1\Controllers\Softwarecategory::class . ':getAll');
          $softwarecategories->map(['POST'], '', \App\v1\Controllers\Softwarecategory::class . ':postItem');
          $softwarecategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwarecategoryId)
          {
            $softwarecategoryId->map(['GET'], '', \App\v1\Controllers\Softwarecategory::class . ':showItem');
            $softwarecategoryId->map(['POST'], '', \App\v1\Controllers\Softwarecategory::class . ':updateItem');
            $softwarecategoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(
                ['GET'],
                'softwarecategories',
                \App\v1\Controllers\Softwarecategory::class . ':showSubSoftwarecategories'
              );
              $sub->map(['GET'], 'history', \App\v1\Controllers\Softwarecategory::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/usertitles', function (RouteCollectorProxy $usertitles)
        {
          $usertitles->map(['GET'], '', \App\v1\Controllers\Usertitle::class . ':getAll');
          $usertitles->map(['POST'], '', \App\v1\Controllers\Usertitle::class . ':postItem');
          $usertitles->group("/{id:[0-9]+}", function (RouteCollectorProxy $usertitleId)
          {
            $usertitleId->map(['GET'], '', \App\v1\Controllers\Usertitle::class . ':showItem');
            $usertitleId->map(['POST'], '', \App\v1\Controllers\Usertitle::class . ':updateItem');
            $usertitleId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Usertitle::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/usercategories', function (RouteCollectorProxy $usercategories)
        {
          $usercategories->map(['GET'], '', \App\v1\Controllers\Usercategory::class . ':getAll');
          $usercategories->map(['POST'], '', \App\v1\Controllers\Usercategory::class . ':postItem');
          $usercategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $usercategoryId)
          {
            $usercategoryId->map(['GET'], '', \App\v1\Controllers\Usercategory::class . ':showItem');
            $usercategoryId->map(['POST'], '', \App\v1\Controllers\Usercategory::class . ':updateItem');
            $usercategoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Usercategory::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/rulerightparameters', function (RouteCollectorProxy $rulerightparameters)
        {
          $rulerightparameters->map(['GET'], '', \App\v1\Controllers\Rulerightparameter::class . ':getAll');
          $rulerightparameters->map(['POST'], '', \App\v1\Controllers\Rulerightparameter::class . ':postItem');
          $rulerightparameters->group("/{id:[0-9]+}", function (RouteCollectorProxy $rulerightparameterId)
          {
            $rulerightparameterId->map(['GET'], '', \App\v1\Controllers\Rulerightparameter::class . ':showItem');
            $rulerightparameterId->map(['POST'], '', \App\v1\Controllers\Rulerightparameter::class . ':updateItem');
            $rulerightparameterId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Rulerightparameter::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/fieldblacklists', function (RouteCollectorProxy $fieldblacklists)
        {
          $fieldblacklists->map(['GET'], '', \App\v1\Controllers\Fieldblacklist::class . ':getAll');
          $fieldblacklists->map(['POST'], '', \App\v1\Controllers\Fieldblacklist::class . ':postItem');
          $fieldblacklists->group("/{id:[0-9]+}", function (RouteCollectorProxy $fieldblacklistId)
          {
            $fieldblacklistId->map(['GET'], '', \App\v1\Controllers\Fieldblacklist::class . ':showItem');
            $fieldblacklistId->map(['POST'], '', \App\v1\Controllers\Fieldblacklist::class . ':updateItem');
            $fieldblacklistId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Fieldblacklist::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/ssovariables', function (RouteCollectorProxy $ssovariables)
        {
          $ssovariables->map(['GET'], '', \App\v1\Controllers\Ssovariable::class . ':getAll');
          $ssovariables->map(['POST'], '', \App\v1\Controllers\Ssovariable::class . ':postItem');
          $ssovariables->group("/{id:[0-9]+}", function (RouteCollectorProxy $ssovariableId)
          {
            $ssovariableId->map(['GET'], '', \App\v1\Controllers\Ssovariable::class . ':showItem');
            $ssovariableId->map(['POST'], '', \App\v1\Controllers\Ssovariable::class . ':updateItem');
            $ssovariableId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Ssovariable::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/plugs', function (RouteCollectorProxy $plugs)
        {
          $plugs->map(['GET'], '', \App\v1\Controllers\Plug::class . ':getAll');
          $plugs->map(['POST'], '', \App\v1\Controllers\Plug::class . ':postItem');
          $plugs->group("/{id:[0-9]+}", function (RouteCollectorProxy $plugId)
          {
            $plugId->map(['GET'], '', \App\v1\Controllers\Plug::class . ':showItem');
            $plugId->map(['POST'], '', \App\v1\Controllers\Plug::class . ':updateItem');
            $plugId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Plug::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/appliancetypes', function (RouteCollectorProxy $viewliancetypes)
        {
          $viewliancetypes->map(['GET'], '', \App\v1\Controllers\Appliancetype::class . ':getAll');
          $viewliancetypes->map(['POST'], '', \App\v1\Controllers\Appliancetype::class . ':postItem');
          $viewliancetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $viewliancetypeId)
          {
            $viewliancetypeId->map(['GET'], '', \App\v1\Controllers\Appliancetype::class . ':showItem');
            $viewliancetypeId->map(['POST'], '', \App\v1\Controllers\Appliancetype::class . ':updateItem');
            $viewliancetypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Appliancetype::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/applianceenvironments', function (RouteCollectorProxy $viewlianceenvironments)
        {
          $viewlianceenvironments->map(['GET'], '', \App\v1\Controllers\Applianceenvironment::class . ':getAll');
          $viewlianceenvironments->map(['POST'], '', \App\v1\Controllers\Applianceenvironment::class . ':postItem');
          $viewlianceenvironments->group("/{id:[0-9]+}", function (RouteCollectorProxy $viewlianceenvironmentId)
          {
            $viewlianceenvironmentId->map(['GET'], '', \App\v1\Controllers\Applianceenvironment::class . ':showItem');
            $viewlianceenvironmentId->map(
              ['POST'],
              '',
              \App\v1\Controllers\Applianceenvironment::class . ':updateItem'
            );
            $viewlianceenvironmentId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\Applianceenvironment::class . ':showSubHistory');
            });
          });
        });
        $dropdowns->group('/oauthimapapplications', function (RouteCollectorProxy $oauthimapapplications)
        {
          $oauthimapapplications->map(['GET'], '', \App\v1\Controllers\OauthimapApplication::class . ':getAll');
          $oauthimapapplications->map(['POST'], '', \App\v1\Controllers\OauthimapApplication::class . ':postItem');
          $oauthimapapplications->group("/{id:[0-9]+}", function (RouteCollectorProxy $oauthimapapplicationId)
          {
            $oauthimapapplicationId->map(['GET'], '', \App\v1\Controllers\OauthimapApplication::class . ':showItem');
            $oauthimapapplicationId->map(['POST'], '', \App\v1\Controllers\OauthimapApplication::class . ':updateItem');
            $oauthimapapplicationId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'history', \App\v1\Controllers\OauthimapApplication::class . ':showSubHistory');
            });
          });
        });
      });

      $view->group('/devices', function (RouteCollectorProxy $devices)
      {
        $devices->group('/devicepowersupplies', function (RouteCollectorProxy $devicepowersupplies)
        {
          $devicepowersupplies->map(['GET'], '', \App\v1\Controllers\Devicepowersupply::class . ':getAll');
          $devicepowersupplies->map(['POST'], '', \App\v1\Controllers\Devicepowersupply::class . ':postItem');
          $devicepowersupplies->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicepowersupplyId)
          {
            $devicepowersupplyId->map(['GET'], '', \App\v1\Controllers\Devicepowersupply::class . ':showItem');
            $devicepowersupplyId->map(['POST'], '', \App\v1\Controllers\Devicepowersupply::class . ':updateItem');
            $devicepowersupplyId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicepowersupply::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicepowersupply::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepowersupply::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicebatteries', function (RouteCollectorProxy $devicebatteries)
        {
          $devicebatteries->map(['GET'], '', \App\v1\Controllers\Devicebattery::class . ':getAll');
          $devicebatteries->map(['POST'], '', \App\v1\Controllers\Devicebattery::class . ':postItem');
          $devicebatteries->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicebatteryId)
          {
            $devicebatteryId->map(['GET'], '', \App\v1\Controllers\Devicebattery::class . ':showItem');
            $devicebatteryId->map(['POST'], '', \App\v1\Controllers\Devicebattery::class . ':updateItem');
            $devicebatteryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicebattery::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicebattery::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicebattery::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicebatterytypes', function (RouteCollectorProxy $devicebatterytypes)
        {
          $devicebatterytypes->map(['GET'], '', \App\v1\Controllers\Devicebatterytype::class . ':getAll');
          $devicebatterytypes->map(['POST'], '', \App\v1\Controllers\Devicebatterytype::class . ':postItem');
          $devicebatterytypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicebatterytypeId)
          {
            $devicebatterytypeId->map(['GET'], '', \App\v1\Controllers\Devicebatterytype::class . ':showItem');
            $devicebatterytypeId->map(['POST'], '', \App\v1\Controllers\Devicebatterytype::class . ':updateItem');
          });
        });
        $devices->group('/devicecases', function (RouteCollectorProxy $devicecases)
        {
          $devicecases->map(['GET'], '', \App\v1\Controllers\Devicecase::class . ':getAll');
          $devicecases->map(['POST'], '', \App\v1\Controllers\Devicecase::class . ':postItem');
          $devicecases->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecaseId)
          {
            $devicecaseId->map(['GET'], '', \App\v1\Controllers\Devicecase::class . ':showItem');
            $devicecaseId->map(['POST'], '', \App\v1\Controllers\Devicecase::class . ':updateItem');
            $devicecaseId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicecase::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicecase::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecase::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicesensors', function (RouteCollectorProxy $devicesensors)
        {
          $devicesensors->map(['GET'], '', \App\v1\Controllers\Devicesensor::class . ':getAll');
          $devicesensors->map(['POST'], '', \App\v1\Controllers\Devicesensor::class . ':postItem');
          $devicesensors->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesensorId)
          {
            $devicesensorId->map(['GET'], '', \App\v1\Controllers\Devicesensor::class . ':showItem');
            $devicesensorId->map(['POST'], '', \App\v1\Controllers\Devicesensor::class . ':updateItem');
            $devicesensorId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicesensor::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicesensor::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesensor::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicesimcards', function (RouteCollectorProxy $devicesimcards)
        {
          $devicesimcards->map(['GET'], '', \App\v1\Controllers\Devicesimcard::class . ':getAll');
          $devicesimcards->map(['POST'], '', \App\v1\Controllers\Devicesimcard::class . ':postItem');
          $devicesimcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesimcardId)
          {
            $devicesimcardId->map(['GET'], '', \App\v1\Controllers\Devicesimcard::class . ':showItem');
            $devicesimcardId->map(['POST'], '', \App\v1\Controllers\Devicesimcard::class . ':updateItem');
            $devicesimcardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicesimcard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicesimcard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesimcard::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicegraphiccards', function (RouteCollectorProxy $devicegraphiccards)
        {
          $devicegraphiccards->map(['GET'], '', \App\v1\Controllers\Devicegraphiccard::class . ':getAll');
          $devicegraphiccards->map(['POST'], '', \App\v1\Controllers\Devicegraphiccard::class . ':postItem');
          $devicegraphiccards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegraphiccardId)
          {
            $devicegraphiccardId->map(['GET'], '', \App\v1\Controllers\Devicegraphiccard::class . ':showItem');
            $devicegraphiccardId->map(['POST'], '', \App\v1\Controllers\Devicegraphiccard::class . ':updateItem');
            $devicegraphiccardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicegraphiccard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicegraphiccard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegraphiccard::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicemotherboards', function (RouteCollectorProxy $devicemotherboards)
        {
          $devicemotherboards->map(['GET'], '', \App\v1\Controllers\Devicemotherboard::class . ':getAll');
          $devicemotherboards->map(['POST'], '', \App\v1\Controllers\Devicemotherboard::class . ':postItem');
          $devicemotherboards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicemotherboardId)
          {
            $devicemotherboardId->map(['GET'], '', \App\v1\Controllers\Devicemotherboard::class . ':showItem');
            $devicemotherboardId->map(['POST'], '', \App\v1\Controllers\Devicemotherboard::class . ':updateItem');
            $devicemotherboardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicemotherboard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicemotherboard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicemotherboard::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicenetworkcards', function (RouteCollectorProxy $devicenetworkcards)
        {
          $devicenetworkcards->map(['GET'], '', \App\v1\Controllers\Devicenetworkcard::class . ':getAll');
          $devicenetworkcards->map(['POST'], '', \App\v1\Controllers\Devicenetworkcard::class . ':postItem');
          $devicenetworkcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicenetworkcardId)
          {
            $devicenetworkcardId->map(['GET'], '', \App\v1\Controllers\Devicenetworkcard::class . ':showItem');
            $devicenetworkcardId->map(['POST'], '', \App\v1\Controllers\Devicenetworkcard::class . ':updateItem');
            $devicenetworkcardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicenetworkcard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicenetworkcard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicenetworkcard::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicesoundcards', function (RouteCollectorProxy $devicesoundcards)
        {
          $devicesoundcards->map(['GET'], '', \App\v1\Controllers\Devicesoundcard::class . ':getAll');
          $devicesoundcards->map(['POST'], '', \App\v1\Controllers\Devicesoundcard::class . ':postItem');
          $devicesoundcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesoundcardId)
          {
            $devicesoundcardId->map(['GET'], '', \App\v1\Controllers\Devicesoundcard::class . ':showItem');
            $devicesoundcardId->map(['POST'], '', \App\v1\Controllers\Devicesoundcard::class . ':updateItem');
            $devicesoundcardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicesoundcard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicesoundcard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesoundcard::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicegenerics', function (RouteCollectorProxy $devicegenerics)
        {
          $devicegenerics->map(['GET'], '', \App\v1\Controllers\Devicegeneric::class . ':getAll');
          $devicegenerics->map(['POST'], '', \App\v1\Controllers\Devicegeneric::class . ':postItem');
          $devicegenerics->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegenericId)
          {
            $devicegenericId->map(['GET'], '', \App\v1\Controllers\Devicegeneric::class . ':showItem');
            $devicegenericId->map(['POST'], '', \App\v1\Controllers\Devicegeneric::class . ':updateItem');
            $devicegenericId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicegeneric::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicegeneric::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegeneric::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicecontrols', function (RouteCollectorProxy $devicecontrols)
        {
          $devicecontrols->map(['GET'], '', \App\v1\Controllers\Devicecontrol::class . ':getAll');
          $devicecontrols->map(['POST'], '', \App\v1\Controllers\Devicecontrol::class . ':postItem');
          $devicecontrols->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecontrolId)
          {
            $devicecontrolId->map(['GET'], '', \App\v1\Controllers\Devicecontrol::class . ':showItem');
            $devicecontrolId->map(['POST'], '', \App\v1\Controllers\Devicecontrol::class . ':updateItem');
            $devicecontrolId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicecontrol::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicecontrol::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecontrol::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/deviceharddrives', function (RouteCollectorProxy $deviceharddrives)
        {
          $deviceharddrives->map(['GET'], '', \App\v1\Controllers\Deviceharddrive::class . ':getAll');
          $deviceharddrives->map(['POST'], '', \App\v1\Controllers\Deviceharddrive::class . ':postItem');
          $deviceharddrives->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceharddriveId)
          {
            $deviceharddriveId->map(['GET'], '', \App\v1\Controllers\Deviceharddrive::class . ':showItem');
            $deviceharddriveId->map(['POST'], '', \App\v1\Controllers\Deviceharddrive::class . ':updateItem');
            $deviceharddriveId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Deviceharddrive::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Deviceharddrive::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceharddrive::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicefirmwares', function (RouteCollectorProxy $devicefirmwares)
        {
          $devicefirmwares->map(['GET'], '', \App\v1\Controllers\Devicefirmware::class . ':getAll');
          $devicefirmwares->map(['POST'], '', \App\v1\Controllers\Devicefirmware::class . ':postItem');
          $devicefirmwares->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicefirmwareId)
          {
            $devicefirmwareId->map(['GET'], '', \App\v1\Controllers\Devicefirmware::class . ':showItem');
            $devicefirmwareId->map(['POST'], '', \App\v1\Controllers\Devicefirmware::class . ':updateItem');
            $devicefirmwareId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicefirmware::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicefirmware::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicefirmware::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicefirmwaretypes', function (RouteCollectorProxy $devicefirmwaretypes)
        {
          $devicefirmwaretypes->map(['GET'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':getAll');
          $devicefirmwaretypes->map(['POST'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':postItem');
          $devicefirmwaretypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicefirmwaretypeId)
          {
            $devicefirmwaretypeId->map(['GET'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':showItem');
            $devicefirmwaretypeId->map(['POST'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':updateItem');
          });
        });
        $devices->group('/devicedrives', function (RouteCollectorProxy $devicedrives)
        {
          $devicedrives->map(['GET'], '', \App\v1\Controllers\Devicedrive::class . ':getAll');
          $devicedrives->map(['POST'], '', \App\v1\Controllers\Devicedrive::class . ':postItem');
          $devicedrives->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicedriveId)
          {
            $devicedriveId->map(['GET'], '', \App\v1\Controllers\Devicedrive::class . ':showItem');
            $devicedriveId->map(['POST'], '', \App\v1\Controllers\Devicedrive::class . ':updateItem');
            $devicedriveId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicedrive::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicedrive::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicedrive::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicememories', function (RouteCollectorProxy $devicememories)
        {
          $devicememories->map(['GET'], '', \App\v1\Controllers\Devicememory::class . ':getAll');
          $devicememories->map(['POST'], '', \App\v1\Controllers\Devicememory::class . ':postItem');
          $devicememories->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicememoryId)
          {
            $devicememoryId->map(['GET'], '', \App\v1\Controllers\Devicememory::class . ':showItem');
            $devicememoryId->map(['POST'], '', \App\v1\Controllers\Devicememory::class . ':updateItem');
            $devicememoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicememory::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicememory::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicememory::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/deviceprocessors', function (RouteCollectorProxy $deviceprocessors)
        {
          $deviceprocessors->map(['GET'], '', \App\v1\Controllers\Deviceprocessor::class . ':getAll');
          $deviceprocessors->map(['POST'], '', \App\v1\Controllers\Deviceprocessor::class . ':postItem');
          $deviceprocessors->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceprocessorId)
          {
            $deviceprocessorId->map(['GET'], '', \App\v1\Controllers\Deviceprocessor::class . ':showItem');
            $deviceprocessorId->map(['POST'], '', \App\v1\Controllers\Deviceprocessor::class . ':updateItem');
            $deviceprocessorId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Deviceprocessor::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Deviceprocessor::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceprocessor::class . ':showSubHistory');
            });
          });
        });
        $devices->group('/devicepcis', function (RouteCollectorProxy $devicepcis)
        {
          $devicepcis->map(['GET'], '', \App\v1\Controllers\Devicepci::class . ':getAll');
          $devicepcis->map(['POST'], '', \App\v1\Controllers\Devicepci::class . ':postItem');
          $devicepcis->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicepciId)
          {
            $devicepciId->map(['GET'], '', \App\v1\Controllers\Devicepci::class . ':showItem');
            $devicepciId->map(['POST'], '', \App\v1\Controllers\Devicepci::class . ':updateItem');
            $devicepciId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicepci::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicepci::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepci::class . ':showSubHistory');
            });
          });
        });
      });

      $view->group('/notificationtemplates', function (RouteCollectorProxy $notificationtemplates)
      {
        $notificationtemplates->map(['GET'], '', \App\v1\Controllers\Notificationtemplate::class . ':getAll');
        $notificationtemplates->map(['POST'], '', \App\v1\Controllers\Notificationtemplate::class . ':postItem');
        $notificationtemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $notificationtemplateId)
        {
          $notificationtemplateId->map(['GET'], '', \App\v1\Controllers\Notificationtemplate::class . ':showItem');
          $notificationtemplateId->map(['POST'], '', \App\v1\Controllers\Notificationtemplate::class . ':updateItem');
          $notificationtemplateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Notificationtemplate::class . ':showSubHistory');
            $sub->map(
              ['GET'],
              'templatetranslation',
              \App\v1\Controllers\Notificationtemplate::class . ':showSubTemplatetranslations'
            );
            $sub->map(
              ['GET'],
              'templatetranslation/{translationid:[0-9]+}',
              \App\v1\Controllers\Notificationtemplate::class . ':showSubTemplatetranslation'
            );
          });
        });
      });
      $view->group('/notifications', function (RouteCollectorProxy $notifications)
      {
        $notifications->map(['GET'], '', \App\v1\Controllers\Notification::class . ':getAll');
        $notifications->map(['POST'], '', \App\v1\Controllers\Notification::class . ':postItem');
        $notifications->group("/{id:[0-9]+}", function (RouteCollectorProxy $notificationId)
        {
          $notificationId->map(['GET'], '', \App\v1\Controllers\Notification::class . ':showItem');
          $notificationId->map(['POST'], '', \App\v1\Controllers\Notification::class . ':updateItem');
          $notificationId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Notification::class . ':showSubHistory');
          });
        });
      });

      $view->group('/slms', function (RouteCollectorProxy $slms)
      {
        $slms->map(['GET'], '', \App\v1\Controllers\Slm::class . ':getAll');
        $slms->map(['POST'], '', \App\v1\Controllers\Slm::class . ':postItem');
        $slms->group("/{id:[0-9]+}", function (RouteCollectorProxy $slmId)
        {
          $slmId->map(['GET'], '', \App\v1\Controllers\Slm::class . ':showItem');
          $slmId->map(['POST'], '', \App\v1\Controllers\Slm::class . ':updateItem');
          $slmId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'slas', \App\v1\Controllers\Slm::class . ':showSubSlas');
            $sub->map(['GET'], 'olas', \App\v1\Controllers\Slm::class . ':showSubOlas');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Slm::class . ':showSubHistory');
          });
        });
      });

      $view->group('/fieldunicities', function (RouteCollectorProxy $fieldunicities)
      {
        $fieldunicities->map(['GET'], '', \App\v1\Controllers\Fieldunicity::class . ':getAll');
        $fieldunicities->map(['POST'], '', \App\v1\Controllers\Fieldunicity::class . ':postItem');
        $fieldunicities->group("/{id:[0-9]+}", function (RouteCollectorProxy $fieldunicityId)
        {
          $fieldunicityId->map(['GET'], '', \App\v1\Controllers\Fieldunicity::class . ':showItem');
          $fieldunicityId->map(['POST'], '', \App\v1\Controllers\Fieldunicity::class . ':updateItem');
          $fieldunicityId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Fieldunicity::class . ':showSubHistory');
          });
        });
      });

      $view->group('/crontasks', function (RouteCollectorProxy $crontasks)
      {
        $crontasks->map(['GET'], '', \App\v1\Controllers\Crontask::class . ':getAll');
        $crontasks->map(['POST'], '', \App\v1\Controllers\Crontask::class . ':postItem');
        $crontasks->group("/{id:[0-9]+}", function (RouteCollectorProxy $crontaskId)
        {
          $crontaskId->map(['GET'], '', \App\v1\Controllers\Crontask::class . ':showItem');
          $crontaskId->map(['POST'], '', \App\v1\Controllers\Crontask::class . ':updateItem');

          $crontaskId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'executions', \App\v1\Controllers\Crontask::class . ':showSubExecutions');
            $sub->map(['GET'], 'executions/{executionid:[0-9]+}', \App\v1\Controllers\Crontask::class .
                      ':showSubExecutionlogs');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Crontask::class . ':showSubHistory');
          });
        });
      });

      $view->group('/links', function (RouteCollectorProxy $links)
      {
        $links->map(['GET'], '', \App\v1\Controllers\Link::class . ':getAll');
        $links->map(['POST'], '', \App\v1\Controllers\Link::class . ':postItem');
        $links->group("/{id:[0-9]+}", function (RouteCollectorProxy $linkId)
        {
          $linkId->map(['GET'], '', \App\v1\Controllers\Link::class . ':showItem');
          $linkId->map(['POST'], '', \App\v1\Controllers\Link::class . ':updateItem');
          $linkId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'associateditemtypes', \App\v1\Controllers\Link::class . ':showSubAssociatedItemType');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Link::class . ':showSubHistory');
          });
        });
      });

      $view->group('/mailcollectors', function (RouteCollectorProxy $mailcollectors)
      {
        $mailcollectors->map(['GET'], '', \App\v1\Controllers\Mailcollector::class . ':getAll');
        $mailcollectors->map(['POST'], '', \App\v1\Controllers\Mailcollector::class . ':postItem');

        $mailcollectors->group("/new", function (RouteCollectorProxy $collectorNew)
        {
          $collectorNew->map(['GET'], '', \App\v1\Controllers\Mailcollector::class . ':showNewItem');
          $collectorNew->map(['POST'], '', \App\v1\Controllers\Mailcollector::class . ':newItem');
        });

        $mailcollectors->group("/{id:[0-9]+}", function (RouteCollectorProxy $mailcollectorId)
        {
          $mailcollectorId->map(['GET'], '', \App\v1\Controllers\Mailcollector::class . ':showItem');
          $mailcollectorId->map(['POST'], '', \App\v1\Controllers\Mailcollector::class . ':updateItem');
          $mailcollectorId->map(['GET'], '/oauth', \App\v1\Controllers\Mailcollector::class . ':doOauth');
          $mailcollectorId->map(['GET'], '/oauth/cb', \App\v1\Controllers\Mailcollector::class . ':callbackOauth');

          $mailcollectorId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Mailcollector::class . ':showSubHistory');
          });
        });
      });

      $view->group('/authssos', function (RouteCollectorProxy $authssos)
      {
        $authssos->map(['GET'], '', \App\v1\Controllers\Authsso::class . ':getAll');
        $authssos->map(['POST'], '', \App\v1\Controllers\Authsso::class . ':postItem');

        $authssos->group("/new", function (RouteCollectorProxy $ssonew)
        {
          $ssonew->map(['GET'], '', \App\v1\Controllers\Authsso::class . ':showNewItem');
          $ssonew->map(['POST'], '', \App\v1\Controllers\Authsso::class . ':newItem');
        });

        $authssos->group("/{id:[0-9]+}", function (RouteCollectorProxy $ssoId)
        {
          $ssoId->map(['GET'], '', \App\v1\Controllers\Authsso::class . ':showItem');
          $ssoId->map(['POST'], '', \App\v1\Controllers\Authsso::class . ':updateItem');

          $ssoId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Authsso::class . ':showSubHistory');
          });
        });
      });

      $view->group('/authldaps', function (RouteCollectorProxy $ldaps)
      {
        $ldaps->map(['GET'], '', \App\v1\Controllers\Authldap::class . ':getAll');
        $ldaps->map(['POST'], '', \App\v1\Controllers\Authldap::class . ':postItem');

        $ldaps->group("/new", function (RouteCollectorProxy $ldapNew)
        {
          $ldapNew->map(['GET'], '', \App\v1\Controllers\Authldap::class . ':showNewItem');
          $ldapNew->map(['POST'], '', \App\v1\Controllers\Authldap::class . ':newItem');
        });

        $ldaps->group("/{id:[0-9]+}", function (RouteCollectorProxy $ldapId)
        {
          $ldapId->map(['GET'], '', \App\v1\Controllers\Authldap::class . ':showItem');
          $ldapId->map(['POST'], '', \App\v1\Controllers\Authldap::class . ':updateItem');
        });
      });

      $view->group('/forms', function (RouteCollectorProxy $forms)
      {
        $forms->map(['GET'], '', \App\v1\Controllers\Forms\Form::class . ':getAll');
        // $forms->map(['POST'], '', \App\v1\Controllers\Forms\Form::class . ':postItem');

        // $forms->group("/new", function (RouteCollectorProxy $formNew)
        // {
        //   $formNew->map(['GET'], '', \App\v1\Controllers\Forms\Form::class . ':showNewItem');
        //   $formNew->map(['POST'], '', \App\v1\Controllers\Forms\Form::class . ':newItem');
        // });

        $forms->group("/{id:[0-9]+}", function (RouteCollectorProxy $formId)
        {
          $formId->map(['GET'], '', \App\v1\Controllers\Forms\Form::class . ':showItem');
          // $formId->map(['POST'], '', \App\v1\Controllers\Forms\Form::class . ':updateItem');
          $formId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'sections', \App\v1\Controllers\Forms\Form::class . ':showSubSections');
            $sub->map(['GET'], 'questions', \App\v1\Controllers\Forms\Form::class . ':showSubQuestions');
            $sub->map(['GET'], 'answers', \App\v1\Controllers\Forms\Form::class . ':showSubAnswers');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Form::class . ':showSubHistory');
          });
        });
      });
      $view->group('/sections', function (RouteCollectorProxy $sections)
      {
        $sections->map(['GET'], '', \App\v1\Controllers\Forms\Section::class . ':getAll');
        // $sections->map(['POST'], '', \App\v1\Controllers\Forms\Section::class . ':postItem');

        // $sections->group("/new", function (RouteCollectorProxy $sectionNew)
        // {
        //   $sectionNew->map(['GET'], '', \App\v1\Controllers\Forms\Section::class . ':showNewItem');
        //   $sectionNew->map(['POST'], '', \App\v1\Controllers\Forms\Section::class . ':newItem');
        // });

        $sections->group("/{id:[0-9]+}", function (RouteCollectorProxy $sectionId)
        {
          $sectionId->map(['GET'], '', \App\v1\Controllers\Forms\Section::class . ':showItem');
          // $sectionId->map(['POST'], '', \App\v1\Controllers\Forms\Section::class . ':updateItem');
          $sectionId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'forms', \App\v1\Controllers\Forms\Section::class . ':showSubForms');
            $sub->map(['GET'], 'questions', \App\v1\Controllers\Forms\Section::class . ':showSubQuestions');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Section::class . ':showSubHistory');
          });
        });
      });
      $view->group('/questions', function (RouteCollectorProxy $questions)
      {
        $questions->map(['GET'], '', \App\v1\Controllers\Forms\Question::class . ':getAll');
        // $questions->map(['POST'], '', \App\v1\Controllers\Forms\Question::class . ':postItem');

        // $questions->group("/new", function (RouteCollectorProxy $questionNew)
        // {
        //   $questionNew->map(['GET'], '', \App\v1\Controllers\Forms\Question::class . ':showNewItem');
        //   $questionNew->map(['POST'], '', \App\v1\Controllers\Forms\Question::class . ':newItem');
        // });

        $questions->group("/{id:[0-9]+}", function (RouteCollectorProxy $questionId)
        {
          $questionId->map(['GET'], '', \App\v1\Controllers\Forms\Question::class . ':showItem');
          // $questionId->map(['POST'], '', \App\v1\Controllers\Forms\Question::class . ':updateItem');
          $questionId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'sections', \App\v1\Controllers\Forms\Question::class . ':showSubSections');
            $sub->map(['GET'], 'forms', \App\v1\Controllers\Forms\Question::class . ':showSubForms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Question::class . ':showSubHistory');
          });
        });
      });
      $view->group('/answers', function (RouteCollectorProxy $answers)
      {
        $answers->map(['GET'], '', \App\v1\Controllers\Forms\Answer::class . ':getAll');
        // $answers->map(['POST'], '', \App\v1\Controllers\Forms\Answer::class . ':postItem');

        // $answers->group("/new", function (RouteCollectorProxy $answerNew)
        // {
        //   $answerNew->map(['GET'], '', \App\v1\Controllers\Forms\Answer::class . ':showNewItem');
        //   $answerNew->map(['POST'], '', \App\v1\Controllers\Forms\Answer::class . ':newItem');
        // });

        $answers->group("/{id:[0-9]+}", function (RouteCollectorProxy $answerId)
        {
          $answerId->map(['GET'], '', \App\v1\Controllers\Forms\Answer::class . ':showAnswer');
          // $answerId->map(['POST'], '', \App\v1\Controllers\Forms\Answer::class . ':updateItem');
          $answerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Answer::class . ':showSubHistory');
          });
        });
      });
    });
  }
}
