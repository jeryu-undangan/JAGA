<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'application_mode',
        'weekly_budget',
        'base_salary',
        'salary_payday_day',
        'payday_interval',
        'payday_workday_interval',
        'payday_anchor_date',
        'profile_photo_path',
        'date_format',
        'low_balance_percent',
        'dark_mode',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'weekly_budget' => 'integer',
        'base_salary' => 'integer',
        'salary_payday_day' => 'integer',
        'payday_anchor_date' => 'date',
        'payday_workday_interval' => 'integer',
        'low_balance_percent' => 'integer',
        'dark_mode' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function salaryBudgets(): HasMany
    {
        return $this->hasMany(SalaryBudget::class);
    }
}
