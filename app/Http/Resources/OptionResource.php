<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\constants;
class OptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            constants::OPTIONS["KEY_ID"] => $this->option_id,
            constants::OPTIONS["KEY_NAME"] => $this->option_name,
            constants::OPTIONS["KEY_CORRECT"] => $this->option_correct,
            constants::OPTIONS["KEY_POSITION"] => $this->option_position,
        ];
    }
}
