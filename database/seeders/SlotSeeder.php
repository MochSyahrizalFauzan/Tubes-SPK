<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlotSeeder extends Seeder
{
  public function run(): void
  {
    $slots = [];
    for ($i = 1; $i <= 7; $i++) {
      $id = 'S' . $i;
      $slots[] = [
        'slot_id'   => $id,
        'slot_name' => 'Slot ' . $i,
        'is_active' => 1,
      ];
    }

    foreach ($slots as $s) {
      DB::table('slots')->updateOrInsert(
        ['slot_id' => $s['slot_id']],
        ['slot_name' => $s['slot_name'], 'is_active' => $s['is_active']]
      );
    }
  }
}
