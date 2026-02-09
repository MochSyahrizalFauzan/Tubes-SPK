<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
  protected $table = 'slots';
  protected $primaryKey = 'slot_id';
  public $incrementing = false;
  protected $keyType = 'string';

  public $timestamps = false;

  protected $fillable = ['slot_id','slot_name','is_active'];

  protected $casts = [
    'is_active' => 'boolean',
  ];
}
