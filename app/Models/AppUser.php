<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
  protected $table = 'app_users';
  protected $primaryKey = 'user_id';
  public $incrementing = true;

  public $timestamps = false;

  protected $fillable = ['user_id','name','username','password_hash','role','is_active','created_at'];
}
