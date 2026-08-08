<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFieldRequest;
use App\Models\Cursus;
use App\Models\Field;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::with('cursus')->withCount('specialities')->orderBy('label')->get();
        $cursuses = Cursus::orderBy('label')->get();

        $stats = [
            'fields' => $fields->count(),
            'specialities' => $fields->sum('specialities_count'),
            'cursuses' => $cursuses->count(),
        ];

        return view('academic.fields.field', compact('fields', 'cursuses', 'stats'));
    }

    public function store(StoreFieldRequest $request)
    {
        Field::create($request->validated());

        return back()->with('success', 'Filière créée avec succès.');
    }

    public function update(StoreFieldRequest $request, Field $field)
    {
        $field->update($request->validated());

        return back()->with('success', 'Filière mise à jour.');
    }

    public function destroy(Field $field)
    {
        $field->delete();

        return back()->with('success', 'Filière supprimée.');
    }
}
