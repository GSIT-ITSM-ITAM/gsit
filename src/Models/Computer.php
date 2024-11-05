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
    'uuid',
    'entity',
    'certificates',
    'domains',
    'appliances',
    'notes',
    'knowbaseitems',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'network:id,name',
    'groupstech:id,name',
    'userstech:id,name',
    'user:id,name',
    'group:id,name',
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
    'entity:id,name',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowbaseitems:id,name',

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
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
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
    );
  }

  public function virtualization(): HasMany
  {
    return $this->hasMany('App\Models\Computervirtualmachine');
  }

  public function volumes(): HasMany
  {
    return $this->hasMany('App\Models\Itemdisk', 'item_id');
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
}
