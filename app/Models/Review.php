<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['score', 'feedback'])]
class Review extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }
}
