<?php

namespace App\Http\Resources;
use App\constants;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if((!empty($this->quiz_title) && !empty($this->quiz_explain_correct)))
        {
            return [
                constants::QUIZ['KEY_ID'] => $this->id,
                constants::QUIZ['KEY_TITLE'] => $this->quiz_title,
                constants::QUIZ['KEY_SENTENCE'] => $this->quiz_sentence,
                constants::QUIZ['KEY_PICTURE'] => $this->quiz_picture,
                constants::QUIZ['KEY_EXPLAIN_CORRECT'] => $this->quiz_explain_correct,
                constants::QUIZ['KEY_EXPLAIN_INCORRECT'] => $this->quiz_explain_incorrect,
                constants::QUIZ['KEY_OPTIONS'] => OptionResource::collection($this->options)
            ];
        }

        if(empty($this->title))
        {
            return [
                constants::QUIZ['KEY_ID'] => $this->id,
            ];
        }
        else 
        {
            return [
                constants::QUIZ['KEY_ID'] => $this->id,
                constants::QUIZ['KEY_TITLE'] => $this->title
            ];
        }

        return [
            constants::QUIZ['KEY_ID'] => $this->id,
            constants::QUIZ['KEY_TITLE'] => $this->title,
        ];
    }
}
