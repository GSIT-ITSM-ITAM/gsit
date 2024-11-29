<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ipnetwork extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Ipnetwork';
  protected $titles = ['IP network', 'IP networks'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'vlans',
  ];

  protected $visible = [
    'entity',
    'vlans',
  ];

  protected $with = [
    'entity:id,name,completename',
    'vlans',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function vlans(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Vlan', 'ipnetworks_vlans', 'ipnetwork_id', 'vlan_id');
  }
}
