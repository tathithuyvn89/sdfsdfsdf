<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Laravue\Models\AnsweredSurvey;
use App\Laravue\Models\AnsweredQuiz;
use Illuminate\Support\Arr;
use App\constants;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyResultResource;
use App\Http\Resources\UserResource;
use App\Laravue\Models\Survey;
use App\Laravue\Models\Quiz;
use App\Laravue\Models\Option;
use App\Laravue\Models\User;
class SurveyResultController extends Controller
{
    const ITEM_PER_PAGE = 10;
    const JapanSurveyLanguage =  [
        "answered_surveys.id",
        "respondent_id",
        "survey_id",
        "result",
        "current_result",
        "total_quizzes",
        "answered_surveys.created_at",
        "users.id as user_id",
        "users.name",
        "surveys.title_ja",
        "surveys.id as survey_id"
    ];
    const EngSurveyLanguage = [
        "answered_surveys.id",
        "respondent_id",
        "survey_id",
        "result",
        "current_result",
        "total_quizzes",
        "answered_surveys.created_at",
        "users.id as user_id",
        "users.name",
        "surveys.title_en",
        "surveys.id as survey_id"
    ];

    const BothLanguage = [
        "answered_surveys.id",
        "respondent_id",
        "survey_id",
        "result",
        "current_result",
        "total_quizzes",
        "answered_surveys.created_at",
        "users.id as user_id",
        "users.name",
        "surveys.title_ja",
        "surveys.title_en",
        "surveys.id"
    ];
    private function arrayOptions2String($raw){
        $result = "";
        for($i = 0; $i < count($raw); $i++){
            $position = Option::where('id',$raw[$i]['option_id'])->get();
            $result=$result.$position[0]['position'];
            $result=$result.",";
        }
        $result = rtrim($result, ", ");
        return $result;
    }

    private function arrayObject2array($ArrayObject){
        $ArrayObject = array_map(function($object){
            return $object['stringOption'];
        }, $ArrayObject);
        return $ArrayObject;
    }

    public function GetSurveyResult(Request $request)
    {
        $languages = Arr::get($request->all(),'la', '');
        $all_unique_survey = AnsweredSurvey::select('survey_id')->distinct()->get();
        $result = [];
        for ($i=0; $i < count($all_unique_survey); $i++)
        {
            $survey_id = $all_unique_survey[$i]['survey_id'];
            $survey_name = '';
            if($languages == 'en'){
                $survey_name = Survey::where('id',$survey_id)->select('surveys.title_en')->get()[0]['title_en'];
            }else if($languages == 'ja'){
                $survey_name = Survey::where('id',$survey_id)->select('surveys.title_ja')->get()[0]['title_ja'];
            }else{
                return response()->json(["message" => "There is no language "],400);
            }
            $respondent_count = AnsweredSurvey::where('survey_id',$survey_id)->select('respondent_id')->distinct()->get()->count();
            $complete_respondent_count = AnsweredSurvey::where('survey_id',$survey_id)->where('result',-1)->select('respondent_id')->distinct()->get()->count();
            $completed_count = AnsweredSurvey::where('survey_id',$survey_id)->where('result',-1)->get()->count();
            if($complete_respondent_count != 0){
                $average_respondence_count = round($completed_count/$complete_respondent_count,2);
            }else{
                $average_respondence_count = 0;
            }
            if($respondent_count != 0){
                $completed_rate = round($complete_respondent_count/$respondent_count,2)*100;
            }else{
                $completed_rate = 0;
            }
            array_push($result,[
                'survey_id' => $survey_id,
                'survey_name' => $survey_name,
                'respondent_count' => $respondent_count,
                'complete_respondent_count' => $complete_respondent_count,
                'completed_rate' => $completed_rate,
                'completed_count' => $completed_count,
                'average_respondence_count' => $average_respondence_count,
            ]);
        }
        return $result;
    }

    public function GetSurveyResultDetail(Request $request) {
        $languages = Arr::get($request->all(),'la', '');
        $quiz_lang_key = '';
        if($languages == 'en'){
            $quiz_lang_key =  'quizzes.title_en as quiz_title';
        }else if($languages == 'ja'){
            $quiz_lang_key =  'quizzes.title_ja as quiz_title';
        }else{
            return response()->json(["message" => "There is no language "],400);
        }
        $survey_id = Arr::get($request->all(),'survey_id', '');

        $query_answered_quizzes = AnsweredQuiz::query();
        $query_answered_quizzes->join('answered_surveys','answered_quizzes.answered_survey_id','answered_surveys.id')
                            ->join('quizzes','answered_quizzes.quiz_id','quizzes.id')
                            ->where('answered_surveys.survey_id',$survey_id)
                            ->with(['answered_options'=> function($query_answered_quizzes) {
                                $query_answered_quizzes->select(['answered_quiz_id','answered_options.option_id'])->orderBy('option_id', 'ASC');
                            }])
                            ->select([
                                'answered_quizzes.id',
                                'answered_quizzes.quiz_id',
                                $quiz_lang_key,
                                'answered_quizzes.correct',
                                'answered_quizzes.times',
                            ]);

        $unique_quizzes_in_answered_quizzes = clone $query_answered_quizzes;
        $unique_quizzes_in_answered_quizzes = $unique_quizzes_in_answered_quizzes->select(['answered_quizzes.quiz_id'])->distinct()->get();
        $answered_quizzes = clone $query_answered_quizzes;
        $result = [];
        for($i = 0; $i < count($unique_quizzes_in_answered_quizzes); $i++ )
        {
            $list_answered_of_an_answered_quizzes = clone $query_answered_quizzes;
            $list_answered_of_an_answered_quizzes = $list_answered_of_an_answered_quizzes->where('answered_quizzes.quiz_id',$unique_quizzes_in_answered_quizzes[$i]['quiz_id'])->get();
            $quiz_id = $list_answered_of_an_answered_quizzes[0]['quiz_id'];
            $quiz_title = $list_answered_of_an_answered_quizzes[0]['quiz_title'];
            $count_number_an_answered_quizzes = count($list_answered_of_an_answered_quizzes);
            $count_number_answered_quiz_without_mistake = 0;
            $count_number_mistake_of_an_answered_quiz = 0;
            $array_compare_answered_options_of_an_quiz = [];
            $max_number_incorrect_appear_of_an_option = 0;
            $current_incorrect_appear_of_an_option = "";
            for($j = 0; $j < $count_number_an_answered_quizzes; $j++){
                if($list_answered_of_an_answered_quizzes[$j]['correct'] == 1 && $list_answered_of_an_answered_quizzes[$j]['times'] == 1)
                {
                    $count_number_answered_quiz_without_mistake++;
                }else if($list_answered_of_an_answered_quizzes[$j]['correct'] == 0){
                    $count_number_mistake_of_an_answered_quiz++;
                    if(count($array_compare_answered_options_of_an_quiz) < 1){
                        $stringOptions = $this->arrayOptions2String($list_answered_of_an_answered_quizzes[$j]['answered_options']);
                        array_push($array_compare_answered_options_of_an_quiz,[
                            'stringOption' => $stringOptions,
                            'times' => 1
                        ]);
                        $max_number_incorrect_appear_of_an_option++;
                        $current_incorrect_appear_of_an_option = $stringOptions;
                    }else
                    {

                        $stringOptions = $this->arrayOptions2String($list_answered_of_an_answered_quizzes[$j]['answered_options']);
                        if(in_array($stringOptions,$this->arrayObject2array($array_compare_answered_options_of_an_quiz)))
                        {
                            for ($k = 0; $k < count($array_compare_answered_options_of_an_quiz); $k++){
                                if($array_compare_answered_options_of_an_quiz[$k]['stringOption'] == $stringOptions)
                                {

                                    $array_compare_answered_options_of_an_quiz[$k]['times']++;
                                    if($array_compare_answered_options_of_an_quiz[$k]['times'] > $max_number_incorrect_appear_of_an_option)
                                    {
                                        $max_number_incorrect_appear_of_an_option = $array_compare_answered_options_of_an_quiz[$k]['times'];
                                        $current_incorrect_appear_of_an_option = $stringOptions;
                                    }
                                }
                            }
                        }else{
                            array_push($array_compare_answered_options_of_an_quiz,[
                                'stringOption' => $stringOptions,
                                'times' => 1
                            ]);
                        }
                    }
                }
            }
            array_push($result,[
                'quiz_id' => $quiz_id,
                'quiz_title' => $quiz_title,
                'correct_rate' => round($count_number_answered_quiz_without_mistake/$count_number_an_answered_quizzes,2)*100,
                'average_mistake_count' => round($count_number_mistake_of_an_answered_quiz/$count_number_an_answered_quizzes,2),
                'frequent_incorrect_answers' => $current_incorrect_appear_of_an_option
            ]);
        }
        return $result;
    }



    public function GetSurveyName(Request $request)
    {
        $languages = Arr::get($request->all(),'la', '');
        $query = AnsweredSurvey::query();
        $query->distinct();
        $query->join('surveys','survey_id','=','surveys.id');
        if($languages=="ja")
        {
            $result = $query->get(['surveys.id','surveys.title_ja as title']);
        }
        else
            $result = $query->get(['surveys.id','surveys.title_en as title']);
        return response()->json($result);
    }

    public function GetRespondentName(Request $request)
    {
        $query = AnsweredSurvey::query();
        $query->distinct();
        $query->join('users','users.id','=','answered_surveys.respondent_id');
        $result = $query->get(['users.id','users.name']);
        error_log(json_encode($result));
        return response()->json($result);
    }
}
