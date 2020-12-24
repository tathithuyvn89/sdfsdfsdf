<?php
namespace App;
use App\constants;
use Illuminate\Support\Arr;
use App\Http\Resources\ClassResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\TagResource;
use App\Laravue\Models\Tag;
use App\Laravue\Models\Department;
use App\Laravue\Models\Classs;
use App\Laravue\Models\Quiz;

class InformationProvider
{
    public function Get_Tags_Departments_Classes($request)
    {
        $tags_languages = [];
        $classes_languages = [];
        $departments_languages = [];
        $languages = Arr::get($request->all(), 'la', '');
        if($languages=="en")
        {
            $tags_languages = [
                "id",
                "name_en as name"
            ];
            $departments_languages = [
                "id",
                "name_en as name"
            ];
            $classes_languages = [
                "id",
                "name_en as name"
            ];
        } else if($languages == "ja")
        {
            $tags_languages = [
                "id",
                "name_ja as name"
            ];
            $departments_languages = [
                "id",
                "name_ja as name"
            ];
            $classes_languages = [
                "id" ,
                "name_ja as name"
            ];
        }
        else
        {
            return response()->json([
                "message" => "please, choose the language => la=en/la=ja"
            ]);
        }
        return [
            constants::TAG['KEY_TAG_LIST'] => TagResource::collection(Tag::all($tags_languages)),
            constants::DEPARTMENT['KEY_DEPARMENT_LIST'] => DepartmentResource::collection(Department::all($departments_languages)),
            constants::CLASSS['KEY_LIST_CLASS'] =>  ClassResource::collection(Classs::all($classes_languages))
        ];
    }

    public function Get_Quizzes_List($request)
    {
        $languages = Arr::get($request->all(), 'la', '');
    
        if($languages=="en")
        {
            return QuizResource::collection(Quiz::all(['id','title_en as title']));
        } 
        else if($languages == "ja")
        {
            return QuizResource::collection(Quiz::all(['id','title_ja as title']));
        }
        else return response()->json(["message" => "Please, Choose The languages"]);
    }
}
?>