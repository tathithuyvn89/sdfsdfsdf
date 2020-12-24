<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Classs extends Model
{
    //
    protected $fillable = [
        'name_ja', 'name_en'
    ];
    // protected $guarded = [];
    public function surveys()
    {
        return $this->hasMany('App\Laravue\Models\Survey');
    }
}
