<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Models\AcademicYear;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('academic.years.year', compact('years'));
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $data = $request->validated();

        if (! empty($data['is_current'])) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        AcademicYear::create($data);

        return back()->with('success', 'Année académique créée avec succès.');
    }

    public function update(StoreAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $data = $request->validated();

        if (! empty($data['is_current'])) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
        }

        $academicYear->update($data);

        return back()->with('success', 'Année académique mise à jour.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return back()->with('success', 'Année académique supprimée.');
    }
}
