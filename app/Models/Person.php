<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    use HasFactory;
    protected $table = "people";
    protected $fillable = ['name', 'last_name','email', 'birthday', 'phone'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function receptionist(): HasOne
    {
        return $this->hasOne(Receptionist::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function scopeSearch($query, $term): void
    {
        $columns = ['name', 'email', 'phone'];

        $query->where(function ($q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhereRaw("LOWER($column) LIKE ?", ["%$term%"]);
            }
        });
    }
}
