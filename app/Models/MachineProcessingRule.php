<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineProcessingRule extends Model
{
  protected $table = 'machine_processing_rules';
  protected $primaryKey = 'rule_id';
  public $incrementing = true;

  public $timestamps = false; // tabel ini tidak punya created_at/updated_at

  protected $fillable = [
    'machine_type_id',
    'size_id',
    'process_days',
    'due_days',
  ];

  public function machineType()
  {
    return $this->belongsTo(MachineType::class, 'machine_type_id', 'machine_type_id');
  }

  public function machineSize()
  {
    return $this->belongsTo(MachineSize::class, 'size_id', 'size_id');
  }
}
