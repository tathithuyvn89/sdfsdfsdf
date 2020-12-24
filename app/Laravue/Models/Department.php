<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $fillable = [
        'name_ja', 'name_en'
    ];
    public function surveys()
    {
        return $this->hasMany('App\Laravue\Models\Survey');
    }
}
