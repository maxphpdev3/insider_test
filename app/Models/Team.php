<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'code',
        'name'
    ];

    /**
     * Return team by code
     *
     * @param  string  $code
     * @return self | null
     */
    public function getByCode(string $code): self
    {
        return self::where('code', $code)->first();
    }

    /**
     * Get all leagues where the team plays.
     *
     * @return HasMany
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(LeagueTeam::class, 'team_id', 'id');
    }
}
