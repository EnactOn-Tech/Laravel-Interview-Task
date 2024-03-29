<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProbabilityRewards extends Model
{
    use HasFactory;

    protected $table = 'probability_rewards';
    protected $primaryKey = 'id';

    protected $fillable = [
        'probability_id',
        'awarded_percentage',
        'awarded'
    ];

    public function probability()
    {
        return $this->hasOne('App\Models\Probability','id','probability_id');
    }
}
