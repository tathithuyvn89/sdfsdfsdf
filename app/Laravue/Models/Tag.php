<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //
    protected $fillable = [
        'name_ja', 'name_en'
    ];
    // protected $guarded = [];
    public function surveys()
    {
        return $this->belongsToMany('App\Laravue\Models\Survey');
    }
}
