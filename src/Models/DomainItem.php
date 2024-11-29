<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DomainItem extends Common
{
  protected $definition = '\App\Models\Definitions\DomainItem';
  protected $titles = ['DomainItem', 'DomainItems'];
  protected $icon = 'globe americas';

  protected $table = 'domain_item';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
    'domain:id,name',
    'relation',
  ];

  public function domain(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Domain');
  }

  public function relation(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Domainrelation', 'domainrelation_id');
  }
}
