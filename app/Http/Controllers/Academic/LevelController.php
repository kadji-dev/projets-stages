<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelRequest;
use App\Models\Field;
use App\Models\Level;
use App\Models\Speciality;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        $fields = Field::with('cursus')->orderBy('label')->get();
        $specialities = Speciality::with('field')->orderBy('label')->get();

        $selectedFieldId = $request->integer('field_id') ?: null;
        $selectedSpecialityId = $request->integer('speciality_id') ?: null;

        $levels = Level::with(['field.cursus', 'speciality'])
            ->when($selectedFieldId, fn ($q) => $q->where('field_id', $selectedFieldId))
            ->when($selectedSpecialityId, fn ($q) => $q->where('speciality_id', $selectedSpecialityId))
            ->orderBy('order')
            ->get();

        return view('academic.levels.level', compact('levels', 'fields', 'specialities', 'selectedFieldId', 'selectedSpecialityId'));
    }

    public function store(StoreLevelRequest $request)
    {
        Level::create($request->validated());

        return back()->with('success', 'Niveau créé avec succès.');
    }

    public function update(StoreLevelRequest $request, Level $level)
    {
        $level->update($request->validated());

        return back()->with('success', 'Niveau mis à jour.');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return back()->with('success', 'Niveau supprimé.');
    }
}
