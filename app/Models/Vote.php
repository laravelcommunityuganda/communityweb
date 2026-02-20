<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'votable_type',
        'votable_id',
        'vote',
    ];

    protected $casts = [
        'vote' => 'integer',
    ];

    /**
     * Get the parent votable model.
     */
    public function votable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who voted.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
