<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;


class CustomerController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));

    $customers = Customer::query()
      ->when($q !== '', function ($query) use ($q) {
        $query->where('customer_name', 'like', "%$q%")
              ->orWhere('customer_code', 'like', "%$q%");
      })
      ->orderByDesc('customer_id')
      ->paginate(10)
      ->withQueryString();

    return view('customers.index', compact('customers', 'q'));
  }

  public function create()
  {
    return view('customers.create');
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'customer_code' => 'nullable|string|max:20|unique:customers,customer_code',
      'customer_name' => 'required|string|max:180',
      'address'       => 'nullable|string|max:255',
      'phone'         => 'nullable|string|max:30',
    ]);

    Customer::create($data);

    return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
  }

  public function edit(Customer $customer)
  {
    return view('customers.edit', compact('customer'));
  }

  public function update(Request $request, Customer $customer)
  {
    $data = $request->validate([
      'customer_code' => 'nullable|string|max:20|unique:customers,customer_code,' . $customer->customer_id . ',customer_id',
      'customer_name' => 'required|string|max:180',
      'address'       => 'nullable|string|max:255',
      'phone'         => 'nullable|string|max:30',
    ]);

    $customer->update($data);

    return redirect()->route('customers.index')->with('success', 'Customer berhasil diupdate.');
  }

public function destroy(Customer $customer)
{
    // Cek apakah customer dipakai di orders
    $hasOrders = DB::table('orders')
        ->where('customer_id', $customer->customer_id)
        ->exists();

    if ($hasOrders) {
        return redirect()
            ->route('customers.index')
            ->with('error', 'Customer tidak bisa dihapus karena sudah memiliki Order. Hapus/ubah Order terkait.');
    }

    try {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    } catch (QueryException $e) {
        // fallback kalau ada relasi lain yang masih mengunci
        return redirect()
            ->route('customers.index')
            ->with('error', 'Customer gagal dihapus karena masih terhubung dengan data lain.');
    }
}


  public function show(Customer $customer)
  {
    return redirect()->route('customers.edit', $customer);
  }
}
