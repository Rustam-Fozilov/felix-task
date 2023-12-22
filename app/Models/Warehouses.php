<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouses extends Model
{
    use HasFactory;

    public $fillable = [
        'material_id',
        'reminder',
        'price',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
