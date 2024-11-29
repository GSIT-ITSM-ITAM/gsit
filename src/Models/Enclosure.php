<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Enclosure extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Enclosure';
  protected $titles = ['Enclosure', 'Enclosures'];
  protected $icon = 'th';

  protected $appends = [
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'items',
    'infocom',
  ];

  protected $visible = [
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'memories',
    'firmwares',
    'processors',
    'harddrives',
    'batteries',
    'soundcards',
    'controllers',
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
    'items',
    'infocom',
  ];

  protected $with = [
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'location:id,name',
    'entity:id,name,completename',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'memories:id,name',
    'firmwares:id,name',
    'processors:id,name',
    'harddrives:id,name',
    'batteries:id,name',
    'soundcards:id,name',
    'controllers:id,name',
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
    'items',
    'infocom',
  ];

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Enclosuremodel', 'enclosuremodel_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
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

  public function items(): HasMany
  {
    return $this->hasMany('\App\Models\ItemEnclosure', 'enclosure_id');
  }

  public function infocom(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Infocom',
      'item',
    );
  }
}
