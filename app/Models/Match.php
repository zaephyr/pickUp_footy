<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'event_date', 'completed', 'participants', 'teamA', 'teamB', 'score'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function matchSquads()
    {
        return $this->hasMany(MatchSquad::class);
    }

    public function playingTeams()
    {
        return $this->hasManyThrough(PlayingTeam::class, MatchSquad::class, 'match_id', 'match_squad_id', 'id', 'id');
    }
}
