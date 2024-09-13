<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowbaseItemCategory extends Common
{
  protected $table = 'glpi_knowbaseitemcategories';
  protected $definition = '\App\Models\Definitions\KnowbaseItemCategory';
  protected $titles = ['Knowledge base category', 'Knowledge base categories'];
  protected $icon = 'edit';

  protected $appends = [
    'category',
  ];

  protected $visible = [
    'category',
  ];

  protected $with = [
    'category:id,name',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\KnowbaseItemCategory', 'knowbaseitemcategories_id');
  }

}
