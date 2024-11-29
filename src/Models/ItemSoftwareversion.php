<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ItemSoftwareversion extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\ItemSoftwareversion';
  protected $titles = ['Softwareversion Item', 'Softwareversion Items'];
  protected $icon = 'edit';
  protected $table = 'item_softwareversion';

  protected $appends = [
    'entity',
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
