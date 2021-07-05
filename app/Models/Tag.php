<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['slug','title'];

    public function scopeSearch($query, $title)
    {
        return $query->where('title', 'LIKE', "%$title%");
    }
}
