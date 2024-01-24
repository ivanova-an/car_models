<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    use HasFactory;

    protected $fillable = ['name'];

    public function generations()
    {
        return $this->hasMany(Generation::class);
    }
}
