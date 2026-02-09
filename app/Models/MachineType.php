<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineType extends Model
{
  protected $table = 'machine_types';
  protected $primaryKey = 'machine_type_id';

  public $incrementing = false;
  protected $keyType = 'string';

  const CREATED_AT = 'created_at';
  const UPDATED_AT = null;

  protected $fillable = [
    'machine_type_id',
    'machine_name',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];
}
