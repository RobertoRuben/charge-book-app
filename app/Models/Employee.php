<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'last_name',
        'middle_name',
        'first_names',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->last_name} {$this->middle_name}, {$this->first_names}";
    }

    /**
     * Obtiene el usuario asociado con este empleado.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
