<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;
    protected $fillable = ['model_id', 'market', 'name', 'period', 'path_to_image', 'path_to_page']; // Поля, которые можно заполнять

    public function model()
    {
        return $this->belongsTo(Model::class);
    }

}
