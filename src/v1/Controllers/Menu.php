<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

final class Menu
{
  protected $rights = [];

  public function getMenu(Request $request)
  {
    $this->loadRights();
    return $this->cleanMenuByDisplay($this->menuData($request));
  }

  private function menuData($request)
  {
    global $basePath, $translator;

    $uri = $request->getUri();
    $activePath = $uri->getPath();

    return [
      [
        'name' => $translator->translate('Assets'),
        'icon' => 'laptop house',
        'sub'  => [
          [
            'name'  => $translator->translatePlural('Computer', 'Computers', 2),
            'link'  => $basePath . '/view/computers',
            'icon'  => 'laptop',
            'class' => $activePath == $basePath . '/view/computers' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Computer'),
          ],
          [
            'name'  => $translator->translatePlural('Monitor', 'Monitors', 2),
            'link'  => $basePath . '/view/monitors',
            'icon'  => 'desktop',
            'class' => $activePath == $basePath . '/view/monitors' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Monitor'),
          ],
          [
            'name' => $translator->translatePlural('Software', 'Software', 2),
            'link' => $basePath . '/view/softwares',
            'icon' => 'cube',
            'class' => $activePath == $basePath . '/view/softwares' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Software'),
          ],
          [
            'name' => $translator->translatePlural('Network device', 'Network devices', 2),
            'link' => $basePath . '/view/networkequipments',
            'icon' => 'network wired',
            'class' => $activePath == $basePath . '/view/networkequipments' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Networkequipment'),
          ],
          [
            'name' => $translator->translatePlural('Device', 'Devices', 2),
            'link' => $basePath . '/view/peripherals',
            'icon' => 'usb',
            'class' => $activePath == $basePath . '/view/peripherals' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Peripheral'),
          ],
          [
            'name' => $translator->translatePlural('Printer', 'Printers', 2),
            'link' => $basePath . '/view/printers',
            'icon' => 'print',
            'class' => $activePath == $basePath . '/view/printers' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Printer'),
          ],
          [
            'name' => $translator->translatePlural('Phone', 'Phones', 2),
            'link' => $basePath . '/view/phones',
            'icon' => 'phone',
            'class' => $activePath == $basePath . '/view/phones' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Phone'),
          ],
          [
            'name' => $translator->translatePlural('Cartridge', 'Cartridges', 2),
            'link' => $basePath . '/view/cartridgeitems',
            'icon' => 'fill drip',
            'class' => $activePath == $basePath . '/view/cartridgeitems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Cartridgeitem'),
          ],
          [
            'name' => $translator->translatePlural('Consumable', 'Consumables', 2),
            'link' => $basePath . '/view/consumableitems',
            'icon' => 'box open',
            'class' => $activePath == $basePath . '/view/consumableitems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Consumableitem'),
          ],
          [
            'name' => $translator->translatePlural('Rack', 'Racks', 2),
            'link' => $basePath . '/view/racks',
            'icon' => 'server',
            'class' => $activePath == $basePath . '/view/racks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Rack'),
          ],
          [
            'name' => $translator->translatePlural('Enclosure', 'Enclosures', 2),
            'link' => $basePath . '/view/enclosures',
            'icon' => 'th',
            'class' => $activePath == $basePath . '/view/enclosures' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Enclosure'),
          ],
          [
            'name' => $translator->translatePlural('PDU', 'PDUs', 2),
            'link' => $basePath . '/view/pdus',
            'icon' => 'plug',
            'class' => $activePath == $basePath . '/view/pdus' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Pdu'),
          ],
          [
            'name' => $translator->translatePlural('Passive device', 'Passive devices', 2),
            'link' => $basePath . '/view/passivedcequipments',
            'icon' => 'th list',
            'class' => $activePath == $basePath . '/view/passivedcequipments' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Passivedcequipment'),
          ],
          [
            'name' => $translator->translatePlural('Simcard', 'Simcards', 2),
            'link' => $basePath . '/view/itemdevicesimcards',
            'icon' => 'sim card',
            'class' => $activePath == $basePath . '/view/item_devicesimcards' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\ItemDevicesimcard'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('Assistance'),
        'icon' => 'hands helping',
        'sub'  => [
          [
            'name'  => $translator->translatePlural('Ticket', 'Tickets', 2),
            'link'  => $basePath . '/view/tickets',
            'icon'  => 'hands helping',
            'class' => $activePath == $basePath . '/view/tickets' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Ticket'),
          ],
          [
            'name'  => $translator->translatePlural('Problem', 'Problems', 2),
            'link'  => $basePath . '/view/problems',
            'icon'  => 'drafting compass',
            'class' => $activePath == $basePath . '/view/problems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Problem'),
          ],
          [
            'name'  => $translator->translatePlural('Change', 'Changes', 2),
            'link'  => $basePath . '/view/changes',
            'icon'  => 'paint roller',
            'class' => $activePath == $basePath . '/view/changes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Change'),
          ],
          [
            'name'  => $translator->translate('Recurrent tickets'),
            'link'  => $basePath . '/view/ticketrecurrents',
            'icon'  => 'stopwatch',
            'class' => $activePath == $basePath . '/view/ticketrecurrents' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Ticketrecurrent'),
          ],
        ],
      ],
      [
        'name' => $translator->translatePlural('Form', 'Forms', 2),
        'icon' => 'hands helping',
        'sub'  => [
          [
            'name'  => $translator->translatePlural('Form', 'Forms', 2),
            'link'  => $basePath . '/view/forms',
            'icon'  => 'hands helping',
            'class' => $activePath == $basePath . '/view/forms' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Forms\Form'),
          ],
          [
            'name'  => $translator->translatePlural('Section', 'Sections', 2),
            'link'  => $basePath . '/view/sections',
            'icon'  => 'exclamation triangle',
            'class' => $activePath == $basePath . '/view/sections' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Forms\Section'),
          ],
          [
            'name'  => $translator->translatePlural('Question', 'Questions', 2),
            'link'  => $basePath . '/view/questions',
            'icon'  => 'clipboard check',
            'class' => $activePath == $basePath . '/view/questions' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Forms\Question'),
          ],
          [
            'name'  => $translator->translatePlural('Answer', 'Answers', 2),
            'link'  => $basePath . '/view/answers',
            'icon'  => 'clipboard check',
            'class' => $activePath == $basePath . '/view/answers' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Forms\Answer'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('Management'),
        'icon' => 'block layout',
        'sub'  => [
          [
            'name' => $translator->translatePlural('License', 'Licenses', 2),
            'link' => $basePath . '/view/softwarelicenses',
            'icon' => 'key',
            'class' => $activePath == $basePath . '/view/softwarelicenses' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Softwarelicense'),
          ],
          [
            'name' => $translator->translatePlural('Budget', 'Budgets', 2),
            'link' => $basePath . '/view/budgets',
            'icon' => 'calculator',
            'class' => $activePath == $basePath . '/view/budgets' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Budget'),
          ],
          [
            'name' => $translator->translatePlural('Supplier', 'Suppliers', 2),
            'link' => $basePath . '/view/suppliers',
            'icon' => 'dolly',
            'class' => $activePath == $basePath . '/view/suppliers' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Supplier'),
          ],
          [
            'name' => $translator->translatePlural('Contact', 'Contacts', 2),
            'link' => $basePath . '/view/contacts',
            'icon' => 'user tie',
            'class' => $activePath == $basePath . '/view/contacts' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Contact'),
          ],
          [
            'name' => $translator->translatePlural('Contract', 'Contracts', 2),
            'link' => $basePath . '/view/contracts',
            'icon' => 'file signature',
            'class' => $activePath == $basePath . '/view/contracts' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Contract'),
          ],
          [
            'name' => $translator->translatePlural('Line', 'Lines', 2),
            'link' => $basePath . '/view/lines',
            'icon' => 'phone',
            'class' => $activePath == $basePath . '/view/lines' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Line'),
          ],
          [
            'name' => $translator->translatePlural('Certificate', 'Certificates', 2),
            'link' => $basePath . '/view/certificates',
            'icon' => 'certificate',
            'class' => $activePath == $basePath . '/view/certificates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Certificate'),
          ],
          [
            'name' => $translator->translatePlural('Document', 'Documents', 2),
            'link' => $basePath . '/view/documents',
            'icon' => 'file',
            'class' => $activePath == $basePath . '/view/documents' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Document'),
          ],
          [
            'name' => $translator->translatePlural('Data center', 'Data centers', 2),
            'link' => $basePath . '/view/datacenters',
            'icon' => 'warehouse',
            'class' => $activePath == $basePath . '/view/datacenters' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Datacenter'),
          ],
          [
            'name' => $translator->translatePlural('Cluster', 'Clusters', 2),
            'link' => $basePath . '/view/clusters',
            'icon' => 'project diagram',
            'class' => $activePath == $basePath . '/view/clusters' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Cluster'),
          ],
          [
            'name' => $translator->translatePlural('Domain', 'Domains', 2),
            'link' => $basePath . '/view/domains',
            'icon' => 'globe americas',
            'class' => $activePath == $basePath . '/view/domains' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Domain'),
          ],
          [
            'name' => $translator->translatePlural('Appliance', 'Appliances', 2),
            'link' => $basePath . '/view/appliances',
            'icon' => 'cubes',
            'class' => $activePath == $basePath . '/view/appliances' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Appliance'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('Tools'),
        'icon' => 'toolbox',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Project', 'Projects', 2),
            'link' => $basePath . '/view/projects',
            'icon' => 'columns',
            'class' => $activePath == $basePath . '/view/projects' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Project'),
          ],
          [
            'name' => $translator->translatePlural('Note', 'Notes', 2),
            'link' => $basePath . '/view/reminders',
            'icon' => 'sticky note',
            'class' => $activePath == $basePath . '/view/reminders' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Reminder'),
          ],
          [
            'name' => $translator->translatePlural('RSS feed', 'RSS feed', 2),
            'link' => $basePath . '/view/rssfeeds',
            'icon' => 'rss',
            'class' => $activePath == $basePath . '/view/rssfeeds' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Rssfeed'),
          ],
          [
            'name' => $translator->translatePlural('Saved search', 'Saved searches', 2),
            'link' => $basePath . '/view/savedsearchs',
            'icon' => 'bookmark',
            'class' => $activePath == $basePath . '/view/savedsearchs' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Savedsearch'),
          ],
          [
            'name' => $translator->translatePlural('Alert', 'Alerts', 2),
            'link' => $basePath . '/view/news',
            'icon' => 'bell',
            'class' => $activePath == $basePath . '/view/news' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Alert'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('Administration'),
        'icon' => 'screwdriver',
        'sub'  => [
          [
            'name' => $translator->translatePlural('User', 'Users', 2),
            'link' => $basePath . '/view/users',
            'icon' => 'user',
            'class' => $activePath == $basePath . '/view/users' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\User'),
          ],
          [
            'name' => $translator->translatePlural('Group', 'Groups', 2),
            'link' => $basePath . '/view/groups',
            'icon' => 'users',
            'class' => $activePath == $basePath . '/view/groups' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Group'),
          ],
          [
            'name' => $translator->translatePlural('Entity', 'Entities', 2),
            'link' => $basePath . '/view/entities',
            'icon' => 'layer group',
            'class' => $activePath == $basePath . '/view/entities' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Entity'),
          ],
          // [
          //   'name' => $translator->translatePlural('Rule', 'Rules', 2),
          //   'link' => $basePath . '/view/rules',
          //   'icon' => 'book',
          //   'class' => $activePath == $basePath . '/view/rules' ? 'active blue' : '',
          // ],
          [
            'name' => $translator->translate('Business rules for tickets'),
            'link' => $basePath . '/view/rules/tickets',
            'icon' => 'magic',
            'class' => $activePath == $basePath . '/view/rules/tickets' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Rules\Ticket'),
          ],
          [
            'name' => $translator->translatePlural('Profile', 'Profiles', 2),
            'link' => $basePath . '/view/profiles',
            'icon' => 'user check',
            'class' => $activePath == $basePath . '/view/profiles' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Profile'),
          ],
          [
            'name' => $translator->translate('Notification queue'),
            'link' => $basePath . '/view/queuednotifications',
            'icon' => 'list alt',
            'class' => $activePath == $basePath . '/view/queuednotifications' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Queuednotification'),
          ],
          [
            'name' => $translator->translatePlural('Log', 'Logs', 2),
            'link' => $basePath . '/view/events',
            'icon' => 'scroll',
            'class' => $activePath == $basePath . '/view/events' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Event'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('Setup'),
        'icon' => 'tools',
        'sub'  => [
          [
            'name' => $translator->translate('Authentication SSO'),
            'link' => $basePath . '/view/authssos',
            'icon' => 'id card alternate',
            'class' => $activePath == $basePath . '/view/authssos' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Authsso'),
          ],
          [
            'name' => $translator->translate('Provisionning LDAP'),
            'link' => $basePath . '/view/authldaps',
            'icon' => 'address book outline',
            'class' => $activePath == $basePath . '/view/authldaps' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Authldap'),
          ],
          [
            'name' => $translator->translatePlural('Notification', 'Notifications', 2) . ' - ' .
                      $translator->translatePlural('Notification template', 'Notification templates', 2),
            'link' => $basePath . '/view/notificationtemplates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/notificationtemplates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Notificationtemplate'),
          ],
          [
            'name' => $translator->translatePlural('Notification', 'Notifications', 2) . ' - ' .
                      $translator->translatePlural('Notification', 'Notifications', 2),
            'link' => $basePath . '/view/notifications',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/notifications' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Notification'),
          ],
          [
            'name' => $translator->translatePlural('Service level', 'Service levels', 2),
            'link' => $basePath . '/view/slms',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/slms' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Slm'),
          ],
          [
            'name' => $translator->translate('Fields unicity'),
            'link' => $basePath . '/view/fieldunicities',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/fieldunicities' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Fieldunicity'),
          ],
          [
            'name' => $translator->translatePlural('Automatic action', 'Automatic actions', 2),
            'link' => $basePath . '/view/crontasks',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/crontasks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Crontask'),
          ],
          [
            'name' => $translator->translatePlural('External link', 'External links', 2),
            'link' => $basePath . '/view/links',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/links' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Link'),
          ],
          [
            'name' => "#" . $translator->translatePlural('Receiver', 'Receivers', 2),
            'link' => $basePath . '/view/mailcollectors',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/mailcollectors' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Mailcollector'),
          ],
        ],
        'dropdown' => [
          [
            'name' => $translator->translatePlural('Location', 'Locations', 2),
            'link' => $basePath . '/view/dropdowns/locations',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/locations' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Location'),
          ],
          [
            'name' => $translator->translatePlural('Status of items', 'Statuses of items', 2),
            'link' => $basePath . '/view/dropdowns/states',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/states' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\State'),
          ],
          [
            'name' => $translator->translatePlural('Manufacturer', 'Manufacturers', 2),
            'link' => $basePath . '/view/dropdowns/manufacturers',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/manufacturers' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Manufacturer'),
          ],
          [
            'name' => $translator->translatePlural('Blacklist', 'Blacklists', 2),
            'link' => $basePath . '/view/dropdowns/blacklists',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/blacklists' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Blacklist'),
          ],
          [
            'name' => $translator->translate('Blacklisted mail content'),
            'link' => $basePath . '/view/dropdowns/blacklistedmailcontents',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/blacklistedmailcontents' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Blacklistedmailcontent'),
          ],
          [
            'name' => $translator->translatePlural('ITIL category', 'ITIL categories', 2),
            'link' => $basePath . '/view/dropdowns/categories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/categories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Category'),
          ],
          [
            'name' => $translator->translatePlural('Ticket template', 'Ticket templates', 2),
            'link' => $basePath . '/view/dropdowns/ticketemplates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/ticketemplates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Tickettemplate'),
          ],
          // [
          //   'name' => $translator->translatePlural('Task category', 'Task categories', 2),
          //   'link' => $basePath . '/view/dropdowns/taskcategory',
          //   'icon' => 'edit',
          //   'class' => $activePath == $basePath . '/view/dropdowns/taskcategories' ? 'active blue' : '',
          //   'display' => $this->getRightForModel('\App\Models\Taskcategories'),
          // ],
          // [
          //   'name' => $translator->translatePlural('Task template', 'Task templates', 2),
          //   'link' => $basePath . '/view/dropdowns/tasktemplates',
          //   'icon' => 'edit',
          //   'class' => $activePath == $basePath . '/view/dropdowns/tasktemplates' ? 'active blue' : '',
          //   'display' => $this->getRightForModel('\App\Models\Tasktemplate'),
          // ],
          [
            'name' => $translator->translatePlural('Solution type', 'Solution types', 2),
            'link' => $basePath . '/view/dropdowns/solutiontypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/solutiontypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Solutiontype'),
          ],
          [
            'name' => $translator->translatePlural('Solution template', 'Solution templates', 2),
            'link' => $basePath . '/view/dropdowns/solutiontemplates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/solutiontemplates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Solutiontemplate'),
          ],
          [
            'name' => $translator->translatePlural('Request source', 'Request sources', 2),
            'link' => $basePath . '/view/dropdowns/requesttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/requesttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Requesttype'),
          ],
          [
            'name' => $translator->translatePlural('Followup template', 'Followup templates', 2),
            'link' => $basePath . '/view/dropdowns/followuptemplates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/followuptemplates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Followuptemplate'),
          ],
          [
            'name' => $translator->translatePlural('Project state', 'Project states', 2),
            'link' => $basePath . '/view/dropdowns/projectstates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/projectstates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Projectstate'),
          ],
          [
            'name' => $translator->translatePlural('Project type', 'Project types', 2),
            'link' => $basePath . '/view/dropdowns/projecttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/projecttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Projecttype'),
          ],
          [
            'name' => $translator->translatePlural('Project task', 'Project tasks', 2),
            'link' => $basePath . '/view/dropdowns/projecttasks',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/projecttasks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Projecttask'),
          ],
          [
            'name' => $translator->translatePlural('Project tasks type', 'Project tasks types', 2),
            'link' => $basePath . '/view/dropdowns/projecttasktypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/projecttasktypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Projecttasktype'),
          ],
          [
            'name' => $translator->translatePlural('Project task template', 'Project task templates', 2),
            'link' => $basePath . '/view/dropdowns/projecttasktemplates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/projecttasktemplates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Projecttasktemplate'),
          ],
          [
            'name' => $translator->translatePlural('Event category', 'Event categories', 2),
            'link' => $basePath . '/view/dropdowns/planningeventcategories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/planningeventcategories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Planningeventcategory'),
          ],
          [
            'name' => $translator->translatePlural('External events template', 'External events templates', 2),
            'link' => $basePath . '/view/dropdowns/planningexternaleventtemplates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/planningexternaleventtemplates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Planningexternaleventtemplate'),
          ],
          [
            'name' => $translator->translatePlural('Computer type', 'Computer types', 2),
            'link' => $basePath . '/view/dropdowns/computertypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/computertypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Computertype'),
          ],
          [
            'name' => $translator->translatePlural('Networking equipment type', 'Networking equipment types', 2),
            'link' => $basePath . '/view/dropdowns/networkequipmenttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/networkequipmenttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Networkequipmenttype'),
          ],
          [
            'name' => $translator->translatePlural('Printer type', 'Printer types', 2),
            'link' => $basePath . '/view/dropdowns/printertypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/printertypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Printertype'),
          ],
          [
            'name' => $translator->translatePlural('Monitor type', 'Monitor types', 2),
            'link' => $basePath . '/view/dropdowns/monitortypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/monitortypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Monitortype'),
          ],
          [
            'name' => $translator->translatePlural('Peripheral type', 'Peripheral types', 2),
            'link' => $basePath . '/view/dropdowns/peripheraltypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/peripheraltypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Peripheraltype'),
          ],
          [
            'name' => $translator->translatePlural('Phone type', 'Phone types', 2),
            'link' => $basePath . '/view/dropdowns/phonetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/phonetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Phonetype'),
          ],
          [
            'name' => $translator->translatePlural('License type', 'License types', 2),
            'link' => $basePath . '/view/dropdowns/softwarelicensetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/softwarelicensetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Softwarelicensetype'),
          ],
          [
            'name' => $translator->translatePlural('Cartridge type', 'Cartridge types', 2),
            'link' => $basePath . '/view/dropdowns/cartridgeitemtypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/cartridgeitemtypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Cartridgeitemtype'),
          ],
          [
            'name' => $translator->translatePlural('Consumable type', 'Consumable types', 2),
            'link' => $basePath . '/view/dropdowns/consumableitemtypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/consumableitemtypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Consumableitemtype'),
          ],
          [
            'name' => $translator->translatePlural('Contract type', 'Contract types', 2),
            'link' => $basePath . '/view/dropdowns/contracttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/contracttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Contracttype'),
          ],
          [
            'name' => $translator->translatePlural('Contact type', 'Contact types', 2),
            'link' => $basePath . '/view/dropdowns/contacttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/contacttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Contacttype'),
          ],
          [
            'name' => $translator->translatePlural('Generic type', 'Generic types', 2),
            'link' => $basePath . '/view/dropdowns/devicegenerictype',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicegenerictype' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicegenerictype'),
          ],
          [
            'name' => $translator->translatePlural('Sensor type', 'Sensor types', 2),
            'link' => $basePath . '/view/dropdowns/devicesensortype',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicesensortype' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesensortype'),
          ],
          [
            'name' => $translator->translatePlural('Memory type', 'Memory types', 2),
            'link' => $basePath . '/view/dropdowns/devicememorytype',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicememorytype' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicememorytype'),
          ],
          [
            'name' => $translator->translatePlural('Third party type', 'Third party types', 2),
            'link' => $basePath . '/view/dropdowns/suppliertypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/suppliertypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Suppliertype'),
          ],
          [
            'name' => $translator->translatePlural(
              'Interface type (Hard drive...)',
              'Interface types (Hard drive...)',
              2
            ),
            'link' => $basePath . '/view/dropdowns/interfacetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/interfacetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Interfacetype'),
          ],
          [
            'name' => $translator->translatePlural('Case type', 'Case types', 2),
            'link' => $basePath . '/view/dropdowns/devicecasetype',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicecasetype' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicecasetype'),
          ],
          [
            'name' => $translator->translatePlural('Phone power supply type', 'Phone power supply types', 2),
            'link' => $basePath . '/view/dropdowns/phonepowersupplies',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/phonepowersupplies' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Phonepowersupply'),
          ],
          [
            'name' => $translator->translatePlural('File system', 'File systems', 2),
            'link' => $basePath . '/view/dropdowns/filesystems',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/filesystems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Filesystem'),
          ],
          [
            'name' => $translator->translatePlural('Certificate type', 'Certificate types', 2),
            'link' => $basePath . '/view/dropdowns/certificatetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/certificatetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Certificatetype'),
          ],
          [
            'name' => $translator->translatePlural('Budget type', 'Budget types', 2),
            'link' => $basePath . '/view/dropdowns/budgettypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/budgettypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Budgettype'),
          ],
          [
            'name' => $translator->translatePlural('Simcard type', 'Simcard types', 2),
            'link' => $basePath . '/view/dropdowns/devicesimcardtypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicesimcardtypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesimcardtype'),
          ],
          [
            'name' => $translator->translatePlural('Line type', 'Line types', 2),
            'link' => $basePath . '/view/dropdowns/linetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/linetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Linetype'),
          ],
          [
            'name' => $translator->translatePlural('Rack type', 'Rack types', 2),
            'link' => $basePath . '/view/dropdowns/racktypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/racktypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Racktype'),
          ],
          [
            'name' => $translator->translatePlural('PDU type', 'PDU types', 2),
            'link' => $basePath . '/view/dropdowns/pdutypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/pdutypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Pdutype'),
          ],
          [
            'name' => $translator->translatePlural('Passive device type', 'Passive device types', 2),
            'link' => $basePath . '/view/dropdowns/passivedcequipmenttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/passivedcequipmenttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Passivedcequipmenttype'),
          ],
          [
            'name' => $translator->translatePlural('Cluster type', 'Cluster types', 2),
            'link' => $basePath . '/view/dropdowns/clustertypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/clustertypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Clustertype'),
          ],
          [
            'name' => $translator->translatePlural('Computer model', 'Computer models', 2),
            'link' => $basePath . '/view/dropdowns/computermodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/computermodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Computermodel'),
          ],
          [
            'name' => $translator->translatePlural('Networking equipment model', 'Networking equipment models', 2),
            'link' => $basePath . '/view/dropdowns/networkequipmentmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/networkequipmentmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Networkequipmentmodel'),
          ],
          [
            'name' => $translator->translatePlural('Printer model', 'Printer models', 2),
            'link' => $basePath . '/view/dropdowns/printermodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/printermodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Printermodel'),
          ],
          [
            'name' => $translator->translatePlural('Monitor model', 'Monitor models', 2),
            'link' => $basePath . '/view/dropdowns/monitormodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/monitormodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Monitormodel'),
          ],
          [
            'name' => $translator->translatePlural('Peripheral model', 'Peripheral models', 2),
            'link' => $basePath . '/view/dropdowns/peripheralmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/peripheralmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Peripheralmodel'),
          ],
          [
            'name' => $translator->translatePlural('Phone model', 'Phone models', 2),
            'link' => $basePath . '/view/dropdowns/phonemodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/phonemodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Phonemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device case model', 'Device case models', 2),
            'link' => $basePath . '/view/dropdowns/devicecasemodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicecasemodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicecasemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device control model', 'Device control models', 2),
            'link' => $basePath . '/view/dropdowns/devicecontrolmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicecontrolmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicecontrolmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device drive model', 'Device drive models', 2),
            'link' => $basePath . '/view/dropdowns/devicedrivemodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicedrivemodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicedrivemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device generic model', 'Device generic models', 2),
            'link' => $basePath . '/view/dropdowns/devicegenericmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicegenericmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicegenericmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device graphic card model', 'Device graphic card models', 2),
            'link' => $basePath . '/view/dropdowns/devicegraphiccardmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicegraphiccardmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicegraphiccardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device hard drive model', 'Device hard drive models', 2),
            'link' => $basePath . '/view/dropdowns/deviceharddrivemodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/deviceharddrivemodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Deviceharddrivemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device memory model', 'Device memory models', 2),
            'link' => $basePath . '/view/dropdowns/devicememorymodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicememorymodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicememorymodel'),
          ],
          [
            'name' => $translator->translatePlural('System board model', 'System board models', 2),
            'link' => $basePath . '/view/dropdowns/devicemotherboardmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicemotherboardmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicemotherboardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Network card model', 'Network card models', 2),
            'link' => $basePath . '/view/dropdowns/devicenetworkcardmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicenetworkcardmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicenetworkcardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Other component model', 'Other component models', 2),
            'link' => $basePath . '/view/dropdowns/devicepcimodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicepcimodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicepcimodel'),
          ],
          [
            'name' => $translator->translatePlural('Device power supply model', 'Device power supply models', 2),
            'link' => $basePath . '/view/dropdowns/devicepowersupplymodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicepowersupplymodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicepowersupplymodel'),
          ],
          [
            'name' => $translator->translatePlural('Device processor model', 'Device processor models', 2),
            'link' => $basePath . '/view/dropdowns/deviceprocessormodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/deviceprocessormodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Deviceprocessormodel'),
          ],
          [
            'name' => $translator->translatePlural('Device sound card model', 'Device sound card models', 2),
            'link' => $basePath . '/view/dropdowns/devicesoundcardmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicesoundcardmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesoundcardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device sensor model', 'Device sensor models', 2),
            'link' => $basePath . '/view/dropdowns/devicesensormodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/devicesensormodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesensormodel'),
          ],
          [
            'name' => $translator->translatePlural('Rack model', 'Rack models', 2),
            'link' => $basePath . '/view/dropdowns/rackmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/rackmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Rackmodel'),
          ],
          [
            'name' => $translator->translatePlural('Enclosure model', 'Enclosure models', 2),
            'link' => $basePath . '/view/dropdowns/enclosuremodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/enclosuremodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Enclosuremodel'),
          ],
          [
            'name' => $translator->translatePlural('PDU model', 'PDU models', 2),
            'link' => $basePath . '/view/dropdowns/pdumodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/pdumodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Pdumodel'),
          ],
          [
            'name' => $translator->translatePlural('Passive device model', 'Passive device models', 2),
            'link' => $basePath . '/view/dropdowns/passivedcequipmentmodels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/passivedcequipmentmodels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Passivedcequipmentmodel'),
          ],
          [
            'name' => $translator->translatePlural('Virtualization system', 'Virtualization systems', 2),
            'link' => $basePath . '/view/dropdowns/virtualmachinetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/virtualmachinetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Virtualmachinetype'),
          ],
          [
            'name' => $translator->translatePlural('Virtualization model', 'Virtualization models', 2),
            'link' => $basePath . '/view/dropdowns/virtualmachinesystems',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/virtualmachinesystems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Virtualmachinesystem'),
          ],
          [
            'name' => $translator->translatePlural('State of the virtual machine', 'States of the virtual machine', 2),
            'link' => $basePath . '/view/dropdowns/virtualmachinestates',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/virtualmachinestates' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Virtualmachinestate'),
          ],
          [
            'name' => $translator->translatePlural('Document heading', 'Document headings', 2),
            'link' => $basePath . '/view/dropdowns/documentcategories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/documentcategories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Documentcategory'),
          ],
          [
            'name' => $translator->translatePlural('Document type', 'Document types', 2),
            'link' => $basePath . '/view/dropdowns/documenttypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/documenttypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Documenttype'),
          ],
          [
            'name' => $translator->translatePlural('Business criticity', 'Business criticities', 2),
            'link' => $basePath . '/view/dropdowns/businesscriticities',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/businesscriticities' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Businesscriticity'),
          ],
          [
            'name' => $translator->translatePlural('Knowledge base category', 'Knowledge base categories', 2),
            'link' => $basePath . '/view/dropdowns/knowbaseitemcategories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/knowbaseitemcategories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Knowbaseitemcategory'),
          ],
          [
            'name' => $translator->translatePlural('Calendar', 'Calendars', 2),
            'link' => $basePath . '/view/dropdowns/calendars',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/calendars' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Calendar'),
          ],
          [
            'name' => $translator->translatePlural('Close time', 'Close times', 2),
            'link' => $basePath . '/view/dropdowns/holidays',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/holidays' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Holiday'),
          ],
          [
            'name' => $translator->translatePlural('Operating system', 'Operating systems', 2),
            'link' => $basePath . '/view/dropdowns/operatingsystems',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystem'),
          ],
          [
            'name' => $translator->translatePlural(
              'Version of the operating system',
              'Versions of the operating systems',
              2
            ),
            'link' => $basePath . '/view/dropdowns/operatingsystemversions',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystemversions' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemversion'),
          ],
          [
            'name' => $translator->translatePlural('Service pack', 'Service packs', 2),
            'link' => $basePath . '/view/dropdowns/operatingsystemservicepacks',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystemservicepacks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemservicepack'),
          ],
          [
            'name' => $translator->translatePlural(
              'Operating system architecture',
              'Operating system architectures',
              2
            ),
            'link' => $basePath . '/view/dropdowns/operatingsystemarchitectures',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystemarchitectures' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemarchitecture'),
          ],
          [
            'name' => $translator->translatePlural('Edition', 'Editions', 2),
            'link' => $basePath . '/view/dropdowns/operatingsystemeditions',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystemeditions' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemedition'),
          ],
          [
            'name' => $translator->translatePlural('Kernel', 'Kernels', 2),
            'link' => $basePath . '/view/dropdowns/operatingsystemkernels',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystemkernels' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemkernel'),
          ],
          [
            'name' => $translator->translatePlural('Kernel version', 'Kernel versions', 2),
            'link' => $basePath . '/view/dropdowns/operatingsystemkernelversions',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/operatingsystemkernelversions' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemkernelversion'),
          ],
          [
            'name' => $translator->translatePlural('Update Source', 'Update Sources', 2),
            'link' => $basePath . '/view/dropdowns/autoupdatesystems',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/autoupdatesystems' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Autoupdatesystem'),
          ],
          [
            'name' => $translator->translatePlural('Network interface', 'Network interfaces', 2),
            'link' => $basePath . '/view/dropdowns/networkinterfaces',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/networkinterfaces' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Networkinterface'),
          ],
          [
            'name' => $translator->translatePlural('Network outlet', 'Network outlets', 2),
            'link' => $basePath . '/view/dropdowns/netpoints',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/netpoints' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Netpoint'),
          ],
          [
            'name' => $translator->translatePlural('Network', 'Networks', 2),
            'link' => $basePath . '/view/dropdowns/networks',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/networks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Network'),
          ],
          [
            'name' => $translator->translatePlural('VLAN', 'VLANs', 2),
            'link' => $basePath . '/view/dropdowns/vlans',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/vlans' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Vlan'),
          ],
          [
            'name' => $translator->translatePlural('Line operator', 'Line operators', 2),
            'link' => $basePath . '/view/dropdowns/lineoperators',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/lineoperators' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Lineoperator'),
          ],
          [
            'name' => $translator->translatePlural('Domain type', 'Domain types', 2),
            'link' => $basePath . '/view/dropdowns/domaintypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/domaintypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Domaintype'),
          ],
          [
            'name' => $translator->translatePlural('Domain relation', 'Domains relations', 2),
            'link' => $basePath . '/view/dropdowns/domainrelations',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/domainrelations' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Domainrelation'),
          ],
          [
            'name' => $translator->translatePlural('Record type', 'Records types', 2),
            'link' => $basePath . '/view/dropdowns/domainrecordtypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/domainrecordtypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Domainrecordtype'),
          ],
          [
            'name' => $translator->translatePlural('IP network', 'IP networks', 2),
            'link' => $basePath . '/view/dropdowns/ipnetworks',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/ipnetworks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Ipnetwork'),
          ],
          [
            'name' => $translator->translatePlural('Internet domain', 'Internet domains', 2),
            'link' => $basePath . '/view/dropdowns/fqdns',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/fqdns' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Fqdn'),
          ],
          [
            'name' => $translator->translatePlural('Wifi network', 'Wifi networks', 2),
            'link' => $basePath . '/view/dropdowns/wifinetworks',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/wifinetworks' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Wifinetwork'),
          ],
          [
            'name' => $translator->translatePlural('Network name', 'Network names', 2),
            'link' => $basePath . '/view/dropdowns/networknames',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/networknames' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Networkname'),
          ],
          [
            'name' => $translator->translatePlural('Software category', 'Software categories', 2),
            'link' => $basePath . '/view/dropdowns/softwarecategories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/softwarecategories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Softwarecategory'),
          ],
          [
            'name' => $translator->translatePlural('User title', 'Users titles', 2),
            'link' => $basePath . '/view/dropdowns/usertitles',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/usertitles' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Usertitle'),
          ],
          [
            'name' => $translator->translatePlural('User category', 'User categories', 2),
            'link' => $basePath . '/view/dropdowns/usercategories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/usercategories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Usercategory'),
          ],
          [
            'name' => $translator->translatePlural('LDAP criterion', 'LDAP criteria', 2),
            'link' => $basePath . '/view/dropdowns/rulerightparameters',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/rulerightparameters' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Rulerightparameter'),
          ],
          [
            'name' => $translator->translatePlural(
              'Ignored value for the unicity',
              'Ignored values for the unicity',
              2
            ),
            'link' => $basePath . '/view/dropdowns/fieldblacklists',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/fieldblacklists' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Fieldblacklist'),
          ],
          [
            'name' => $translator->translatePlural(
              'Field storage of the login in the HTTP request',
              'Fields storage of the login in the HTTP request',
              2
            ),
            'link' => $basePath . '/view/dropdowns/ssovariables',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/ssovariables' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Ssovariable'),
          ],
          [
            'name' => $translator->translatePlural('Plug', 'Plugs', 2),
            'link' => $basePath . '/view/dropdowns/plugs',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/plugs' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Plug'),
          ],
          [
            'name' => $translator->translatePlural('Appliance type', 'Appliance types', 2),
            'link' => $basePath . '/view/dropdowns/appliancetypes',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/appliancetypes' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Appliancetype'),
          ],
          [
            'name' => $translator->translatePlural('Appliance environment', 'Appliance environments', 2),
            'link' => $basePath . '/view/dropdowns/applianceenvironments',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/applianceenvironments' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Applianceenvironment'),
          ],
          [
            'name' => $translator->translatePlural('Oauth IMAP application', 'Oauth IMAP applications', 2),
            'link' => $basePath . '/view/dropdowns/oauthimapapplications',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/dropdowns/oauthimapapplications' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\OauthimapApplication'),
          ],
        ],
        'component' => [
          [
            'name' => $translator->translatePlural('Power supply', 'Power supplies', 2),
            'link' => $basePath . '/view/devices/devicepowersupplies',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicepowersupplies' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicepowersupply'),
          ],
          [
            'name' => $translator->translatePlural('Battery', 'Batteries', 2),
            'link' => $basePath . '/view/devices/devicebatteries',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicebatteries' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicebattery'),
          ],
          [
            'name' => $translator->translatePlural('Case', 'Cases', 2),
            'link' => $basePath . '/view/devices/devicecases',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicecases' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicecase'),
          ],
          [
            'name' => $translator->translatePlural('Sensor', 'Sensors', 2),
            'link' => $basePath . '/view/devices/devicesensors',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicesensors' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesensor'),
          ],
          [
            'name' => $translator->translatePlural('Simcard', 'Simcards', 2),
            'link' => $basePath . '/view/devices/devicesimcards',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicesimcards' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesimcard'),
          ],
          [
            'name' => $translator->translatePlural('Graphics card', 'Graphics cards', 2),
            'link' => $basePath . '/view/devices/devicegraphiccards',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicegraphiccards' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicegraphiccard'),
          ],
          [
            'name' => $translator->translatePlural('System board', 'System boards', 2),
            'link' => $basePath . '/view/devices/devicemotherboards',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicemotherboards' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicemotherboard'),
          ],
          [
            'name' => $translator->translatePlural('Network card', 'Network cards', 2),
            'link' => $basePath . '/view/devices/devicenetworkcards',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicenetworkcards' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicenetworkcard'),
          ],
          [
            'name' => $translator->translatePlural('Soundcard', 'Soundcards', 2),
            'link' => $basePath . '/view/devices/devicesoundcards',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicesoundcards' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicesoundcard'),
          ],
          [
            'name' => $translator->translatePlural('Generic device', 'Generic devices', 2),
            'link' => $basePath . '/view/devices/devicegenerics',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicegenerics' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicegeneric'),
          ],
          [
            'name' => $translator->translatePlural('Controller', 'Controllers', 2),
            'link' => $basePath . '/view/devices/devicecontrols',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicecontrols' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicecontrol'),
          ],
          [
            'name' => $translator->translatePlural('Hard drive', 'Hard drives', 2),
            'link' => $basePath . '/view/devices/deviceharddrives',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/deviceharddrives' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Deviceharddrive'),
          ],
          [
            'name' => $translator->translatePlural('Firmware', 'Firmware', 2),
            'link' => $basePath . '/view/devices/devicefirmwares',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicefirmwares' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicefirmware'),
          ],
          [
            'name' => $translator->translatePlural('Drive', 'Drives', 2),
            'link' => $basePath . '/view/devices/devicedrives',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicedrives' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicedrive'),
          ],
          [
            'name' => $translator->translatePlural('Memory', 'Memory', 2),
            'link' => $basePath . '/view/devices/devicememories',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicememories' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicememory'),
          ],
          [
            'name' => $translator->translatePlural('Processor', 'Processors', 2),
            'link' => $basePath . '/view/devices/deviceprocessors',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/deviceprocessors' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Deviceprocessor'),
          ],
          [
            'name' => $translator->translatePlural('PCI device', 'PCI devices', 2),
            'link' => $basePath . '/view/devices/devicepcis',
            'icon' => 'edit',
            'class' => $activePath == $basePath . '/view/devices/devicepcis' ? 'active blue' : '',
            'display' => $this->getRightForModel('\App\Models\Devicepci'),
          ],
        ],
      ],
    ];
  }

  private function loadRights()
  {
    $profile = \App\Models\Profile::find($GLOBALS['profile_id']);
    if (!is_null($profile))
    {
      $dbRights = \App\Models\Profileright::where('profile_id', $profile->id)->get();
      foreach ($dbRights as $dbRight)
      {
        if ($dbRight->read || $dbRight->readmyitems || $dbRight->readmygroupitems)
        {
          $this->setRightForModel($dbRight->model);
        }
      }
    }
  }

  private function setRightForModel($model)
  {
    $this->rights['\\' . $model] = true;
  }

  private function getRightForModel($modelName)
  {
    if (isset($this->rights[$modelName]))
    {
      return true;
    }
    return false;
  }

  private function cleanMenuByDisplay($menu)
  {
    $newMenu = [];
    foreach ($menu as $item)
    {
      $submenu = [];
      $more = [];
      $dropdown = [];
      $component = [];
      foreach ($item['sub'] as $key => $subitem)
      {
        if (isset($subitem['display']) && $subitem['display'])
        {
          if (count($submenu) >= 6)
          {
            $more[] = $subitem;
          }
          else
          {
            $submenu[] = $subitem;
          }
        }
      }
      if (isset($item['dropdown']))
      {
        foreach ($item['dropdown'] as $key => $subitem)
        {
          if (isset($subitem['display']) && $subitem['display'])
          {
            $dropdown[] = $subitem;
          }
        }
      }
      if (isset($item['component']))
      {
        foreach ($item['component'] as $key => $subitem)
        {
          if (isset($subitem['display']) && $subitem['display'])
          {
            $component[] = $subitem;
          }
        }
      }


      if (count($submenu) > 0 || count($dropdown) > 0 || count($component))
      {
        $catMenu = [
          'name' => $item['name'],
          'icon' => $item['icon'],
          'sub'  => $submenu,
        ];
        if (count($more) > 0)
        {
          $catMenu['more'] = $more;
        }
        if (count($dropdown) > 0)
        {
          $catMenu['dropdown'] = $dropdown;
        }
        if (count($component) > 0)
        {
          $catMenu['component'] = $component;
        }
        $newMenu[] = $catMenu;
      }
    }
    return $newMenu;
  }
}

// ITAM
//   Hardware inventory
//      Ordinateurs
//      Moniteurs
//      Périphériques
//      Imprimantes
//      Cartouches
//      Consommables
//      Téléphones
//      Baies
//      Châssis
//      PDU
//      Équipements passifs
//      Cartes SIM
//   User data
//   Administrative data
//      Documents
//   Contract & cost management
//      Licences
//      Contrats
//      Lignes
//      Certificats
//   Software inventory
//      Logiciels
//      Systèmes d'exploitation
//      Applicatifs
//   Network inventory
//      Matériels réseau

// ITSM




// Service strategie
//      Budgets
//      Tickets demande

// Service design
//      Niveaux de services
//      Fournisseurs
//      Contacts

// Service transaction
//      Changements
//      Assets
//      Base de connaissances

// Service operations
//      Service desk
//      Tickets incidents
//      Problèmes




//////////////////// OLD //////////////////

// Assistance
//      Créer un ticket
//      Planning
//      Statistiques
//      Tickets récurrents
//      Formulaires

// Gestion
//      Data centers
//      Clusters
//      Domaines

// Outils
//      Projets
//      Notes
//      Flux RSS
//      Réservations
//      Rapports
//      Recherches sauvegardées
//      Data Injection
//      Alertes

// Administration
//      Utilisateurs
//      Groupes
//      Entités
//      Règles
//      Dictionnaires
//      Profils
//      File d'attente des notifications
//      Journaux
//      FusionInventory
//      Formulaires

// Configuration
//      Intitulés
//      Composants
//      Notifications
//      Générale
//      Unicité des champs
//      Actions automatiques
//      Authentification
//      Collecteurs
//      Liens externes
//      Plugins
//      Notifications generation
//      Tasks Workflows
//      Applications Oauth IMAP
