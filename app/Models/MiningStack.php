<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiningStack extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function config()
    {
        return $this->belongsTo(MiningConfig::class);
    }
    
    public function log()
    {
        return $this->hasMany(MiningHistory::class);
    }
}
