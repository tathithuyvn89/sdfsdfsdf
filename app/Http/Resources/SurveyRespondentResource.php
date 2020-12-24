<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Laravue\Models\AnsweredSurvey;
use App\constants;
class SurveyRespondentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $rs = 0;
        if(!empty($this->answered_surveys) && $this->answered_surveys != null) $rs = $this->answered_surveys->updated_at->format('Y-m-d H:i');
        if(empty($this->survey_complete_message))
        {
            if(empty($this->survey_title_en))
            {
                return [
                    constants::SURVEY['KEY_ID'] => $this->id,
                    constants::SURVEY['KEY_TITLE'] => $this->survey_title,
                    constants::SURVEY['KEY_STATUS'] => $this->survey_status,
                    constants::SURVEY['KEY_RESULT'] => $rs
                ];
            }
            else
            return [
                "survey_id" => $this->id,
                "survey_title_ja" => $this->survey_title_ja,
                "survey_title_en" => $this->survey_title_en,
                "survey_status" => $this->survey_status,
                "survey_result" => $rs
            ];
        }
        return [
            constants::SURVEY['KEY_ID'] => $this->id,
            constants::SURVEY['KEY_TITLE'] => $this->survey_title,
            constants::SURVEY['KEY_STATUS'] => $this->survey_status,
            constants::SURVEY['KEY_COMPLETE_MESSAGE'] => $this->survey_complete_message,
            constants::SURVEY['KEY_CORRECT_PICTURE'] => $this->survey_correct_picture,
            constants::SURVEY['KEY_INCORRECT_PICTURE'] => $this->survey_incorrect_picture,
            constants::SURVEY["KEY_RESULT"] => $rs,
            "quizzes" => QuizResource::collection($this->quizzes),
        ];
    }
}
