<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Softwareversion extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Softwareversion';
  protected $titles = ['Software version', 'Software versions'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'software_id',
    'entity',
  ];

  protected $fillable = [
    'name',
    'entity_id',
    'software_id',
    'entity',
  ];

  protected $with = [
    'software:id,name',
    'entity:id,name,completename',
    'state:id,name',
    'operatingsystem:id,name',
  ];


  // We get all devices
  public function devices()
  {
    return $this->belongsToMany('\App\Models\Computer', 'item_softwareversion', 'softwareversion_id', 'item_id');
  }

  public function software(): BelongsTo
  {
    return $this->belongsTo('App\Models\Software');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
  }

  public function operatingsystem(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Operatingsystem', 'operatingsystem_id');
  }
}
