<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Computer extends Common
{
  protected $model = '\App\Models\Computer';
  protected $rootUrl2 = '/computers/';
  protected $choose = 'computers';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Computer();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = \App\Models\Computer::find($args['id']);
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Computer();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubSoftwares(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('softwareversions', 'antiviruses')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/softwares');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAntiviruses = [];
    foreach ($myItem->antiviruses as $antivirus)
    {
      $antivirus_url = $this->genereRootUrl2Link($rootUrl2, '/computerantivirus/', $antivirus->id);

      $manufacturer = '';
      $manufacturer_url = '';
      if ($antivirus->manufacturer !== null)
      {
        $manufacturer = $antivirus->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $antivirus->manufacturer->id
        );
      }

      $is_dynamic = $antivirus->is_dynamic;
      if ($is_dynamic == 1)
      {
        $is_dynamic_val = $translator->translate('Yes');
      }
      else
      {
        $is_dynamic_val = $translator->translate('No');
      }

      $is_active = $antivirus->is_active;
      if ($is_active == 1)
      {
        $is_active_val = $translator->translate('Yes');
      }
      else
      {
        $is_active_val = $translator->translate('No');
      }

      $is_uptodate = $antivirus->is_uptodate;
      if ($is_uptodate == 1)
      {
        $is_uptodate_val = $translator->translate('Yes');
      }
      else
      {
        $is_uptodate_val = $translator->translate('No');
      }

      $myAntiviruses[] = [
        'name'                => $antivirus->name,
        'antivirus_url'       => $antivirus_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'is_dynamic'          => $is_dynamic,
        'is_dynamic_val'      => $is_dynamic_val,
        'version'             => $antivirus->antivirus_version,
        'signature'           => $antivirus->signature_version,
        'is_active'           => $is_active,
        'is_active_val'       => $is_active_val,
        'is_uptodate'         => $is_uptodate,
        'is_uptodate_val'     => $is_uptodate_val,
      ];
    }

    $softwares = [];
    foreach ($myItem->softwareversions as $softwareversion)
    {
      $softwareversion_url = $this->genereRootUrl2Link($rootUrl2, '/softwareversions/', $softwareversion->id);

      $software_url = $this->genereRootUrl2Link($rootUrl2, '/softwares/', $softwareversion->software->id);

      $softwares[] = [
        'id'        => $softwareversion->id,
        'name'      => $softwareversion->name,
        'url'       => $softwareversion_url,
        'software'  => [
          'id' => $softwareversion->software->id,
          'name' => $softwareversion->software->name,
          'url' => $software_url,
        ]
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/softwares');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('show', 'computer');
    $viewData->addData('softwares', $softwares);
    $viewData->addData('antiviruses', $myAntiviruses);

    $viewData->addTranslation('software', $translator->translatePlural('Software', 'Software', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));
    $viewData->addTranslation('antivirus', $translator->translatePlural('Antivirus', 'Antiviruses', 1));
    $viewData->addTranslation('antivirus_version', $translator->translate('Antivirus version'));
    $viewData->addTranslation('manufacturer', $translator->translatePlural('Manufacturer', 'Manufacturers', 1));
    $viewData->addTranslation('is_dynamic', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('is_active', $translator->translate('Active'));
    $viewData->addTranslation('is_uptodate', $translator->translate('Up to date'));
    $viewData->addTranslation('signature', $translator->translate('Signature database version'));

    return $view->render($response, 'subitem/softwares.html.twig', (array)$viewData);
  }

  protected function getInformationTop($item, $request)
  {
    global $translator, $basePath;

    $myItem = $item::with('operatingsystems', 'memories', 'processors', 'harddrives')->find($item->id);

    $tabInfos = [];

    $operatingsystem = '';
    foreach ($myItem->operatingsystems as $os)
    {
      $operatingsystem = $os->name;
    }
    $tabInfos[] = [
      'key'   => 'operatingsystem',
      'value' => $translator->translatePlural('Operating system', 'Operating systems', 1) . ' : ' . $operatingsystem,
      'link'  => $basePath . '/view/computers/' . $item->id . '/operatingsystem',
    ];

    $memoryTotalSize = 0;
    foreach ($myItem->memories as $memory)
    {
      if ($memory->pivot->size != '' && $memory->pivot->size > 0)
      {
        $memoryTotalSize = $memoryTotalSize + $memory->pivot->size;
      }
    }
    $tabInfos[] = [
      'key'   => 'memorytotalzize',
      'value' => $translator->translate('Mémoire totale (Mio)') . ' : ' . $memoryTotalSize,
      'link'  => $basePath . '/view/computers/' . $item->id . '/components',
    ];

    foreach ($myItem->processors as $processor)
    {
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id,
        'value' => $translator->translatePlural('Processor', 'Processors', 1) . ' : ' . $processor->name,
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id . '_frequency',
        'value' => ' - ' . $translator->translate('Fréquence (MHz)') . ' : ' . $processor->pivot->frequency,
        'link'  => null,
      ];
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id . '_nbcores_nbthreads',
        'value' => ' - ' . $translator->translate('Nombre de cœurs') . ' / ' .
                   $translator->translate('Nombre de threads') . ' : ' . $processor->pivot->nbcores . ' / ' .
                   $processor->pivot->nbthreads,
        'link'  => null,
      ];
    }

    foreach ($myItem->harddrives as $harddrive)
    {
      $tabInfos[] = [
        'key'   => 'harddrive_' . $harddrive->id,
        'value' => $translator->translatePlural('Hard drive', 'Hard drives', 1) . ' : ' . $harddrive->name,
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
      $tabInfos[] = [
        'key'   => 'harddrive_' . $harddrive->id . '_capacity',
        'value' => ' - ' . $translator->translate('Capacité (Mio)') . ' : ' . $harddrive->pivot->capacity,
        'link'  => null,
      ];
    }

    return $tabInfos;
  }

  public function showSubVirtualization(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('virtualization')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/virtualization');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVirtualmachines = [];
    foreach ($myItem->virtualization as $virtualization)
    {
      $virtualmachinesystem = '';
      $virtualmachinesystem_url = '';
      if ($virtualization->system !== null)
      {
        $virtualmachinesystem = $virtualization->system->name;
        $virtualmachinestate_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/virtualmachinesystems/',
          $virtualization->system->id
        );
      }

      $virtualmachinemodel = '';
      $virtualmachinemodel_url = '';
      if ($virtualization->type !== null)
      {
        $virtualmachinemodel = $virtualization->type->name;
        $virtualmachinemodel_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/virtualmachinetypes/',
          $virtualization->type->id
        );
      }

      $virtualmachinestate = '';
      $virtualmachinestate_url = '';
      if ($virtualization->state !== null)
      {
        $virtualmachinestate = $virtualization->state->name;
        $virtualmachinestate_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/virtualmachinestates/',
          $virtualization->state->id
        );
      }

      if ($virtualization->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      $machine_host = '';
      if ($virtualization->uuid != '' && $virtualization->uuid !== null)
      {
        $item2 = new \App\Models\Computer();
        $myItem2 = $item2::where('uuid', $virtualization->uuid)->get();

        foreach ($myItem2 as $host)
        {
          $machine_host = $host->name;
        }
      }

      $myVirtualmachines[] = [
        'name'                        => $virtualization->name,
        'comment'                     => $virtualization->comment,
        'auto'                        => $virtualization->is_dynamic,
        'auto_val'                    => $auto_val,
        'virtualmachinesystem'        => $virtualmachinesystem,
        'virtualmachinesystem_url'    => $virtualmachinesystem_url,
        'virtualmachinemodel'         => $virtualmachinemodel,
        'virtualmachinemodel_url'     => $virtualmachinemodel_url,
        'virtualmachinestate'         => $virtualmachinestate,
        'virtualmachinestate_url'     => $virtualmachinestate_url,
        'uuid'                        => $virtualization->uuid,
        'nb_proc'                     => $virtualization->vcpu,
        'memory'                      => $virtualization->ram,
        'machine_host'                => $machine_host,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('virtualmachines', $myVirtualmachines);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation(
      'virtualmachinesystem',
      $translator->translatePlural('Virtualization system', 'Virtualization systems', 1)
    );
    $viewData->addTranslation(
      'virtualmachinemodel',
      $translator->translatePlural('Virtualization model', 'Virtualization models', 1)
    );
    $viewData->addTranslation(
      'virtualmachinestate',
      $translator->translate('Status')
    );
    $viewData->addTranslation('uuid', $translator->translate('UUID'));
    $viewData->addTranslation('nb_proc', $translator->translate('processor number'));
    $viewData->addTranslation(
      'memory',
      sprintf('%1$s (%2$s)', $translator->translatePlural('Memory', 'Memories', 1), $translator->translate('Mio'))
    );
    $viewData->addTranslation('machine_host', 'Machine hote');

    return $view->render($response, 'subitem/virtualization.html.twig', (array)$viewData);
  }

  public function showSubConnections(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Computeritem();
    $myItem2 = $item2::where('computer_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/connections');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myConnections = [];
    foreach ($myItem2 as $connection)
    {
      $item3 = new $connection->item_type();
      $myItem3 = $item3->find($connection->item_id);
      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $myItem3->id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        if ($connection->is_dynamic == 1)
        {
          $auto_val = $translator->translate('Yes');
        }
        else
        {
          $auto_val = $translator->translate('No');
        }


        $serial_number = $myItem3->serial;

        $inventaire_number = $myItem3->otherserial;

        $myConnections[] = [
          'type'                 => $type_fr,
          'name'                 => $name,
          'url'                  => $url,
          'auto'                 => $connection->is_dynamic,
          'auto_val'             => $auto_val,
          'entity'               => $entity,
          'entity_url'           => $entity_url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
        ];
      }
    }

    // tri ordre alpha
    uasort($myConnections, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });
    uasort($myConnections, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('connections', $myConnections);
    $viewData->addData('show', 'computer');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('no_connection_found', $translator->translate('Not connected.'));

    return $view->render($response, 'subitem/connections.html.twig', (array)$viewData);
  }
}
