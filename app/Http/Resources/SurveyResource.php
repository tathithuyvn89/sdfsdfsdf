<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\constants;
use App\Http\Resources\QuizResource;
class SurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(!empty($this->title_ja) && !empty($this->title_en))
        {
            $quiz = QuizResource::collection($this->quizzes);
            $quiz_arr =[];
            foreach ($quiz as $key => $value) {
                array_push($quiz_arr,$value->id);
            }
            return [
                "id" => $this->id,
                "title_ja" => $this->title_ja,
                "title_en" => $this->title_en,
                "display" => $this->display,
                "status" => $this->status,
                "correct_pic" => $this->correct_pic,
                "incorrect_pic" => $this->incorrect_pic,
                "completemessage_ja" => $this->completemessage_ja,
                "completemessage_en" => $this->completemessage_en,
                "class_id" => $this->class_id,
                "class_name_ja" => $this->class_name_ja,
                "class_name_en" => $this->class_name_en,
                "department_id" => $this->department_id,
                "department_name_ja" => $this->department_name_ja,
                "department_name_en" => $this->department_name_en,
                "survey_quizzes" => $quiz_arr, //TMP
                "tags" => TagResource::collection($this->tags)
            ];//chua update constants vi chua co constants
        }

        return [
            constants::SURVEY['KEY_ID'] => $this->id,
            constants::SURVEY['KEY_TITLE'] => $this->title,
            "survey_department_id" => $this->department_id,
            "survey_department_name" => $this->department_name,
            "survey_class_id" => $this->class_id,
            "survey_class_name" => $this->class_name,
            "survey_display" => $this->display,
            "survey_quizzes" => QuizResource::collection($this->quizzes),
        ];
    }
}
