<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\constants;
class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if((!empty($this->name_ja) && !empty($this->name_en)))
        {
            return [
                "id" => $this->id,
                "name_ja" => $this->name_ja,
                "name_en" => $this->name_en
            ];
        }

        return [
            constants::DEPARTMENT['KEY_DEPARTMENTID'] => $this->id,
            constants::DEPARTMENT['KEY_DEPARTMENTNAME'] => $this->name
        ];
    }
}
