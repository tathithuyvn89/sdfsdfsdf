<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class AnsweredQuiz extends Model
{
    protected $fillable = [
        'quiz_id', 'times','answered_survey_id','correct'
    ];
    public function quizz()
    {
        return $this->belongsTo('App\Laravue\Models\Quiz','quiz_id','id');
    }
    public function answered_survey()
    {
        return $this->belongsTo('App\Laravue\Models\AnsweredSurvey','answered_survey_id','id');
    }
    public function answered_options()
    {
        return $this->hasMany('App\Laravue\Models\AnsweredOption','answered_quiz_id','id');
    }
}
