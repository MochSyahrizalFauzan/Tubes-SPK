<?php

namespace App\Http\Controllers;

use App\Models\MachineProcessingRule;
use App\Models\MachineType;
use App\Models\MachineSize;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcessingRuleController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));

    $rules = MachineProcessingRule::query()
      ->with(['machineType', 'machineSize'])
      ->when($q !== '', function ($query) use ($q) {
        $query->where('machine_type_id', 'like', "%$q%")
              ->orWhere('size_id', 'like', "%$q%");
      })
      ->orderBy('machine_type_id')
      ->orderBy('size_id')
      ->paginate(10)
      ->withQueryString();

    return view('processing_rules.index', compact('rules', 'q'));
  }

  public function create()
  {
    $machineTypes = MachineType::orderBy('machine_type_id')->get();
    $machineSizes = MachineSize::orderBy('size_id')->get();

    return view('processing_rules.create', compact('machineTypes', 'machineSizes'));
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'machine_type_id' => ['required','string', Rule::exists('machine_types','machine_type_id')],
      'size_id'         => ['required','string', Rule::exists('machine_sizes','size_id')],
      'process_days'    => ['required','integer','min:1','max:365'],
      'due_days'        => ['required','integer','min:1','max:365'],
    ]);

    // enforce unique (machine_type_id, size_id) at app level too
    $exists = MachineProcessingRule::where('machine_type_id', $data['machine_type_id'])
      ->where('size_id', $data['size_id'])
      ->exists();

    if ($exists) {
      return back()
        ->withInput()
        ->withErrors(['size_id' => 'Rule untuk kombinasi Machine Type + Size ini sudah ada.']);
    }

    MachineProcessingRule::create($data);

    return redirect()->route('processing-rules.index')->with('success', 'Processing Rule berhasil ditambahkan.');
  }

  public function edit(MachineProcessingRule $processingRule)
  {
    $machineTypes = MachineType::orderBy('machine_type_id')->get();
    $machineSizes = MachineSize::orderBy('size_id')->get();

    return view('processing_rules.edit', compact('processingRule', 'machineTypes', 'machineSizes'));
  }

  public function update(Request $request, MachineProcessingRule $processingRule)
  {
    $data = $request->validate([
      'machine_type_id' => ['required','string', Rule::exists('machine_types','machine_type_id')],
      'size_id'         => ['required','string', Rule::exists('machine_sizes','size_id')],
      'process_days'    => ['required','integer','min:1','max:365'],
      'due_days'        => ['required','integer','min:1','max:365'],
    ]);

    // avoid duplicate combination except current rule
    $exists = MachineProcessingRule::where('machine_type_id', $data['machine_type_id'])
      ->where('size_id', $data['size_id'])
      ->where('rule_id', '!=', $processingRule->rule_id)
      ->exists();

    if ($exists) {
      return back()
        ->withInput()
        ->withErrors(['size_id' => 'Rule untuk kombinasi Machine Type + Size ini sudah ada.']);
    }

    $processingRule->update($data);

    return redirect()->route('processing-rules.index')->with('success', 'Processing Rule berhasil diupdate.');
  }

  public function destroy(MachineProcessingRule $processingRule)
  {
    $processingRule->delete();
    return redirect()->route('processing-rules.index')->with('success', 'Processing Rule berhasil dihapus.');
  }

  public function show(MachineProcessingRule $processingRule)
  {
    return redirect()->route('processing-rules.edit', $processingRule);
  }
}
