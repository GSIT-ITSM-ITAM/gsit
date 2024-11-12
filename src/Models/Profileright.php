<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profileright extends Common
{
  protected $definition = '\App\Models\Definitions\Profileright';
  protected $titles = ['Right', 'Rights'];
  protected $icon = 'user check';
  protected $hasEntityField = false;

  // For default values
  protected $attributes = [
    'read'        => false,
    'create'      => false,
    'update'      => false,
    'softdelete'  => false,
    'delete'      => false,
    'custom'      => false,
    'readmyitems' => false,
    'readmygroupitems' => false,
    'readprivateitems' => false,
    'canassign'   => false,
  ];

  protected $fillable = [
    'model',
    'profile_id',
    'rights',
    'read',
    'create',
    'update',
    'softdelete',
    'delete',
    'custom',
    'readmyitems',
    'readmygroupitems',
    'readprivateitems',
    'canassign',
  ];
}
