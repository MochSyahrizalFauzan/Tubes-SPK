<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineSize extends Model
{
  protected $table = 'machine_sizes';
  protected $primaryKey = 'size_id';

  public $incrementing = false;
  protected $keyType = 'string';

  const CREATED_AT = 'created_at';
  const UPDATED_AT = null;

  protected $fillable = [
    'size_id',
    'size_name',
  ];
}
