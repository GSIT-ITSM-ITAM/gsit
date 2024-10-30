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

  public function showSubOperatingSystem(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('operatingsystems')->find($args['id']);

    $operatingsystem = [];
    foreach ($myItem->operatingsystems as $os)
    {
      $osa = \App\Models\Operatingsystemarchitecture::find($os->pivot->operatingsystemarchitecture_id);
      $osv = \App\Models\Operatingsystemversion::find($os->pivot->operatingsystemversion_id);
      $ossp = \App\Models\Operatingsystemservicepack::find($os->pivot->operatingsystemservicepack_id);
      $oskv = \App\Models\Operatingsystemkernelversion::find($os->pivot->operatingsystemkernelversion_id);
      $ose = \App\Models\Operatingsystemedition::find($os->pivot->operatingsystemedition_id);
      $osln = $os->pivot->license_number;
      $oslid = $os->pivot->licenseid;
      $osid = $os->pivot->installationdate;
      $oswo = $os->pivot->winowner;
      $oswc = $os->pivot->wincompany;
      $osoc = $os->pivot->oscomment;
      $oshid = $os->pivot->hostid;

      $architecture = '';
      if ($osa !== null)
      {
        $architecture = $osa->name;
      }
      $version = '';
      if ($osv !== null)
      {
        $version = $osv->name;
      }
      $servicepack = '';
      if ($ossp !== null)
      {
        $servicepack = $ossp->name;
      }
      $kernelversion = '';
      if ($oskv !== null)
      {
        $kernelversion = $oskv->name;
      }
      $edition = '';
      if ($ose !== null)
      {
        $edition = $ose->name;
      }
      $license_number = '';
      if ($osln !== null)
      {
        $license_number = $osln;
      }
      $licenseid = '';
      if ($oslid !== null)
      {
        $licenseid = $oslid;
      }
      $installationdate = '';
      if ($osid !== null)
      {
        $installationdate = $osid;
      }
      $winowner = '';
      if ($oswo !== null)
      {
        $winowner = $oswo;
      }
      $wincompany = '';
      if ($oswc !== null)
      {
        $wincompany = $oswc;
      }
      $oscomment = '';
      if ($osoc !== null)
      {
        $oscomment = $osoc;
      }
      $hostid = '';
      if ($oshid !== null)
      {
        $hostid = $oshid;
      }

      $operatingsystem = [
        'id' => $os->id,
        'name' => $os->name,
        'architecture' => $architecture,
        'architecture_id' => $os->pivot->operatingsystemarchitecture_id,
        'version' => $version,
        'version_id' => $os->pivot->operatingsystemversion_id,
        'servicepack' => $servicepack,
        'servicepack_id' => $os->pivot->operatingsystemservicepack_id,
        'kernelversion' => $kernelversion,
        'kernelversion_id' => $os->pivot->operatingsystemkernelversion_id,
        'edition' => $edition,
        'edition_id' => $os->pivot->operatingsystemedition_id,
        'licensenumber' => $license_number,
        'licenseid' => $licenseid,
        'installationdate' => $installationdate,
        'winowner' => $winowner,
        'wincompany' => $wincompany,
        'oscomment' => $oscomment,
        'hostid' => $hostid,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/operatingsystem');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDef = [];
    $myItemData = [];


    $getDefs = $item->getSpecificFunction('getDefinitionOperatingSystem');

    $myItemData = [
      'name'  => $operatingsystem['name'],
      'architecture'  => [
        'id' => $operatingsystem['architecture_id'],
        'name' => $operatingsystem['architecture'],
      ],
      'kernelversion'  => [
        'id' => $operatingsystem['kernelversion_id'],
        'name' => $operatingsystem['kernelversion'],
      ],
      'version'  => [
        'id' => $operatingsystem['version_id'],
        'name' => $operatingsystem['version'],
      ],
      'servicepack'  => [
        'id' => $operatingsystem['servicepack_id'],
        'name' => $operatingsystem['servicepack'],
      ],
      'edition'  => [
        'id' => $operatingsystem['edition_id'],
        'name' => $operatingsystem['edition'],
      ],
      'licenseid'  => $operatingsystem['licenseid'],
      'licensenumber'  => $operatingsystem['licensenumber'],
    ];
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('operatingsystem', $operatingsystem);

    return $view->render($response, 'subitem/operatingsystems.html.twig', (array)$viewData);
  }

  public function showSubSoftwares(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('softwareversions.software:id,name', 'antiviruses')->find($args['id']);

    $myAntiviruses = [];
    foreach ($myItem->antiviruses as $antivirus)
    {
      $myAntiviruses[] = [
        'name'        => $antivirus->name,
        'publisher'   => $antivirus->manufacturer_id,
        'is_dynamic'  => $antivirus->is_dynamic,
        'version'     => $antivirus->antivirus_version,
        'signature'   => $antivirus->signature_version,
        'is_active'   => $antivirus->is_active,
        'is_uptodate' => $antivirus-> is_uptodate
      ];
    }

    $softwares = [];
    foreach ($myItem->softwareversions as $softwareversion)
    {
      $softwares[] = [
        'id' => $softwareversion->id,
        'name' => $softwareversion->name,
        'software' => [
          'id' => $softwareversion->software->id,
          'name' => $softwareversion->software->name,
        ]
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/softwares');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwares', $softwares);
    $viewData->addData('antiviruses', $myAntiviruses);

    $viewData->addTranslation('software', $translator->translatePlural('Software', 'Software', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));

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

  public function showSubComponents(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(
      'memories',
      'firmwares',
      'processors',
      'harddrives',
      'batteries',
      'soundcards',
      'controllers'
    )->find($args['id']);

    $myMemories = [];
    foreach ($myItem->memories as $memory)
    {
      $loc = \App\Models\Location::find($memory->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($memory->manufacturer !== null)
      {
        $manufacturer = $memory->manufacturer->name;
      }

      $myMemories[] = [
        'name'          => $memory->name,
        'manufacturer'  => $manufacturer,
        'type'          => $memory->type->name,
        'frequence'     => $memory->frequence,
        'size'          => $memory->pivot->size,
        'serial'        => $memory->pivot->serial,
        'busID'         => $memory->pivot->busID,
        'location'      => $location,
        'color'         => 'red',
      ];
    }

    $myFirmwares = [];
    foreach ($myItem->firmwares as $firmware)
    {
      $loc = \App\Models\Location::find($firmware->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($firmware->manufacturer !== null)
      {
        $manufacturer = $firmware->manufacturer->name;
      }

      $type = '';
      if ($firmware->type !== null)
      {
        $type = $firmware->type->name;
      }

      $myFirmwares[] = [
        'name'          => $firmware->name,
        'manufacturer'  => $manufacturer,
        'type'          => $type,
        'version'       => $firmware->version,
        'date'          => $firmware->date,
        'location'      => $location,
        'color'         => 'orange',
      ];
    }

    $myProcessors = [];
    foreach ($myItem->processors as $processor)
    {
      $loc = \App\Models\Location::find($processor->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($processor->manufacturer !== null)
      {
        $manufacturer = $processor->manufacturer->name;
      }

      $myProcessors[] = [
        'name'          => $processor->name,
        'manufacturer'  => $manufacturer,
        'frequency'     => $processor->pivot->frequency,
        'nbcores'       => $processor->pivot->nbcores,
        'nbthreads'     => $processor->pivot->nbthreads,
        'location'      => $location,
        'color'         => 'olive',
      ];
    }

    $myHarddrives = [];
    foreach ($myItem->harddrives as $harddrive)
    {
      $loc = \App\Models\Location::find($harddrive->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($harddrive->manufacturer !== null)
      {
        $manufacturer = $harddrive->manufacturer->name;
      }

      $interface = '';
      if ($harddrive->interface !== null)
      {
        $interface = $harddrive->interface->name;
      }

      $myHarddrives[] = [
        'name'            => $harddrive->name,
        'manufacturer'    => $manufacturer,
        'rpm'             => $harddrive->rpm,
        'cache'           => $harddrive->cache,
        'interface'       => $interface,
        'capacity'        => $harddrive->pivot->capacity,
        'serial'          => $harddrive->pivot->serial,
        'location'        => $location,
        'color'         => 'teal',
      ];
    }

    $myBatteries = [];
    foreach ($myItem->batteries as $battery)
    {
      $loc = \App\Models\Location::find($battery->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($battery->manufacturer !== null)
      {
        $manufacturer = $battery->manufacturer->name;
      }

      $type = '';
      if ($battery->type !== null)
      {
        $type = $battery->type->name;
      }

      $myBatteries[] = [
        'name'                => $battery->name,
        'manufacturer'        => $manufacturer,
        'type'                => $type,
        'voltage'             => $battery->voltage,
        'capacity'            => $battery->capacity,
        'serial'              => $battery->pivot->serial,
        'manufacturing_date'  => $battery->pivot->manufacturing_date,
        'location'            => $location,
        'color'               => 'blue',
      ];
    }

    $mySoundcards = [];
    foreach ($myItem->soundcards as $soundcard)
    {
      $loc = \App\Models\Location::find($soundcard->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($soundcard->manufacturer !== null)
      {
        $manufacturer = $soundcard->manufacturer->name;
      }

      $mySoundcards[] = [
        'name'            => $soundcard->name,
        'manufacturer'    => $manufacturer,
        'type'            => $soundcard->type,
        'location'        => $location,
        'color'           => 'purple',
      ];
    }

    $myControllers = [];
    foreach ($myItem->controllers as $controller)
    {
      $loc = \App\Models\Location::find($controller->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($controller->manufacturer !== null)
      {
        $manufacturer = $controller->manufacturer->name;
      }

      $interface = '';
      if ($controller->interface !== null)
      {
        $interface = $controller->interface->name;
      }

      $myControllers[] = [
        'name'            => $controller->name,
        'manufacturer'    => $manufacturer,
        'interface'       => $interface,
        'location'        => $location,
        'color'           => 'brown',
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/components');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('memories', $myMemories);
    $viewData->addData('firmwares', $myFirmwares);
    $viewData->addData('processors', $myProcessors);
    $viewData->addData('harddrives', $myHarddrives);
    $viewData->addData('batteries', $myBatteries);
    $viewData->addData('soundcards', $mySoundcards);
    $viewData->addData('controllers', $myControllers);

    $viewData->addTranslation('memory', 'Memoire');
    $viewData->addTranslation('manufacturer', 'Fabricant');
    $viewData->addTranslation('type', 'Type');
    $viewData->addTranslation('frequence', 'Fréquence');
    $viewData->addTranslation('size', 'Taille (Mio)');
    $viewData->addTranslation('serial', 'Numéro de série');
    $viewData->addTranslation('location', 'Lieu');
    $viewData->addTranslation('busID', 'Position du composant sur son bus');
    $viewData->addTranslation('firmware', 'Micrologiciel');
    $viewData->addTranslation('version', 'Version');
    $viewData->addTranslation('install_date', "Date d'installation");
    $viewData->addTranslation('processor', 'Processeur');
    $viewData->addTranslation('frequence_mhz', 'Fréquence (MHz)');
    $viewData->addTranslation('nbcores', 'Nombre de cœurs');
    $viewData->addTranslation('nbthreads', 'Nombre de threads');
    $viewData->addTranslation('harddrive', 'Disque dur');
    $viewData->addTranslation('rpm', 'Vitesse de rotation');
    $viewData->addTranslation('cache', 'Cache');
    $viewData->addTranslation('interface', 'Interface');
    $viewData->addTranslation('capacity', 'Capacité (Mio)');
    $viewData->addTranslation('battery', 'Batterie');
    $viewData->addTranslation('voltage_mv', 'Voltage (mV)');
    $viewData->addTranslation('capacity_mwh', 'Capacité (mWh)');
    $viewData->addTranslation('manufacturing_date', 'Date de fabrication');
    $viewData->addTranslation('soundcard', 'Carte son');
    $viewData->addTranslation('controller', 'Contrôleur');


    return $view->render($response, 'subitem/components.html.twig', (array)$viewData);
  }

  public function showSubVolumes(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('volumes')->find($args['id']);

    $myVolumes = [];
    foreach ($myItem->volumes as $volume)
    {
      if ($volume->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      $filesystem = '';
      if ($volume->filesystem !== null)
      {
        $filesystem = $volume->filesystem->name;
      }


      $usedpercent = 100;
      if ($volume->totalsize > 0)
      {
        $usedpercent = 100 - round(($volume->freesize / $volume->totalsize) * 100);
      }

      $encryption_status_val = '';
      if ($volume->encryption_status == 0)
      {
        $encryption_status_val = $translator->translate('Non chiffré');
      }
      if ($volume->encryption_status == 1)
      {
        $encryption_status_val = $translator->translate('Chiffré');
      }
      if ($volume->encryption_status == 2)
      {
        $encryption_status_val = $translator->translate('Partiellement chiffré');
      }

      $myVolumes[] = [
        'name'                      => $volume->name,
        'auto'                      => $volume->is_dynamic,
        'auto_val'                  => $auto_val,
        'device'                    => $volume->device,
        'mountpoint'                => $volume->mountpoint,
        'filesystem'                => $filesystem,
        'totalsize'                 => $volume->totalsize,
        'freesize'                  => $volume->freesize,
        'usedpercent'               => $usedpercent,
        'encryption_status'         => $volume->encryption_status,
        'encryption_status_val'     => $encryption_status_val,
        'encryption_tool'           => $volume->encryption_tool,
        'encryption_algorithm'      => $volume->encryption_algorithm,
        'encryption_type'           => $volume->encryption_type,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/volumes');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('volumes', $myVolumes);

    $viewData->addTranslation('auto', 'Inventaire automatique');
    $viewData->addTranslation('device', 'Partition');
    $viewData->addTranslation('mountpoint', 'Point de montage');
    $viewData->addTranslation('filesystem', 'Système de fichiers');
    $viewData->addTranslation('totalsize', 'Taille totale');
    $viewData->addTranslation('freesize', 'Taille libre');
    $viewData->addTranslation('encryption', 'Chiffrement');
    $viewData->addTranslation('encryption_algorithm', 'Algorithme de chiffrement');
    $viewData->addTranslation('encryption_tool', 'Outil de chiffrement');
    $viewData->addTranslation('encryption_type', 'Type de chiffrement');
    $viewData->addTranslation('usedpercent', 'Pourcentage utilisé');

    return $view->render($response, 'subitem/volumes.html.twig', (array)$viewData);
  }

  public function showSubVirtualization(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('virtualization')->find($args['id']);

    $myVirtualmachines = [];
    foreach ($myItem->virtualization as $virtualization)
    {
      $virtualmachinesystem = '';
      if ($virtualization->system !== null)
      {
        $virtualmachinesystem = $virtualization->system->name;
      }

      $virtualmachinemodel = '';
      if ($virtualization->type !== null)
      {
        $virtualmachinemodel = $virtualization->type->name;
      }

      $virtualmachinestate = '';
      if ($virtualization->state !== null)
      {
        $virtualmachinestate = $virtualization->state->name;
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
      if ($virtualization->uuid != '' && $virtualization->uuid != null)
      {
        $item2 = new \App\Models\Computer();
        $myItem2 = $item2::where('uuid', $virtualization->uuid)->get();

        foreach ($myItem2 as $host)
        {
          $machine_host = $host->name;
        }
      }

      $myVirtualmachines[] = [
        'name'                    => $virtualization->name,
        'comment'                 => $virtualization->comment,
        'auto'                    => $virtualization->is_dynamic,
        'auto_val'                => $auto_val,
        'virtualmachinesystem'    => $virtualmachinesystem,
        'virtualmachinemodel'     => $virtualmachinemodel,
        'virtualmachinestate'     => $virtualmachinestate,
        'uuid'                    => $virtualization->uuid,
        'nb_proc'                 => $virtualization->vcpu,
        'memory'                  => $virtualization->ram,
        'machine_host'            => $machine_host,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/virtualization');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('virtualmachines', $myVirtualmachines);

    $viewData->addTranslation('name', 'Nom');
    $viewData->addTranslation('comment', 'Commentaires');
    $viewData->addTranslation('auto', 'Inventaire automatique');
    $viewData->addTranslation('virtualmachinesystem', 'Système de virtualisation');
    $viewData->addTranslation('virtualmachinemodel', 'Modèle de virtualisation');
    $viewData->addTranslation('virtualmachinestate', 'Statut');
    $viewData->addTranslation('uuid', 'UUID');
    $viewData->addTranslation('nb_proc', 'Nombre de processeurs');
    $viewData->addTranslation('memory', 'Mémoire (Mio)');
    $viewData->addTranslation('machine_host', 'Machine hote');

    return $view->render($response, 'subitem/virtualization.html.twig', (array)$viewData);
  }

  public function showCertificates(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('certificates')->find($args['id']);

    $myCertificates = [];
    foreach ($myItem->certificates as $certificate)
    {
      $type = '';
      if ($certificate->type !== null)
      {
        $type = $certificate->type->name;
      }
      $entity = '';
      if ($certificate->entity !== null)
      {
        $entity = $certificate->entity->name;
      }

      $date_expiration = $certificate->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = $translator->translate("N'expire pas");
      }
      $state = '';
      if ($certificate->state !== null)
      {
        $state = $certificate->state->name;
      }


      $myCertificates[] = [
        'name'              => $certificate->name,
        'entity'            => $entity,
        'type'              => $type,
        'dns_name'          => $certificate->dns_name,
        'dns_suffix'        => $certificate->dns_suffix,
        'created_at'        => $certificate->created_at,
        'date_expiration'   => $date_expiration,
        'state'             => $state,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/certificates');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('certificates', $myCertificates);

    $viewData->addTranslation('name', 'Nom');
    $viewData->addTranslation('entity', 'Entité');
    $viewData->addTranslation('type', 'Type');
    $viewData->addTranslation('dns_name', 'Nom DNS');
    $viewData->addTranslation('dns_suffix', 'Suffixe DNS');
    $viewData->addTranslation('created_at', 'Date de création');
    $viewData->addTranslation('date_expiration', "Date d'expiration");
    $viewData->addTranslation('state', 'Statut');

    return $view->render($response, 'subitem/certificates.html.twig', (array)$viewData);
  }

  public function showExternalLinks(Request $request, Response $response, $args): Response
  {
    global $translator;

    $computermodelclass = str_ireplace('\\v1\\Controllers\\', '\\Models\\', get_class($this));


    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::find($args['id']);

    $item2 = new \App\Models\LinkItemtype();
    $externallinks = $item2::with('links')->where('item_type', $computermodelclass)->get();

    $item3 = new \App\Models\DomainItem();
    $domainitems = $item3->where(['item_id' => $args['id'], 'item_type' => $computermodelclass])->get();

    $myExternalLinks = [];
    foreach ($externallinks as $externallink)
    {
      $name = '';
      $open_window = 0;
      $link = '';
      $data = '';
      $generate = '';
      if ($externallink->links !== null)
      {
        $name = $externallink->links->name;
        $open_window = $externallink->links->open_window;
        $link = $externallink->links->link;
        $data = $externallink->links->data;

        if ($myItem->location == null)
        {
          $myItem->location->id = '';
          $myItem->location->name = '';
        }

        $domains = [];
        foreach ($domainitems as $domainitem)
        {
          if ($domainitem->domain != null)
          {
            $domains[] = $domainitem->domain->name;
          }
        }

        if ($myItem->network == null)
        {
          $myItem->network->name = '';
        }

        if ($myItem->user == null)
        {
          $myItem->user->name = '';
        }
        if ($myItem->group == null)
        {
          $myItem->group->name = '';
        }

        $ips = [];
        $macs = [];

        $itemsLink = [
          'id' => $externallink->links->id,
          'name' => $myItem->name,
          'serial' => $myItem->serial,
          'otherserial' => $myItem->otherserial,
          'location_id' => $myItem->location->id,
          'location' => $myItem->location->name,
          'domains' => $domains,
          'network' => $myItem->network->name,
          'comment' => $myItem->comment,
          'user' => $myItem->user->name,
          'group' => $myItem->group->name,
          // 'realname' => $realname,
          // 'firstname' => $firstname,
          // 'login' => $login,
          // 'ips' => $ips,
          // 'macs' => $macs,
        ];

        $generate = $name . ' : ' . self::generateLinkContents($data, $itemsLink, true);
      }


      $myExternalLinks[] = [
        'name'          => $name,
        'open_window'   => $open_window,
        'link'          => $link,
        'generate'      => $generate,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/externallinks');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('externallinks', $myExternalLinks);

    return $view->render($response, 'subitem/externallinks.html.twig', (array)$viewData);
  }


  private function generateLinkContents($link, $item, $replaceByBr = false)
  {
    $new_link = $link;
    if ($replaceByBr === true)
    {
      $new_link = str_ireplace("\n", "<br>", $new_link);
    }
    $matches = [];
    if (preg_match_all('/\[FIELD:(\w+)\]/', $new_link, $matches))
    {
      foreach ($matches[1] as $key => $field)
      {
        $new_link = self::checkAndReplaceProperty($item, $field, $matches[0][$key], $new_link, $replaceByBr);
      }
    }

    if (strstr($new_link, "[ID]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'id', "[ID]", $new_link, $replaceByBr);
    }
    if (strstr($link, "[NAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'name', "[NAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[SERIAL]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'serial', "[SERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[OTHERSERIAL]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'otherserial', "[OTHERSERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATIONID]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'location_id', "[LOCATIONID]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATION]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'location', "[LOCATION]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[DOMAIN]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'domains', "[DOMAIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[NETWORK]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'network', "[NETWORK]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[REALNAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'realname', "[REALNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[FIRSTNAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'firstname', "[FIRSTNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOGIN]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'login', "[LOGIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[USER]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'user', "[USER]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[GROUP]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'group', "[GROUP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[IP]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'ips', "[IP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[MAC]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'macs', "[MAC]", $new_link, $replaceByBr);
    }

    return $new_link;
  }

  private function checkAndReplaceProperty($item, $field, $strToReplace, $new_link, $replaceByBr = false)
  {
    $ret = $new_link;

    if (array_key_exists($field, $item))
    {
      if (is_array($item[$field]))
      {
        $tmp = '';
        foreach ($item[$field] as $val)
        {
          if ($tmp != '')
          {
            $tmp = $tmp  . "\n";
          }
          $tmp = $tmp . $val;
        }
        $ret = str_replace($strToReplace, $tmp, $ret);
      } else {
        $ret = str_replace($strToReplace, $item[$field], $ret);
      }
      if ($replaceByBr === true)
      {
        $ret = str_ireplace("\n", "<br>", $ret);
      }
    }
    return $ret;
  }
}
