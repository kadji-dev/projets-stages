<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpecialityRequest;
use App\Models\Field;
use App\Models\Speciality;
use Illuminate\Http\Request;

class SpecialityController extends Controller
{
    public function index(Request $request)
    {
        $fields = Field::with('cursus')->orderBy('label')->get();

        $selectedFieldId = $request->integer('field_id') ?: $fields->first()?->id;

        $specialities = Speciality::with('field.cursus')
            ->when($selectedFieldId, fn ($q) => $q->where('field_id', $selectedFieldId))
            ->orderBy('label')
            ->get();

        return view('academic.specialities.specialitie', compact('specialities', 'fields', 'selectedFieldId'));
    }

    public function store(StoreSpecialityRequest $request)
    {
        Speciality::create($request->validated());

        return back()->with('success', 'Spécialité créée avec succès.');
    }

    public function update(StoreSpecialityRequest $request, Speciality $speciality)
    {
        $speciality->update($request->validated());

        return back()->with('success', 'Spécialité mise à jour.');
    }

    public function destroy(Speciality $speciality)
    {
        $speciality->delete();

        return back()->with('success', 'Spécialité supprimée.');
    }
}
