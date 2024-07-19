<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use  HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_of_questions',
        'no_of_players',
        'code',
        'difficulty_id',
        'time_for_each_question'
    ];

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class);
    }

    public function gameQuestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GameQuestion::class);
    }
}
