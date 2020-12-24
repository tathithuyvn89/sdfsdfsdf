<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
    protected $fillable = [
        'title_ja', 'title_en','display','status','correct_pic','incorrect_pic', 'completemessage_ja','completemessage_en','department_id','classs_id','admin_created'
    ];
    // protected $guarded = [];
    public function tags()
    {
        return $this->belongsToMany('App\Laravue\Models\Tag');
        //->withTimeStamps();
    }
    public function quizzes()
    {
        return $this->belongsToMany('App\Laravue\Models\Quiz','survey_quiz','survey_id','quiz_id');
        //->withTimeStamps();
    }
    public function classs()
    {
        return $this->belongsTo('App\Laravue\Models\Classs');
    }
    public function department()
    {
        return $this->belongsTo('App\Laravue\Models\Department');
    }
    public function user()
    {
        return $this->belongsTo('App\Laravue\Models\User','admin_created','id');
    }
    public function answered_surveys()
    {
        return $this->belongsTo('App\Laravue\Models\AnsweredSurvey','id','survey_id');
    }
}
