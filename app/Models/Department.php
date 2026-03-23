<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'department_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'department_name',
        'department_code',
        'is_active'
    ];


    public function sections()
    {
        return $this->hasMany(Section::class, 'department_id', 'department_id');
    }
}
