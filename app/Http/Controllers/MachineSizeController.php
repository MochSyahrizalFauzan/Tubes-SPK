<?php

namespace App\Http\Controllers;

use App\Models\MachineSize;
use Illuminate\Http\Request;

class MachineSizeController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));

    $machineSizes = MachineSize::query()
      ->when($q !== '', function ($query) use ($q) {
        $query->where('size_id', 'like', "%$q%")
              ->orWhere('size_name', 'like', "%$q%");
      })
      ->orderBy('size_id')
      ->paginate(10)
      ->withQueryString();

    return view('machine_sizes.index', compact('machineSizes', 'q'));
  }

  public function create()
  {
    return view('machine_sizes.create');
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'size_id'   => 'required|string|max:10|unique:machine_sizes,size_id',
      'size_name' => 'required|string|max:50',
    ]);

    MachineSize::create($data);

    return redirect()->route('machine-sizes.index')->with('success', 'Machine Size berhasil ditambahkan.');
  }

  public function edit(MachineSize $machineSize)
  {
    return view('machine_sizes.edit', compact('machineSize'));
  }

  public function update(Request $request, MachineSize $machineSize)
  {
    $data = $request->validate([
      'size_id'   => 'required|string|max:10|unique:machine_sizes,size_id,' . $machineSize->size_id . ',size_id',
      'size_name' => 'required|string|max:50',
    ]);

    $newId = $data['size_id'];

    if ($newId !== $machineSize->size_id) {
      MachineSize::where('size_id', $machineSize->size_id)->update($data);
      return redirect()->route('machine-sizes.index')->with('success', 'Machine Size berhasil diupdate.');
    }

    $machineSize->update($data);

    return redirect()->route('machine-sizes.index')->with('success', 'Machine Size berhasil diupdate.');
  }

  public function destroy(MachineSize $machineSize)
  {
    $machineSize->delete();
    return redirect()->route('machine-sizes.index')->with('success', 'Machine Size berhasil dihapus.');
  }

  public function show(MachineSize $machineSize)
  {
    return redirect()->route('machine-sizes.edit', $machineSize);
  }
}
