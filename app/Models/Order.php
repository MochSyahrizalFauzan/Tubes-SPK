<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $table = 'orders';
  protected $primaryKey = 'order_id';
  public $incrementing = false;
  protected $keyType = 'string';

  const CREATED_AT = 'created_at';
  const UPDATED_AT = null;

  protected $fillable = [
    'order_id','invoice_id','customer_id','machine_type_id','size_id',
    'process_days','qty','unit','order_date','due_date','status','note'
  ];

  protected $casts = [
    'order_date' => 'date',
    'due_date'   => 'date',
  ];

  public function customer() { return $this->belongsTo(Customer::class, 'customer_id', 'customer_id'); }
  public function machineType(){ return $this->belongsTo(MachineType::class, 'machine_type_id', 'machine_type_id'); }
  public function machineSize(){ return $this->belongsTo(MachineSize::class, 'size_id', 'size_id'); }
  public function invoice(){ return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id'); }
}
