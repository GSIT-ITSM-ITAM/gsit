<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class LinkItemtype extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\LinkItemtype';
  protected $titles = ['Link Itemtype', 'Link Itemtype'];
  protected $icon = 'virus slash';

  protected $table = 'link_itemtype';

  protected $appends = [
    'links',
  ];

  protected $visible = [
    'links',
  ];

  protected $with = [
    'links:id,name',
  ];

  public function links(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Link', 'link_id');
  }
}
