<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRun extends Model
{
  protected $table = 'schedule_runs';
  protected $primaryKey = 'run_id';
  public $incrementing = true;

  public $timestamps = false;

  protected $fillable = [
    'run_date','capacity_slots','total_orders','method','created_by','note'
  ];

  public function results()
  {
    return $this->hasMany(ScheduleResult::class, 'run_id', 'run_id');
  }
}
