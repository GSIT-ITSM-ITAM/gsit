<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Software extends Common
{
  protected $model = '\App\Models\Software';
  protected $rootUrl2 = '/softwares/';
  protected $choose = 'softwares';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Software();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Software();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Software();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubVersions(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('versions')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/versions');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVersions = [];
    $total_install = 0;
    foreach ($myItem->versions as $version)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/softwareversions/', $version->id);

      $status = '';
      $status_url = '';
      if ($version->state !== null)
      {
        $status = $version->state->name;
        $status_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $version->state->id);
      }

      $os = '';
      $os_url = '';
      if ($version->operatingsystem !== null)
      {
        $os = $version->operatingsystem->name;
        $os_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/operatingsystems/', $version->operatingsystem->id);
      }

      $nb_install = 0;
      $nb_item_version = \App\Models\ItemSoftwareversion::where('softwareversion_id', $version->id)->count();
      if ($nb_item_version !== null)
      {
        $nb_install = $nb_item_version;
      }

      $total_install = $total_install + $nb_install;

      $myVersions[] = [
        'name'          => $version->name,
        'url'           => $url,
        'status'        => $status,
        'status_url'    => $status_url,
        'os'            => $os,
        'os_url'        => $os_url,
        'nb_install'    => $nb_install,
        'comment'       => $version->comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('versions', $myVersions);
    $viewData->addData('total_install', $total_install);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('os', $translator->translate('Operating System'));
    $viewData->addTranslation('nb_install', $translator->translatePlural('Installation', 'Installations', 2));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('no_item_found', $translator->translate('No items found.'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/versions.html.twig', (array)$viewData);
  }

  public function showSubLicenses(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Softwarelicense();
    $myItem2 = $item2::where('software_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/licenses');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myLicenses = [];
    $total_number = 0;
    $total_affected_items = 0;
    foreach ($myItem2 as $license)
    {
      $name = $license->completename;

      $url = $this->genereRootUrl2Link($rootUrl2, '/softwarelicenses/', $license->id);

      $serial = $license->serial;

      $number = $license->number;
      $total_number = $total_number + $number;

      $affected_items = 0;
      $nb_item_affected = \App\Models\ItemSoftwarelicence::where('softwarelicense_id', $license->id)->count();
      if ($nb_item_affected !== null)
      {
        $affected_items = $nb_item_affected;
      }

      $total_affected_items = $total_affected_items + $affected_items;

      $type = '';
      $type_url = '';
      if ($license->softwarelicensetype !== null)
      {
        $type = $license->softwarelicensetype->name;
        $type_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/softwarelicensetypes/',
          $license->softwarelicensetype->id
        );
      }

      $version_buy = '';
      if ($license->softwareversionsBuy !== null)
      {
        $version_buy = $license->softwareversionsBuy->name;
      }

      $version_use = '';
      if ($license->softwareversionsUse !== null)
      {
        $version_use = $license->softwareversionsUse->name;
      }

      $exp_date = $license->expire;

      $alert_expiration = false;
      $date_expiration = $license->expire;
      if ($date_expiration == null)
      {
        $date_expiration = $translator->translate("N'expire pas");
      }
      else
      {
        if ($date_expiration < date('Y-m-d H:i:s'))
        {
          $alert_expiration = true;
        }
      }

      $status = '';
      $status_url = '';
      if ($license->state !== null)
      {
        $status = $license->state->name;
        $status_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $license->state->id);
      }

      $myLicenses[] = [
        'name'                     => $name,
        'url'                      => $url,
        'serial'                   => $serial,
        'number'                   => $number,
        'affected_items'           => $affected_items,
        'type'                     => $type,
        'type_url'                 => $type_url,
        'version_buy'              => $version_buy,
        'version_use'              => $version_use,
        'date_expiration'          => $date_expiration,
        'alert_expiration'         => $alert_expiration,
        'status'                   => $status,
        'status_url'               => $status_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('licenses', $myLicenses);
    $viewData->addData('total_number', $total_number);
    $viewData->addData('total_affected_items', $total_affected_items);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('number', $translator->translate('Number'));
    $viewData->addTranslation('affected_items', $translator->translate('Affected items'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('version_buy', $translator->translate('Purchase version'));
    $viewData->addTranslation('version_use', $translator->translate('Version in use'));
    $viewData->addTranslation('expiration', $translator->translate('Expiration'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/licenses.html.twig', (array)$viewData);
  }

  public function showSubSoftwareInstall(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('versions')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/softwareinstall');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySoftwareInstall = [];
    foreach ($myItem->versions as $current_version)
    {
      $software_licences = \App\Models\Softwarelicense::where('softwareversion_id_buy', $current_version->id)
        ->orWhere('softwareversion_id_use', $current_version->id)->get();

      $mySoftwareLicense = [];
      foreach ($software_licences as $current_software_licence)
      {
        $type = '';
        if ($current_software_licence->softwarelicensetype !== null)
        {
          $type = $current_software_licence->softwarelicensetype->name;
        }

        $mySoftwareLicense[$current_software_licence->id] = [
          'id'          => $current_software_licence->id,
          'name'        => $current_software_licence->name,
          'serial'      => $current_software_licence->serial,
          'type'        => $type,
        ];
      }

      $items_version = \App\Models\ItemSoftwareversion::where('softwareversion_id', $current_version->id)->get();
      if ($items_version !== null)
      {
        foreach ($items_version as $current_item)
        {
          $item3 = new $current_item->item_type();
          $myItem3 = $item3->find($current_item->item_id);
          if ($myItem3 !== null)
          {
            $type_fr = $item3->getTitle();
            $type = $item3->getTable();

            $version = $current_version->name;

            $version_url = $this->genereRootUrl2Link($rootUrl2, '/softwareversion/', $current_version->id);

            $nom = $myItem3->name;

            $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

            $serial = $myItem3->serial;

            $otherserial = $myItem3->otherserial;

            $location = '';
            $location_url = '';
            if ($myItem3->location !== null)
            {
              $location = $myItem3->location->name;
              $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $myItem3->location->id);
            }

            $status = '';
            $status_url = '';
            if ($myItem3->state !== null)
            {
              $status = $myItem3->state->name;
              $status_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $myItem3->state->id);
            }

            $group = '';
            $group_url = '';
            if ($myItem3->group !== null)
            {
              $group = $myItem3->group->completename;
              $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $myItem3->group->id);
            }

            $user = '';
            $user_url = '';
            if ($myItem3->user !== null)
            {
              $user = $myItem3->user->completename;
              $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $myItem3->user->id);
            }

            $licences = '';
            $licences_url = '';
            $licences_serial_type = '';
            foreach (array_keys($mySoftwareLicense) as $softwarelicenseid)
            {
              $items_licences = \App\Models\ItemSoftwarelicence::where([
                'item_id' => $current_item->item_id,
                'item_type' => $current_item->item_type,
                'softwarelicense_id' => $softwarelicenseid
              ])->get();
              if (count($items_licences) == 1)
              {
                $licences = $mySoftwareLicense[$softwarelicenseid]['name'];
                $licences_url = $this->genereRootUrl2Link($rootUrl2, '/softwarelicenses/', $softwarelicenseid);

                if ($mySoftwareLicense[$softwarelicenseid]['serial'] != '')
                {
                  $licences_serial_type = $licences_serial_type . $mySoftwareLicense[$softwarelicenseid]['serial'];
                }
                if ($mySoftwareLicense[$softwarelicenseid]['type'] != '')
                {
                  $toadd = ' (' . $mySoftwareLicense[$softwarelicenseid]['type'] . ')';
                  $licences_serial_type = $licences_serial_type . $toadd;
                }
                if ($licences_serial_type != '')
                {
                  $licences_serial_type = ' - ' . $licences_serial_type;
                }
              }
            }

            $date_install = $current_item->date_install;

            $mySoftwareInstall[] = [
              'version'                 => $version,
              'version_url'             => $version_url,
              'type'                    => $type_fr,
              'nom'                     => $nom,
              'nom_url'                 => $nom_url,
              'serial'                  => $serial,
              'otherserial'             => $otherserial,
              'location'                => $location,
              'location_url'            => $location_url,
              'status'                  => $status,
              'status_url'              => $status_url,
              'group'                   => $group,
              'group_url'               => $group_url,
              'user'                    => $user,
              'user_url'                => $user_url,
              'licences'                => $licences,
              'licences_url'            => $licences_url,
              'licences_serial_type'    => $licences_serial_type,
              'date_install'            => $date_install,
            ];
          }
        }
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwareinstall', $mySoftwareInstall);

    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));
    $viewData->addTranslation('type', $translator->translate('Item type'));
    $viewData->addTranslation('nom', $translator->translate('Name'));
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('otherserial', $translator->translate('Inventory number'));
    $viewData->addTranslation('location', $translator->translatePlural('Location', 'Locations', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('licences', $translator->translatePlural('License', 'Licenses', 1));
    $viewData->addTranslation('date_install', $translator->translate('Installation date'));

    return $view->render($response, 'subitem/softwareinstall.html.twig', (array)$viewData);
  }
}
