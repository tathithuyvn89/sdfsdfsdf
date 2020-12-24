<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyRespondentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Laravue\Models\Survey;
use App\Laravue\Models\AnsweredSurvey;
use App\Laravue\Models\AnsweredQuiz;
use App\Laravue\Models\AnsweredOption;
use App\constants;
use Illuminate\Support\Facades\Validator;
//use App\Providers\InformationProvider;
use App\InformationProvider;


class RespondentController extends Controller
{   
    const ITEM_PER_PAGE = 10;
    const ENG_SURVEY_LIST = [
        'surveys.id',
        'surveys.title_en as '.constants::SURVEY['KEY_TITLE'],
        'status as '.constants::SURVEY['KEY_STATUS'],
    ];

    const JA_SURVEY_LIST = [
        'surveys.id',
        'surveys.title_ja as '.constants::SURVEY['KEY_TITLE'],
        'status as '.constants::SURVEY['KEY_STATUS'],
    ];

    const BOTH_SURVEY_LIST = [
        'surveys.id',
        'surveys.title_en as survey_title_en',
        'surveys.title_ja as survey_title_ja',
        'status as '.constants::SURVEY['KEY_STATUS'],
    ];

    const ENG_SURVEY = [
        'surveys.id',// as '.constants::SURVEY['KEY_ID'],
        'surveys.title_en as '.constants::SURVEY['KEY_TITLE'],
        'status as '.constants::SURVEY['KEY_STATUS'],
        'completemessage_en as '.constants::SURVEY['KEY_COMPLETE_MESSAGE'],
        'correct_pic as '.constants::SURVEY['KEY_CORRECT_PICTURE'],
        'incorrect_pic as '.constants::SURVEY['KEY_INCORRECT_PICTURE'],
    ];

    const JA_SURVEY = [
        'surveys.id',// as '.constants::SURVEY['KEY_ID'],
        'surveys.title_ja as '.constants::SURVEY['KEY_TITLE'],
        'status as '.constants::SURVEY['KEY_STATUS'],
        'completemessage_ja as '.constants::SURVEY['KEY_COMPLETE_MESSAGE'],
        'correct_pic as '.constants::SURVEY['KEY_CORRECT_PICTURE'],
        'incorrect_pic as '.constants::SURVEY['KEY_INCORRECT_PICTURE'],
    ];

    const BOTH_SURVEY = [
        'surveys.id',
        'surveys.title_en as survey_title_en',
        'surveys.title_ja as survey_title_ja',
        'status as '.constants::SURVEY['KEY_STATUS'],
        'completemessage_ja as survey_complete_message_ja',
        'completemessage_en as survey_complete_message_en',
        'correct_pic as '.constants::SURVEY['KEY_CORRECT_PICTURE'],
        'incorrect_pic as '.constants::SURVEY['KEY_INCORRECT_PICTURE'],
    ];
    const BOTH_LANGUAGES_QUIZZ = [
        'quizzes.id',
        'title_ja as quiz_title_ja',
        'title_en as quiz_title_en',
        'sentence_ja as quiz_sentence_ja',
        'sentence_en as quiz_sentence_en',
        'picture_ja as quiz_picture_ja',
        'picture_en as quiz_picture_en',
        'explain_correct_ja as quiz_explain_correct_ja',
        'explain_correct_en as quiz_explain_correct_en',
        'explain_incorrect_ja as quiz_explain_incorrect_ja',
        'explain_incorrect_en as quiz_explain_incorrect_en'
    ];
    // I have to make select 'as' because the result of paginate when passing to QuizzResource and response()->json() can not response page infor.
    // select 'as' help me to avoid to using resource but I still customize about the key of data.
    const JAPAN_QUIZZ_LANGUAGES = [
        'quizzes.id',
        'quizzes.title_ja as '.constants::QUIZ['KEY_TITLE'],
        'sentence_ja as '.constants::QUIZ['KEY_SENTENCE'],
        'picture_ja as '.constants::QUIZ['KEY_PICTURE'],
        'explain_correct_ja as '.constants::QUIZ['KEY_EXPLAIN_CORRECT'],
        'explain_incorrect_ja as '.constants::QUIZ['KEY_EXPLAIN_INCORRECT'],
    ];

    const ENG_QUIZZ_LANGUAGES = [
        'quizzes.id',
        'quizzes.title_en as '.constants::QUIZ['KEY_TITLE'],
        'sentence_en as '.constants::QUIZ['KEY_SENTENCE'],
        'picture_en as '.constants::QUIZ['KEY_PICTURE'],
        'explain_correct_en as '.constants::QUIZ['KEY_EXPLAIN_CORRECT'],
        'explain_incorrect_en as '.constants::QUIZ['KEY_EXPLAIN_INCORRECT'],
    ];

    const JAPAN_OPTION_LANGUAGES = [
        'id as '.constants::OPTIONS['KEY_ID'], 
        'option_ja as '.constants::OPTIONS['KEY_NAME'],
        'correct as '.constants::OPTIONS['KEY_CORRECT'], 
        'position as '.constants::OPTIONS['KEY_POSITION'],
        'quiz_id'
    ];

    const ENG_OPTION_LANGUAGES = [
        'options.id as '.constants::OPTIONS['KEY_ID'], 
        'option_en as '.constants::OPTIONS['KEY_NAME'],
        'correct as '.constants::OPTIONS['KEY_CORRECT'], 
        'position as '.constants::OPTIONS['KEY_POSITION'],
        'quiz_id'
    ];

    const BOTH_LANGUAGES_OPTION = [
        'options.id as option_id', 
        'option_ja as option_name_ja',
        'option_en as option_name_en',
        'correct as option_correct', 
        'position as option_position',
        'quiz_id'
    ];
    public function GetListSurveys(Request $request)
    {
        $languages = Arr::get($request->all(),constants::KEY_LANGUAGE, '');
        $department = Arr::get($request->all(),constants::DEPARTMENT['KEY_DEPARTMENTID'], '');
        $class = Arr::get($request->all(),constants::CLASSS['KEY_CLASSID'], '');
        $title = Arr::get($request->all(),constants::KEY_SEARCH_VALUE, '');
        $tags = $request->tags;
        $query = Survey::query();
        $query->distinct();
        $query->where('display',1);
        if($languages == "ja")
            $languages_survey= static::JA_SURVEY_LIST;
        else if($languages=="en")
            $languages_survey= static::ENG_SURVEY_LIST;
        else 
            $languages_survey= static::BOTH_SURVEY_LIST;
        if(!empty($department))
        {
            $query->join('departments','surveys.department_id','=','departments.id');
            $query->where('department_id',$department);
        }
        if(!empty($class))
        {
            $query->join('classses','surveys.classs_id','=','classses.id');
            $query->where('classs_id',$class);
        }
        if(!empty($tags) && $tags != null)
        {
            $query->join('survey_tag','survey_id','=','surveys.id');
            $query->whereIn('survey_tag.tag_id',$tags);
        }
        if(!empty($title))
        {
            $query->where(function($query) use($title) {
                $query->where('title_en','LIKE', '%' . $title . '%');
                $query->orWhere('title_ja','LIKE', '%' . $title . '%');
            });
        }
        $result = $query->with([
            'answered_surveys' => function($query) use($request){
                $query->select('result','total_quizzes','survey_id','updated_at')
                ->where('result', -1)->where('respondent_id',$request->user()->id)
                ->orWhereRaw('total_quizzes = result')->where('respondent_id',$request->user()->id);
        }]);
        $result = $query->get($languages_survey);
        SurveyRespondentResource::withoutWrapping();
        return SurveyRespondentResource::collection($result);
    }

    public function GetSurveyDetail(Request $request,$id)
    {
        $languages = Arr::get($request->all(), 'la', '');
        if($languages  == "ja")
        {
            $languages_survey= static::JA_SURVEY;
            $languages_quizz = static::JAPAN_QUIZZ_LANGUAGES;
            $languages_options = static::JAPAN_OPTION_LANGUAGES;
        }
        else if($languages  == "en"){
            $languages_survey= static::ENG_SURVEY;
            $languages_quizz = static::ENG_QUIZZ_LANGUAGES;
            $languages_options = static::ENG_OPTION_LANGUAGES;
        }
        else {
            $languages_survey= static::BOTH_SURVEY;
            $languages_quizz = static::BOTH_LANGUAGES_QUIZZ;
            $languages_options = static::BOTH_LANGUAGES_OPTION;
        }

        $survey_detail = Survey::where('surveys.id',$id)->with([
            'quizzes' => function ($query) use($languages_quizz) {
                $query->select($languages_quizz);
            },
            'quizzes.options' => function ($query) use($languages_options)
            {
                $query->select($languages_options)->orderBy('position', 'ASC');
            }
        ])->first($languages_survey);

        if(empty($survey_detail))
        {
            return response()->json(["message" => "Can not found"]);//not match any quizz
        }

        $survey_detail['user_id'] = $request->user_id;
        SurveyRespondentResource::withoutWrapping();
        return  new SurveyRespondentResource($survey_detail);
    }

    public function InsertResultSurvey(Request $request)
    {
        // return $request;
        $it =1 ;
        $its = $request->it;
        $options = $request->options; // get options from request
        if($this->validate_result($request)) return response()->json(["message"=>"Invalid data"],403);
        
        if($request->result == -1)
        {
            
            AnsweredSurvey::where('id',$request->id)->update([
                'result' => -1,
                'current_result'=> $request->total
            ]);
            
            return response()->json([
                "message"=>"Record successfully",
                "id" => $request->id
            ]);
        }
        else if($request->result <= 1)
        {
            
            try
            {
                if($request->times <2 && $it == $its){
                   
                    $answeredsurvey =  AnsweredSurvey::create([
                        "respondent_id"    => $request->user_id,
                        "survey_id"    => $request->survey_id,
                        'result' => $request->result,
                        'current_result' => $request->current_result,
                        'total_quizzes' => $request->total
                    ]);
                    
                    
                    $answeredquiz = AnsweredQuiz::create([
                        'quiz_id' => $request->quiz_id,
                        'answered_survey_id' => $answeredsurvey->id,
                        'correct' => $request->correct,
                        "times" => $request->times
                    ]);
                   
                    for ($i=0; $i < count($options); $i++) {
                        AnsweredOption::create([ // add option to answered_quizzes
                            "option_id" => $options[$i],
                            "answered_quiz_id" => $answeredquiz->id,                
                        ]);
                    }
                    return response()->json([
                        "message" => "Record successfully",
                        "id" => $answeredsurvey->id,
                        "answered_quiz_id" =>$answeredquiz->id,
                        "it" => 0,
                    ]); //if sucessfully -> response successfully
                }else{
                    AnsweredSurvey::where('id',$request->id)->update(['current_result'=>$request->current_result,
                                                                        'result'=>$request->current_result]);

                      $answeredquiz = AnsweredQuiz::create([
                        'quiz_id' => $request->quiz_id,
                        'answered_survey_id' => $request->id,
                        'correct' => $request->correct,
                        "times" => $request->times
                    ]);
                    for ($i=0; $i < count($options); $i++) {
                        AnsweredOption::create([ // add option to answered_quizzes
                            "option_id" => $options[$i],
                            "answered_quiz_id" => $answeredquiz->id,                
                        ]);
                    }
                    return response()->json([
                        "message" => "Record successfully",
                        // "infor_page" => $this->return_page_infor($request->languages,(ceil($count/10))),
                        "id" => $request->id,
                        "answered_quiz_id" =>$answeredquiz->id,
                    ]); //if sucessfully -> response successfully
                }
                
                
            }
            catch(\Exception $e)
            {
                return response()->json([
                    "message" => "Record Faild",
                    "error" => $e
                ],400); // response error
            }
        }
        else if($request->result >= 1)
        {
          AnsweredSurvey::where('id',$request->id)->update(['current_result'=>$request->result,
                                                            'result'=>$request->result]);
       
          $answeredquiz = AnsweredQuiz::create([
        'quiz_id' => $request->quiz_id,
        'answered_survey_id' => $request->id,
        'correct' => $request->correct,
        "times" => $request->times
             ]);
        for ($i=0; $i < count($options); $i++) {
        AnsweredOption::create([ // add option to answered_quizzes
        "option_id" => $options[$i],
        "answered_quiz_id" => $answeredquiz->id,                
          ]);
          }
            return response()->json([
                "message"=>"Record successfully",
                "id" => $request->id,
                "answered_quiz_id" =>$request->answered_quiz_id,
            ]);
        }
    }
    public function Get_Tags_Classes_Departments(Request $request)
    { 
        $InformationProvider = new InformationProvider();
        return $InformationProvider->Get_Tags_Departments_Classes($request);
    }

    private function validate_result($request)
    {
        $Validater = Validator::make($request->all(),[
            "survey_id"	=> '|required|integer',
            "result" => '|required|integer'
        ]); //validate variables to insert quizz to database
        if($Validater->fails())
            return true;
        else return false;
    }
}
