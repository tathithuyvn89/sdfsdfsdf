<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class AnsweredOption extends Model
{
    protected $fillable = [
        'option_id','answered_quiz_id'
    ];
    public function option()
    {
        return $this->belongsTo('App\Laravue\Models\Option','option_id','id');
    }
    public function answered_quiz()
    {
        return $this->belongsTo('App\Laravue\Models\AnsweredQuiz','answered_quiz_id','id');
    }
}
