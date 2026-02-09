<?php

namespace App\Http\Controllers;

use App\Models\MachineType;
use Illuminate\Http\Request;

class MachineTypeController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));

    $machineTypes = MachineType::query()
      ->when($q !== '', function ($query) use ($q) {
        $query->where('machine_type_id', 'like', "%$q%")
              ->orWhere('machine_name', 'like', "%$q%");
      })
      ->orderBy('machine_type_id')
      ->paginate(10)
      ->withQueryString();

    return view('machine_types.index', compact('machineTypes', 'q'));
  }

  public function create()
  {
    return view('machine_types.create');
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'machine_type_id' => 'required|string|max:10|unique:machine_types,machine_type_id',
      'machine_name'    => 'required|string|max:120',
      'is_active'       => 'nullable|boolean',
    ]);

    $data['is_active'] = $request->boolean('is_active');

    MachineType::create($data);

    return redirect()->route('machine-types.index')->with('success', 'Machine Type berhasil ditambahkan.');
  }

  public function edit(MachineType $machineType)
  {
    return view('machine_types.edit', compact('machineType'));
  }

  public function update(Request $request, MachineType $machineType)
  {
    $data = $request->validate([
      'machine_type_id' => 'required|string|max:10|unique:machine_types,machine_type_id,' . $machineType->machine_type_id . ',machine_type_id',
      'machine_name'    => 'required|string|max:120',
      'is_active'       => 'nullable|boolean',
    ]);

    // Kalau ID diubah, kita handle manual (karena PK string)
    $newId = $data['machine_type_id'];
    $data['is_active'] = $request->boolean('is_active');

    if ($newId !== $machineType->machine_type_id) {
      // update PK dengan query builder supaya aman
      MachineType::where('machine_type_id', $machineType->machine_type_id)->update($data);
      return redirect()->route('machine-types.index')->with('success', 'Machine Type berhasil diupdate.');
    }

    $machineType->update($data);

    return redirect()->route('machine-types.index')->with('success', 'Machine Type berhasil diupdate.');
  }

  public function destroy(MachineType $machineType)
  {
    // kalau sudah dipakai di orders/rules, FK akan menolak (ON DELETE RESTRICT)
    $machineType->delete();

    return redirect()->route('machine-types.index')->with('success', 'Machine Type berhasil dihapus.');
  }

  public function show(MachineType $machineType)
  {
    return redirect()->route('machine-types.edit', $machineType);
  }
}
