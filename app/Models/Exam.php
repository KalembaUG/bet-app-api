<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'candidate_id',
        'candidate_name',
        'location_name',
        'date',
        'longitude',
        'latitude'
    ];
}
