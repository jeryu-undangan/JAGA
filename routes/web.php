<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/setup/mode', [BudgetController::class, 'chooseApplicationMode'])->middleware('application.mode')->name('application-mode.choose');
    Route::put('/settings/application-mode', [BudgetController::class, 'updateApplicationMode'])->middleware('application.mode')->name('application-mode.update');
    Route::get('/budget/setup', [BudgetController::class, 'setupSchedule'])->middleware('application.mode')->name('budget.schedule.setup');
    Route::put('/budget/setup', [BudgetController::class, 'updatePaydaySchedule'])->middleware('application.mode')->name('budget.schedule.setup.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('application.mode')->prefix('salary')->name('salary.')->group(function () {
        Route::get('/', [SalaryController::class, 'index'])->name('index');
        Route::get('/transactions', [SalaryController::class, 'transactions'])->name('transactions.index');
        Route::get('/reports', [SalaryController::class, 'reports'])->name('reports.index');
        Route::get('/reports/export', [SalaryController::class, 'export'])->name('reports.export');
        Route::put('/profile', [SalaryController::class, 'updateProfile'])->name('profile.update');
        Route::post('/income', [SalaryController::class, 'storeIncome'])->name('income.store');
        Route::post('/expense', [SalaryController::class, 'storeExpense'])->name('expense.store');
        Route::post('/budgets', [SalaryController::class, 'storeBudget'])->name('budgets.store');
        Route::delete('/transactions/{transaction}', [SalaryController::class, 'destroyTransaction'])->name('transactions.destroy');
    });

    Route::middleware(['application.mode', 'budget.schedule'])->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('budget.index');
        Route::get('/transactions', [BudgetController::class, 'transactions'])->name('transactions.index');
        Route::get('/reports', [BudgetController::class, 'reports'])->name('reports.index');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/settings/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::put('/settings/theme', [ProfileController::class, 'updateTheme'])->name('theme.update');
        Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/settings/transactions', [ProfileController::class, 'destroyTransactions'])->name('transactions.destroy-all');
        Route::post('/transactions/expense', [BudgetController::class, 'storeExpense'])->name('transactions.expense');
        Route::post('/transactions/income', [BudgetController::class, 'storeIncome'])->name('transactions.income');
        Route::delete('/transactions/{transaction}', [BudgetController::class, 'destroy'])->name('transactions.destroy');
        Route::get('/reports/export', [BudgetController::class, 'export'])->name('reports.export');
        Route::put('/budget', [BudgetController::class, 'updateBudget'])->name('budget.update');
    });

    Route::get('/settings/budget', [BudgetController::class, 'settings'])->middleware('application.mode')->name('budget.settings');
});
