<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Computer extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Computer';
  protected $titles = ['Computer', 'Computers'];
  protected $icon = 'laptop';

  protected $appends = [
    // 'type',
    // 'model',
    // 'state',
    // 'manufacturer',
    // 'network',
    // 'groupstech',
    // 'userstech',
    // 'user',
    // 'group',
    // 'location',
    // 'autoupdatesystem',
    'entity',
    'infocom',
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'network',
    'groupstech',
    'userstech',
    'user',
    'group',
    'location',
    'autoupdatesystem',
    'softwareversions',
    'uuid',
    'entity',
    'certificates',
    'domains',
    'appliances',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'powersupplies',
    'sensors',
    'devicepcis',
    'devicegenerics',
    'devicenetworkcards',
    'devicesimcards',
    'devicemotherboards',
    'devicecases',
    'devicegraphiccards',
    'devicedrives',
    'infocom',
    'reservations',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'network:id,name',
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'location:id,name',
    'autoupdatesystem:id,name',
    'softwareversions:id,name',
    'operatingsystems:id,name',
    'memories:id,name',
    'firmwares:id,name',
    'processors:id,name',
    'harddrives:id,name',
    'batteries:id,name',
    'soundcards:id,name',
    'controllers:id,name',
    'volumes:id,name',
    'virtualization:id,name',
    'certificates:id,name',
    'entity:id,name,completename',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'powersupplies:id,name',
    'sensors:id,name',
    'devicepcis:id,name',
    'devicegenerics:id,name',
    'devicenetworkcards:id,name',
    'devicesimcards:id,name',
    'devicemotherboards:id,name',
    'devicecases:id,name',
    'devicegraphiccards:id,name',
    'devicedrives:id,name',
    'infocom',
    'reservations',
  ];

  protected static function booted(): void
  {
    parent::boot();

    static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes)
    {
      if ($relationName == 'softwareversions')
      {
        $softwareversions = \App\Models\Softwareversion::with('software:name')->whereIn('id', $pivotIds)->get();
        foreach($softwareversions as $softwareversion)
        {
          $software = $softwareversion->software()->first();
          \App\v1\Controllers\Log::addEntry(
            $model,
            'version ' . $softwareversion->name . ' of software ' . $software->name . ' installed',
            $softwareversion->name . ' (' . $softwareversion->id . ')',
          );
        }
      }
    });
  }


  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Computertype', 'computertype_id');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Computermodel', 'computermodel_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function network(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Network');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function autoupdatesystem(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Autoupdatesystem');
  }

  public function softwareversions(): MorphToMany
  {
    return $this->morphToMany('\App\Models\Softwareversion', 'item', 'item_softwareversion');
  }

  public function operatingsystems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Operatingsystem',
      'item',
      'item_operatingsystem'
    )->withPivot(
      'operatingsystemversion_id',
      'operatingsystemservicepack_id',
      'operatingsystemarchitecture_id',
      'operatingsystemkernelversion_id',
      'operatingsystemedition_id',
      'license_number',
      'licenseid',
      'installationdate',
      'winowner',
      'wincompany',
      'oscomment',
      'hostid'
    );
  }

  public function memories(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicememory',
      'item',
      'item_devicememory'
    )->withPivot(
      'devicememory_id',
      'size',
      'serial',
      'busID',
      'location_id',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function firmwares(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicefirmware',
      'item',
      'item_devicefirmware'
    )->withPivot(
      'devicefirmware_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function processors(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Deviceprocessor',
      'item',
      'item_deviceprocessor'
    )->withPivot(
      'deviceprocessor_id',
      'frequency',
      'nbcores',
      'nbthreads',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function harddrives(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Deviceharddrive',
      'item',
      'item_deviceharddrive'
    )->withPivot(
      'deviceharddrive_id',
      'capacity',
      'serial',
      'location_id',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function batteries(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicebattery',
      'item',
      'item_devicebattery'
    )->withPivot(
      'devicebattery_id',
      'manufacturing_date',
      'serial',
      'location_id',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function soundcards(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicesoundcard',
      'item',
      'item_devicesoundcard'
    )->withPivot(
      'devicesoundcard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function controllers(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicecontrol',
      'item',
      'item_devicecontrol'
    )->withPivot(
      'devicecontrol_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function virtualization(): HasMany
  {
    return $this->hasMany('App\Models\Computervirtualmachine');
  }

  public function volumes(): HasMany
  {
    return $this->hasMany('App\Models\Itemdisk', 'item_id')->where('item_type', get_class($this));
  }

  public function certificates(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Certificate',
      'item',
      'certificate_item'
    )->withPivot(
      'certificate_id',
    );
  }

  public function antiviruses(): HasMany
  {
    return $this->hasMany('App\Models\Computerantivirus');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function domains(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Domain',
      'item',
      'domain_item'
    )->withPivot(
      'domainrelation_id',
    );
  }

  public function appliances(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Appliance',
      'item',
      'appliance_item'
    );
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
  }

  public function knowbaseitems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Knowbaseitem',
      'item',
      'knowbaseitem_item'
    )->withPivot(
      'knowbaseitem_id',
    );
  }

  public function documents(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Document',
      'item',
      'document_item'
    )->withPivot(
      'document_id',
      'updated_at',
    );
  }

  public function contracts(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Contract',
      'item',
      'contract_item'
    )->withPivot(
      'contract_id',
    );
  }

  public function tickets(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Ticket',
      'item',
      'item_ticket'
    )->withPivot(
      'ticket_id',
    );
  }

  public function problems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Problem',
      'item',
      'item_problem'
    )->withPivot(
      'problem_id',
    );
  }

  public function changes(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Change',
      'item',
      'change_item'
    )->withPivot(
      'change_id',
    );
  }

  public function powersupplies(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicepowersupply',
      'item',
      'item_devicepowersupply'
    )->withPivot(
      'devicepowersupply_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function sensors(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicesensor',
      'item',
      'item_devicesensor'
    )->withPivot(
      'devicesensor_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function devicepcis(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicepci',
      'item',
      'item_devicepci'
    )->withPivot(
      'devicepci_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function devicegenerics(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicegeneric',
      'item',
      'item_devicegeneric'
    )->withPivot(
      'devicegeneric_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function devicenetworkcards(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicenetworkcard',
      'item',
      'item_devicenetworkcard'
    )->withPivot(
      'devicenetworkcard_id',
      'location_id',
      'mac',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function devicesimcards(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicesimcard',
      'item',
      'item_devicesimcard'
    )->withPivot(
      'devicesimcard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'msin',
      'user_id',
      'group_id',
      'line_id',
      'id',
    );
  }

  public function devicemotherboards(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicemotherboard',
      'item',
      'item_devicemotherboard'
    )->withPivot(
      'devicemotherboard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function devicecases(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicecase',
      'item',
      'item_devicecase'
    )->withPivot(
      'devicecase_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  public function devicegraphiccards(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicegraphiccard',
      'item',
      'item_devicegraphiccard'
    )->withPivot(
      'devicegraphiccard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'memory',
      'id',
    );
  }

  public function devicedrives(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Devicedrive',
      'item',
      'item_devicedrive'
    )->withPivot(
      'devicedrive_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  public function infocom(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Infocom',
      'item',
    );
  }

  public function reservations(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Reservationitem',
      'item',
    );
  }
}
