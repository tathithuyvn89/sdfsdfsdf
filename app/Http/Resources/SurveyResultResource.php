<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\constants;
class SurveyResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(!empty($this->title_ja) && empty($this->title_en))
        {
            return [
                "answered_survey_id" => $this->id,
                constants::USER['KEY_USER_ID'] => $this->user_id,
                constants::SURVEY['KEY_TITLE'] => $this->title_ja,
                constants::SURVEY['KEY_ID'] => $this->survey_id,
                constants::USER['KEY_USER_NAME'] => $this->name,
                "answered_survey_result" => $this->result,
                "answered_survey_current_result" => $this->current_result,
                "answered_survey_total_quizzes" => $this->total_quizzes,
                "answered_survey_first_submit" => date_format($this->created_at, 'd-m-Y')
            ];

        }
        if(empty($this->title_ja) && !empty($this->title_en))
        {
            return [
                "answered_survey_id" => $this->id,
                constants::USER['KEY_USER_ID'] => $this->user_id,
                constants::SURVEY['KEY_TITLE'] => $this->title_en,
                constants::SURVEY['KEY_ID'] => $this->survey_id,
                constants::USER['KEY_USER_NAME'] => $this->name,
                "answered_survey_result" => $this->result,
                "answered_survey_current_result" => $this->current_result,
                "answered_survey_total_quizzes" => $this->total_quizzes,
                "answered_survey_first_submit" => date_format($this->created_at, 'd-m-Y')
            ];
        }
    }
}