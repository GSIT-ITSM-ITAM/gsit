<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cartridge extends Common
{
  protected $definition = '\App\Models\Definitions\Cartridge';
  protected $titles = ['Cartridge', 'Cartridges'];
  protected $icon = 'fill drip';
  protected $hasEntityField = false;

  protected $appends = [
    'date_in',
    'date_use',
    'date_out',
    'pages',
  ];

  protected $visible = [
    'date_in',
    'date_use',
    'date_out',
    'pages',
    'cartridgeitems',
    'printer',
  ];

  protected $with = [
    'cartridgeitems:id,name,cartridgeitemtype_id',
    'printer',
  ];


  public function cartridgeitems(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Cartridgeitem', 'cartridgeitem_id');
  }
  public function printer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Printer', 'printer_id');
  }
}
