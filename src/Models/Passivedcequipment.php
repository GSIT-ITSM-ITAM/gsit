<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passivedcequipment extends Common
{
  protected $table = 'glpi_passivedcequipments';
  protected $definition = '\App\Models\Definitions\Passivedcequipment';
  protected $titles = ['Passive device', 'Passive devices'];
  protected $icon = 'th list';

  protected $appends = [
    'type',
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'groupstech:id,name',
    'userstech:id,name',
    'location:id,name',
  ];


  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Passivedcequipmenttype', 'passivedcequipmenttypes_id');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Passivedcequipmentmodel', 'passivedcequipmentmodels_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State', 'states_id');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer', 'manufacturers_id');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'groups_id_tech');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'users_id_tech');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location', 'locations_id');
  }

}
