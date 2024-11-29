<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Domainrelation extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Domainrelation';
  protected $titles = ['Domain relation', 'Domains relations'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'domains',
  ];

  protected $visible = [
    'entity',
    'domains',
  ];

  protected $with = [
    'entity:id,name,completename',
    'domains',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function domains(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Domain', 'domain_item', 'domainrelation_id', 'domain_id');
  }
}
