<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    //
    protected $fillable = [
        'title_ja', 'title_en','sentence_ja','sentence_en','picture_ja','picture_en','explain_correct_ja','explain_correct_en','explain_incorrect_ja','explain_incorrect_en','admin_created'
    ];
    public function surveys()
    {
        return $this->belongsToMany('App\Laravue\Models\Survey','survey_quiz','quiz_id','survey_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Laravue\Models\User','admin_created','id');
    }
    public function options()
    {
        return $this->hasMany('App\Laravue\Models\Option','quiz_id','id');
    }
}
