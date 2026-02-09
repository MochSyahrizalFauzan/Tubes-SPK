<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleResult extends Model
{
  protected $table = 'schedule_results';
  protected $primaryKey = 'result_id';
  public $incrementing = true;

  public $timestamps = false;

  protected $fillable = [
    'run_id','order_id','decision','slot_id','start_day','finish_day','tardiness_days','reason'
  ];
}
