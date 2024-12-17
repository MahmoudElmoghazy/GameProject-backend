<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'time_for_each_question',
        'category_id',
        'user_id',
        'started_at'
    ];

    protected static function booted()
    {
        static::creating(function ($game) {
            $game->code = Str::upper(Str::random(10)); // Generate 10 character long random string
        });
    }
    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class);
    }

    public function gameQuestions(): HasMany
    {
        return $this->hasMany(GameQuestion::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->BelongsToMany(User::class,'game_users','user_id','game_id');
    }
}
