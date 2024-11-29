<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickettemplate extends Common
{
  protected $definition = '\App\Models\Definitions\Tickettemplate';
  protected $titles = ['Ticket template', 'Ticket templates'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'mandatoryfields',
    'predefinedfields',
    'hiddenfields',
  ];

  protected $visible = [
    'entity',
    'mandatoryfields',
    'predefinedfields',
    'hiddenfields',
  ];

  protected $with = [
    'entity:id,name,completename',
    'mandatoryfields:id,num',
    'predefinedfields:id,num,value',
    'hiddenfields:id,num',
  ];

  protected $fillable = [
    'name',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function mandatoryfields(): HasMany
  {
    return $this->hasMany('\App\Models\Tickettemplatemandatoryfields', 'tickettemplate_id');
  }

  public function predefinedfields(): HasMany
  {
    return $this->hasMany('\App\Models\Tickettemplatepredefinedfields', 'tickettemplate_id');
  }

  public function hiddenfields(): HasMany
  {
    return $this->hasMany('\App\Models\Tickettemplatehiddenfields', 'tickettemplate_id');
  }
}
