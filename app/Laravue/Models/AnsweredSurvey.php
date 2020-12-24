<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class AnsweredSurvey extends Model
{
    protected $fillable = [
        'respondent_id', 'result','survey_id','current_result','total_quizzes'
    ];
    public function user()
    {
        return $this->belongsTo('App\Laravue\Models\User','respondent_id','id');
    }
    public function survey()
    {
        return $this->hasMany('App\Laravue\Models\Survey','survey_id','id');
    }
    public function answered_quizzes()
    {
        return $this->hasMany('App\Laravue\Models\AnsweredQuiz','answered_survey_id','id');
    }
}
