<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\User';
  protected $titles = ['User', 'Users'];
  protected $icon = 'user';

  protected $appends = [
    // 'category',
    // 'title',
    // 'location',
    // 'profile',
    // 'supervisor',
    // 'group',
    'completename',
    'entity',
    'certificates',
    'defaultgroup',
  ];

  protected $visible = [
    'category',
    'title',
    'location',
    'entity',
    'profile',
    'supervisor',
    'group',
    'completename',
    'certificates',
    'defaultgroup',
    'documents',
  ];

  protected $with = [
    'category:id,name',
    'title:id,name',
    'location:id,name',
    'entity:id,name,completename',
    'profile:id,name',
    'supervisor:id,name,firstname,lastname',
    'group:id,name,completename',
    'certificates:id,name',
    'defaultgroup:id,name',
    'documents:id,name',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Usercategory', 'usercategory_id');
  }

  public function title(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Usertitle', 'usertitle_id');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function profile(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Profile');
  }

  public function supervisor(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_supervisor');
  }

  public function group(): BelongsToMany
  {
    return $this
      ->belongsToMany('\App\Models\Group')
      ->withPivot('group_id', 'is_dynamic', 'is_manager', 'is_userdelegate');
  }

  public function defaultgroup(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id');
  }

  public function profiles(): BelongsToMany
  {
      return $this->belongsToMany('\App\Models\Profile')->withPivot('entity_id', 'is_recursive');
  }

  public function getCompletenameAttribute()
  {
    if ($this->id == 0)
    {
      return 'Nobody';
    }

    $name = '';
    if (
        (!is_null($this->lastname) && !empty($this->lastname)) ||
        (!is_null($this->firstname) && !empty($this->firstname))
    )
    {
      $names = [];
      if (!is_null($this->firstname))
      {
        $names[] = $this->firstname;
      }
      if (!is_null($this->lastname))
      {
        $names[] = $this->lastname;
      }
      $name = implode(' ', $names);
    }
    else
    {
      $name = $this->name;
    }
    return $name;
  }

  public function certificates(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Certificate',
      'item',
      'certificate_item'
    )->withPivot(
      'certificate_id',
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
      'updated_at',
    );
  }
}
