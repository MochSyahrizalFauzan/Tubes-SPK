<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  protected $table = 'customers';
  protected $primaryKey = 'customer_id';
  public $incrementing = true;

  const CREATED_AT = 'created_at';
  const UPDATED_AT = null;

  protected $fillable = [
    'customer_code','customer_name','address','phone'
  ];
}
