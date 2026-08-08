<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCursusRequest;
use App\Models\Cursus;

class CursusController extends Controller
{
    public function index()
    {
        $cursuses = Cursus::withCount(['fields', 'levels'])->orderBy('label')->get();

        return view('academic.cursuses.cursus', compact('cursuses'));
    }

    public function store(StoreCursusRequest $request)
    {
        Cursus::create($request->validated());

        return back()->with('success', 'Cursus créé avec succès.');
    }

    public function update(StoreCursusRequest $request, Cursus $cursus)
    {
        $cursus->update($request->validated());

        return back()->with('success', 'Cursus mis à jour.');
    }

    public function destroy(Cursus $cursus)
    {
        $cursus->delete();

        return back()->with('success', 'Cursus supprimé.');
    }
}
