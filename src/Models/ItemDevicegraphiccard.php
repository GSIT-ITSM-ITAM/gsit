<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ItemDevicegraphiccard extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\ItemDevicegraphiccard';
  protected $titles = ['Devicegraphiccard Item', 'Devicegraphiccard Items'];
  protected $icon = 'edit';
  protected $table = 'item_devicegraphiccard';

  protected $appends = [
    'entity',
    'documents',
  ];

  protected $visible = [
    'entity',
    'documents',
  ];

  protected $with = [
    'entity:id,name,completename',
    'documents',
  ];

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
}
