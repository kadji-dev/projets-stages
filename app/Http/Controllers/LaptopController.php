<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaptopRequest;
use App\Models\Laptop;

class LaptopController extends Controller
{
    public function index()
    {
        $laptops = Laptop::orderByDesc('created_at')->get();

        $stats = [
            'total' => $laptops->count(),
            'disponible' => $laptops->where('status', 'disponible')->count(),
            'attribue' => $laptops->where('status', 'attribue')->count(),
            'maintenance' => $laptops->where('status', 'maintenance')->count(),
        ];

        return view('pc-stocks.stock', compact('laptops', 'stats'));
    }

    public function store(StoreLaptopRequest $request)
    {
        Laptop::create($request->validated());

        return back()->with('success', 'Poste ajouté au parc informatique.');
    }

    public function update(StoreLaptopRequest $request, Laptop $laptop)
    {
        $laptop->update($request->validated());

        return back()->with('success', 'Poste mis à jour.');
    }

    public function destroy(Laptop $laptop)
    {
        $laptop->delete();

        return back()->with('success', 'Poste supprimé du parc informatique.');
    }
}
