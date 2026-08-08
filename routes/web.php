<?php

use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Academic\CursusController;
use App\Http\Controllers\Academic\FieldController;
use App\Http\Controllers\Academic\LevelController;
use App\Http\Controllers\Academic\SpecialityController;
use App\Http\Controllers\LaptopController;
use App\Http\Controllers\PreEnrollmentController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffEnrollmentController;
use App\Http\Controllers\StaffPaymentController;
use App\Http\Controllers\StudentEnrollmentController;
use App\Http\Controllers\StudentPaymentController;
use Illuminate\Support\Facades\Route;

// 1. Page d'accueil publique
Route::view('/', 'welcome')->name('welcome');

// 2. Espace Étudiant
Route::middleware(['auth', 'role:student'])->prefix('students')->group(function () {
    Route::view('dashboard', 'student-dashboard.dashboard')->name('student-dashboard.dashboard');

    Route::controller(PreEnrollmentController::class)->group(function () {
        Route::get('pre-enrollment', 'index')->name('pre-enrollments.index');
        Route::post('pre-enrollment', 'store')->name('pre-enrollments.store');
        Route::get('pre-enrollment/{preEnrollment}/success', 'success')->name('pre-enrollments.success');
        Route::get('pre-enrollment/{preEnrollment}/pdf', 'downloadPdf')->name('pre-enrollments.pdf');
    });

    Route::get('payments', [StudentPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [StudentPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/callback', [StudentPaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/{payment}/receipt', [StudentPaymentController::class, 'downloadReceipt'])->name('payments.receipt');

    Route::get('/enrollment', [StudentEnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('/enrollment', [StudentEnrollmentController::class, 'store'])->name('enrollments.store');
});

// 3. Espace Staff / Admin
Route::middleware(['auth', 'role:admin'])->prefix('staff')->group(function () {

    // Tableau de bord Staff
    Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('staff-dashboard.dashboard');

    // Gestion des Admissions (StaffEnrollmentController)
    Route::controller(StaffEnrollmentController::class)->group(function () {
        Route::get('admissions', 'index')->name('staff-dashboard.admissions');
        Route::post('admissions', 'store')->name('staff.admissions.store');
        Route::patch('admissions/{enrollment}/approve', 'approve')->name('staff.admissions.approve');
        Route::put('admissions/{enrollment}', 'update')->name('staff.admissions.update');
    });

    // Gestion des Paiements (StaffPaymentController)
    Route::controller(StaffPaymentController::class)->group(function () {
        Route::get('payments', 'index')->name('staff-dashboard.payments');
        Route::post('payments/cash', 'storeCash')->name('staff.payments.storeCash');
        Route::patch('payments/{payment}/approve', 'approve')->name('staff.payments.approve');
    });

    // Gestion du Stock PC (LaptopController)
    Route::controller(LaptopController::class)->group(function () {
        Route::get('pc-stock', 'index')->name('staff-dashboard.pc-stock');
        Route::post('pc-stock', 'store')->name('staff-dashboard.pc-stock.store');
        Route::put('pc-stock/{laptop}', 'update')->name('staff-dashboard.pc-stock.update');
        Route::delete('pc-stock/{laptop}', 'destroy')->name('staff-dashboard.pc-stock.destroy');
    });

    // Structuration Académique
    Route::prefix('academic')->name('academic.')->group(function () {

        Route::controller(AcademicYearController::class)->group(function () {
            Route::get('years', 'index')->name('years');
            Route::post('years', 'store')->name('years.store');
            Route::put('years/{academicYear}', 'update')->name('years.update');
            Route::delete('years/{academicYear}', 'destroy')->name('years.destroy');
        });

        Route::controller(CursusController::class)->group(function () {
            Route::get('cursuses', 'index')->name('cursuses');
            Route::post('cursuses', 'store')->name('cursuses.store');
            Route::put('cursuses/{cursus}', 'update')->name('cursuses.update');
            Route::delete('cursuses/{cursus}', 'destroy')->name('cursuses.destroy');
        });

        Route::controller(FieldController::class)->group(function () {
            Route::get('fields', 'index')->name('fields');
            Route::post('fields', 'store')->name('fields.store');
            Route::put('fields/{field}', 'update')->name('fields.update');
            Route::delete('fields/{field}', 'destroy')->name('fields.destroy');
        });

        Route::controller(SpecialityController::class)->group(function () {
            Route::get('specialities', 'index')->name('specialities');
            Route::post('specialities', 'store')->name('specialities.store');
            Route::put('specialities/{speciality}', 'update')->name('specialities.update');
            Route::delete('specialities/{speciality}', 'destroy')->name('specialities.destroy');
        });

        Route::controller(LevelController::class)->group(function () {
            Route::get('levels', 'index')->name('levels');
            Route::post('levels', 'store')->name('levels.store');
            Route::put('levels/{level}', 'update')->name('levels.update');
            Route::delete('levels/{level}', 'destroy')->name('levels.destroy');
        });
    });

});
