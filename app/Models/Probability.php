<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Probability extends Model
{
    use HasFactory;

    protected $table = 'probabilities';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'percentage'
    ];

    public function reward()
    {
        return $this->hasOne('App\Models\ProbabilityRewards','probability_id','id');
    }
}
