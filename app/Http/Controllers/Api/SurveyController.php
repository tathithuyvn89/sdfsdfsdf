<?php

namespace App\Http\Controllers\API;

use App\constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyResource;
use App\Laravue\Models\Quiz;
use App\Laravue\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use App\Laravue\Models\Tag;
use Illuminate\Support\Facades\Storage;
use App\InformationProvider;
class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    const ITEM_PER_PAGE = 10;

    const BOTH_LANGUAGES_SURVEY = [
        'surveys.id',
        "surveys.title_ja",
        "surveys.title_en",
        "display",
        "status",
        "correct_pic",
        "incorrect_pic",
        "completemessage_ja",
        "completemessage_en",
        "classses.id as class_id",
        "classses.name_ja as class_name_ja",
        "classses.name_en as class_name_en",
        "departments.id as department_id",
        "departments.name_ja as department_name_ja",
        "departments.name_en as department_name_en"
    ];

    const SURVEY_JA_LANGUAGES = [
        'surveys.id',
        "title_ja as title",
        "display",
        "status",
        "correct_pic",
        "incorrect_pic",
        "completemessage_ja as completemessage",
        "classses.id as class_id",
        "classses.name_ja as class_name",
        "departments.id as department_id",
        "departments.name_ja as department_name"
    ];

    const SURVEY_ENG_LANGUAGES = [
        'surveys.id',
        "title_en as title",
        "display",
        "status",
        "correct_pic",
        "incorrect_pic",
        "completemessage_en as completemessage",
        "classses.id as class_id",
        "classses.name_en as class_name",
        "departments.id as department_id",
        "departments.name_en as department_name"
    ];

    const QUIZ_JA_LANGUAGES = [
        'quizzes.id',
        'quizzes.title_ja as title'
    ];

    const QUIZ_EN_LANGUAGES = [
        'quizzes.id',
        'title_en as title'
    ];

    const QUIZ_BOTH_LANGUAGES = [
        'quizzes.id',
        'quizzes.title_en',
        'quizzes.title_ja'
    ];

    const TAG_EN_LANGUAGES = [
        'tags.id',
        'tags.name_en as name'
    ];
    const TAG_JA_LANGUAGES = [
        'tags.id',
        'tags.name_ja as name'
    ];
    const TAG_BOTH_LANGUAGES = [
        'tags.id',
        'tags.name_en',
        'tags.name_ja'
    ];

    const BOTH_LANGUAGES_OPTION = ['id', 'option_ja','option_en','correct', 'position','quiz_id'];
    const JAPAN_OPTION_LANGUAGES = [
        'options.id', 'option_ja as option', 'position','quiz_id'
    ];

    const ENG_OPTION_LANGUAGES = [
        'options.id', 'option_en as option', 'position','quiz_id'
    ];

    const ENG_OPTION_LANGUAGES_LESS = [
        'options.id', 'option_en as option','quiz_id'
    ];

    const JA_OPTION_LANGUAGES_LESS = [
        'options.id', 'option_ja as option','quiz_id'
    ];
    public function index(Request $request)
    {
        $languages = Arr::get($request->all(), 'la', ''); //get language from param
        $department = Arr::get($request->all(), 'department', '');
        $class = Arr::get($request->all(), 'class', '');
        $display = Arr::get($request->all(), 'display', '');
        $title = Arr::get($request->all(), 'title', '');
        $tags = Arr::get($request->all(), 'tag', '');
        if($languages == "ja") //select languages for survey, quiz, options of quizzes, ja= Japan
        {
            $languages_survey= $languages_survey =  [
                'surveys.id',
                "title_ja as title",
                "display",
                "classses.id as class_id",
                "classses.name_ja as class_name",
                "departments.id as department_id",
                "departments.name_ja as department_name"
            ];
            $languages_quizz = static::QUIZ_JA_LANGUAGES;
        }
        else if($languages=="en"){ //en= English
            $languages_survey =  [
                'surveys.id',
                "title_en as title",
                "display",
                "classses.id as class_id",
                "classses.name_en as class_name",
                "departments.id as department_id",
                "departments.name_en as department_name"
            ];
            $languages_quizz = static::QUIZ_EN_LANGUAGES;
        }
        else {
           return response()->json(["message" => "Please Choose a language. la=en/la=ja"]);
        }
        $query = Survey::query();
        $query->distinct();
        $query->with([
            'quizzes' => function ($query) use($languages_quizz) {
                $query->select($languages_quizz);
            }
        ])->join('classses','surveys.classs_id','=','classses.id')
        ->join('departments','surveys.department_id','=','departments.id');
        if(!empty($department) && $department!= null)
        {
            $query->where('surveys.department_id',$department);
        }
        if(!empty($class) && $class!= null)
        {
            $query->where('surveys.classs_id',$class);
        }
        if($display!=null)
        {
            $query->where('display',$display);
        }
        if(!empty($title))
        {
            $query->where(function($query) use($title) {
                $query->where('title_en','LIKE', '%' . $title . '%');
                $query->orWhere('title_ja','LIKE', '%' . $title . '%');
            });
        }
        if(!empty($tags))
        {
                $query->join('survey_tag','surveys.id','=','survey_tag.survey_id');
                $query->whereIn('survey_tag.tag_id',$tags);
                if($languages == "ja") //select languages for survey, quiz, options of quizzes, ja= Japan
                {
                    //avoid to config database  strict = false, pls let it true. 
                    $query->groupBy([ 'surveys.id', "title_ja", "display", "status", "correct_pic",
                        "incorrect_pic",
                        "completemessage_ja",
                        "classses.id",
                        "classses.name_ja",
                        "departments.id",
                        "departments.name_ja"
                    ]);
                }
                else if($languages == "en")
                {
                    $query->groupBy(['surveys.id', "title_en", "display", "status", "correct_pic",
                        "incorrect_pic",
                        "completemessage_en",
                        "classses.id",
                        "classses.name_en",
                        "departments.id",
                        "departments.name_en"
                    ]);
                }
                $query->havingRaw('count(*) = '.count($tags));
        }
        return SurveyResource::collection($query->paginate(static::ITEM_PER_PAGE,$languages_survey));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       if($this->validate_survey($request)) return response()->json(["message"=>"Record Faild Validate"]);
       try
       {
            $pathImage_ja = $request->file('correct_pic'); //get file image from req
            $pathImage_en = $request->file('incorrect_pic');  //get file image from req

            $path_correct = $pathImage_ja->store('public/images'); //store image to storage/public/images

            $path_incorrect = $pathImage_en->store('public/images'); //store image to storage/public/images

            $path_correct = str_replace('public/','',$path_correct); //store method will reutnr "public/images/abc.png" so, we have to replace "Pulbic from
            $path_incorrect = str_replace('public/','',$path_incorrect);

            $survey = Survey::create([
                "title_ja" =>   $request->title_ja,
                "title_en"	=>  $request->title_en,
                "display"	=>  1,
                "status"	=>  $request->status,
                "correct_pic"=> $path_correct,
                "incorrect_pic"=>	    $path_incorrect,
                "completemessage_ja" =>	$request->completemessage_ja,
                "completemessage_en"=>  $request->completemessage_en,
                "department_id"	=>      $request->department_id,
                "classs_id"	=>          $request->classs_id,
                "admin_created" =>      $request->user_id
            ]);

            $tag_id_list = [];
            if(!empty($request->tag_list))
                $tag_id_list = Tag::whereIn('id', $request->tag_list)->get('id'); //get id by name in tags list

            if(count($tag_id_list)>0)
                $survey->tags()->attach($tag_id_list);

            $survey->quizzes()->attach($request->quizz_id_list);

            $count = $survey->count();
            return  $this->return_page_infor($request->languages,(ceil($count/10)),'create');
        }
        catch(\Exception $e)
        {
            $survey->delete();
            return response()->json([
                "message"=>"Record Faild"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $survey_detail = Survey::where('surveys.id',$id)->with([
            'quizzes' => function ($query) {
                $query->select(['quizzes.id as id']);
            },
            'tags' => function ($query) {
                $query->select(static::TAG_BOTH_LANGUAGES);
            }
        ])->join('classses','surveys.classs_id','=','classses.id')
        ->join('departments','surveys.department_id','=','departments.id')
        ->first(static::BOTH_LANGUAGES_SURVEY);

        if(empty($survey_detail))
        {
            return response()->json(["message" => "Can not found"]);//not match any quizz
        }
        return new SurveyResource($survey_detail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Survey $survey)
    {
        if($this->validate_survey($request)) //This function will using validator to validate input
            return response()->json(["message" => "Validate Faild"]);

        $path_correct = $survey->correct_pic; //Get the current picture of correct and incorrect.
        $path_incorrect = $survey->incorrect_pic;

        if($request->file('correct_pic') != null) //if have new picture, delete current picture and store new picture in storage.
        {
            $path_correct = $request->file('correct_pic');
            $path_correct = $path_correct->store('public/images');
            Storage::delete(['public/'.$survey->correct_pic]);
        }

        if($request->file('incorrect_pic') != null) //the same as correct picture.
        {
            $path_incorrect = $request->file('incorrect_pic');
            $path_incorrect = $path_incorrect->store('public/images'); //=> return "public/images/abc.png"
            Storage::delete(['public/'.$survey->incorrect_pic]);
        }
        $path_correct = str_replace('public/','',$path_correct); //remove "public/" from result of :store()
        $path_incorrect = str_replace('public/','',$path_incorrect);//remove "public/" from result of :store() => to record in DB => ex: images/abc.png
        //to record in DB

        if(!$survey->update([ //update new information of survey
            "title_ja" => $request->title_ja,
            "title_en" => $request->title_en,
            "display" => $request->display,
            "status"   => $request->status,
            "correct_pic" => $path_correct,
            "incorrect_pic" => $path_incorrect,
            "completemessage_ja" => $request->completemessage_ja,
            "completemessage_en" => $request->completemessage_en,
            "department_id" => $request->department_id,
            "classs_id" =>$request->classs_id
        ])) return response()->json(["message" => "Update Faild"]);

        $tag_id_list = [];
        if(!empty($request->tag_list))
            $tag_id_list = Tag::whereIn('id', $request->tag_list)->get('id'); //get id by name in tags list
            $survey->tags()->sync($tag_id_list); //sync the new tags id list to db.
        $quiz_list = Quiz::whereIn('id',$request->quizz_id_list)->count(); //check list quizz from request, if this list have 5 quizzes suitable to add for survey
        if($quiz_list==0)
        {
            $this->return_page_infor($request->languages,$request->current_page,'update'); //sync new quizzes id list to db for survey
        }
        $survey->quizzes()->sync($request->quizz_id_list); 
        return $this->return_page_infor($request->languages,$request->current_page,'update');//return current page information if update successfully.
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function forcedelete(Request $request)
    {
        $avalible_to_delete = [];
        $can_not_delete = [];
        $surveys = Survey::whereIn('id',$request->id_list)->get();
        for($i=0; $i<count($surveys); $i++)
        {
            try{
                if($surveys[$i]->delete())
                {
                    Storage::delete(['public/'.$surveys[$i]->correct_pic,'public/'.$surveys[$i]->incorrect_pic]);
                    array_push($avalible_to_delete,$surveys[$i]['id']);
                }
            }
            catch(\Exception $e)
            {
                array_push($can_not_delete,$surveys[$i]['id']);
            }
        }
        if(count($avalible_to_delete) == 0) return response($this->return_page_infor($request->languages,$request->current_page,'delete',[$avalible_to_delete,$can_not_delete,"error","errorPage.errorSurveyHasResponse"]),500);
        return response($this->return_page_infor($request->languages,$request->current_page,'delete',[$avalible_to_delete,$can_not_delete,"message","Delete Successfully"]),200);
    }

    public function Get_Quizz_List_Tags_Departments_Classes_Survey_Detail(Request $request)
    {
        $InformationProvider = new InformationProvider();
        return response()->json([
            "quizzes_list" => $InformationProvider->Get_Quizzes_List($request),
            "Informations" => $InformationProvider->Get_Tags_Departments_Classes($request)//pls check this method in InformationProvider class
            //it will return "tags list information, departments, classes, quizzes list" to fill for select box on UI of surver and searching of respondent UI.
        ]);
    }

    private function validate_survey($request)
    {
        $Validater = Validator::make($request->all(),[
            'title_ja'=> '|required|max:255',
            'title_en' =>	'|required|max:255',
            'display' =>'boolean',
            'status'	=>'|required|boolean',
            'correct_pic'=>'image:jpeg,jpg,png,gif|max:10000',
            'incorrect_pic'	=>'image:jpeg,jpg,png,gif|max:10000',
            'completemessage_ja'=>	'|required|max:255',
            'completemessage_en'	=>'|required|max:255',
            'department_id'	=>'|required|integer',
            'classs_id' => '|required|integer',
            'quizz_id_list.*' => '|required|integer|',
            "tag_list.*" => 'integer'
        ]); //validate variables to insert quizz to database
        if($Validater->fails()) return true;
        else return false;
    }

    private function return_page_infor($languages,$current_page,$method,$optional = null)//return page infor when Update, create and delete.
    {
        if($languages == "ja")
        {
            $languages_survey= static::SURVEY_JA_LANGUAGES;
            $languages_quizz = static::QUIZ_JA_LANGUAGES;
            $languages_tag = static::TAG_JA_LANGUAGES;
            $languages_options = static::JAPAN_OPTION_LANGUAGES;
        }
        else if($languages=="en"){
            $languages_survey = static::SURVEY_ENG_LANGUAGES;
            $languages_quizz = static::QUIZ_EN_LANGUAGES;
            $languages_tag = static::TAG_EN_LANGUAGES;
            $languages_options = static::ENG_OPTION_LANGUAGES;
        }
        else {
            $languages_survey = static::BOTH_LANGUAGES_SURVEY;
            $languages_quizz = static::QUIZ_BOTH_LANGUAGES;
            $languages_tag = static::TAG_BOTH_LANGUAGES;
            $languages_options = static::BOTH_LANGUAGES_OPTION;
        }

        $query = Survey::query();

        $query->with([
            'quizzes' => function ($query) use($languages_quizz) {
                $query->select($languages_quizz);
            },
            'quizzes.options' => function ($query) use($languages_options) {
                $query->select($languages_options);
            },
            'tags' => function ($query) use($languages_tag) {
                $query->select($languages_tag);
            }
        ]);

        $query->join('classses','surveys.classs_id','=','classses.id');
        $query->join('departments','surveys.department_id','=','departments.id');

        if($method == 'delete')
        {
            $datas = json_decode(json_encode($query->paginate(static::ITEM_PER_PAGE,$languages_survey,'page',$current_page)));
            return [
                $optional[2] => $optional[3],
                'deleted' => $optional[0],
                'delete_faild' => $optional[1],
                'data'=> SurveyResource::collection($datas->data),
                'links' => [
                    'first' => $datas->first_page_url,
                    'last' => $datas->last_page_url,
                    "next" => $datas->next_page_url,
                    "prev" => $datas->prev_page_url
                ],
                'meta' => [
                    'current_page' => $datas->current_page,
                    'from' => $datas->from,
                    'last_page' => $datas->last_page,
                    'path' => $datas->path,
                    'per_page' => $datas->per_page,
                    'to' =>  $datas->to,
                    'total' => $datas->total
                ], 
            ];
        }
        if($current_page!="params")
        return SurveyResource::collection($query->paginate(static::ITEM_PER_PAGE,$languages_survey,'page',$current_page));
        else
        return SurveyResource::collection($query->paginate(static::ITEM_PER_PAGE,$languages_survey));
    }
}