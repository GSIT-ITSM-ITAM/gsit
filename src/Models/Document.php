<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Document extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Document';
  protected $titles = ['Document', 'Documents'];
  protected $icon = 'file';

  protected $appends = [
    'categorie',
    'entity',
    'notes',
  ];

  protected $visible = [
    'categorie',
    'entity',
    'notes',
    'documents',
  ];

  protected $with = [
    'categorie:id,name',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
  ];

  public function categorie(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Documentcategory', 'documentcategory_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
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
    );
  }
}
