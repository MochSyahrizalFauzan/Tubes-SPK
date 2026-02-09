<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlotController extends Controller
{
  public function index()
  {
    $slots = Slot::orderBy('slot_id')->get();
    return view('slots.index', compact('slots'));
  }

  public function toggle(Request $request, $slotId)
  {
    $slot = Slot::where('slot_id', $slotId)->firstOrFail();
    $slot->is_active = !$slot->is_active;
    $slot->save();

    return redirect()->route('slots.index')->with('success', 'Status slot berhasil diubah.');
  }

  // opsional: tombol seed ulang S1..S7 (idempotent)
  public function seedDefault()
  {
    DB::transaction(function () {
      for ($i = 1; $i <= 7; $i++) {
        $id = 'S'.$i;
        DB::table('slots')->updateOrInsert(
          ['slot_id' => $id],
          ['slot_name' => 'Slot '.$i, 'is_active' => 1]
        );
      }
    });

    return redirect()->route('slots.index')->with('success', 'Default slots S1..S7 sudah dipastikan ada.');
  }
}
