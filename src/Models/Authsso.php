<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Authsso extends Common
{
  protected $definition = '\App\Models\Definitions\Authsso';
  protected $titles = ['SSO', 'SSO'];
  protected $icon = 'id card alternate';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::creating(function ($model)
    {
      $model->callbackid = uniqid();
    });

    static::created(function ($model)
    {
      \App\v1\Controllers\Authsso::initScopesForProvider($model);
      \App\v1\Controllers\Authsso::initOptionsForProvider($model);
    });
  }
}
