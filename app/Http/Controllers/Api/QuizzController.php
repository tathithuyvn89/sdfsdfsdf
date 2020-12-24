<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Laravue\Models\Option;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Laravue\Models\Quiz;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuizzController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    const ITEM_PER_PAGE = 10;

    const BOTH_LANGUAGES = [
        'id',
        'title_ja',
        'title_en',
        'sentence_ja',
        'sentence_en',
        'picture_ja',
        'picture_en',
        'explain_correct_ja',
        'explain_correct_en',
        'explain_incorrect_ja',
        'explain_incorrect_en'
    ];
    // I have to make select 'as' because the result of paginate when passing to QuizzResource and response()->json() can not response page infor.
    // select 'as' help me to avoid to using resource but I still customize about the key of data.
    const BOTH_LANGUAGES_OPTION = ['id', 'option_ja','option_en','correct', 'position','quiz_id'];
    const JAPAN_QUIZZ_LANGUAGES = [
        'id',
        'title_ja as title',
        'sentence_ja as sentence',
        'picture_ja as picture',
        'explain_correct_ja as explain_correct',
        'explain_incorrect_ja as explain_incorrect',
    ];

    const ENG_QUIZZ_LANGUAGES = [
        'id',
        'title_en as title',
        'sentence_en as sentence',
        'picture_en as picture',
        'explain_correct_en as explain_correct',
        'explain_incorrect_en as explain_incorrect',
    ];

    const JAPAN_OPTION_LANGUAGES = [
        'id', 'option_ja as option', 'position','quiz_id'
    ];

    const ENG_OPTION_LANGUAGES = [
        'id', 'option_en as option', 'position','quiz_id'
    ];

    const ENG_OPTION_LANGUAGES_LESS = [
        'id', 'option_en as option','quiz_id'
    ];

    const JA_OPTION_LANGUAGES_LESS = [
        'id', 'option_ja as option','quiz_id'
    ];
    public function index(Request $request)
    {
        $languages = Arr::get($request->all(), 'la', '');

        $keyword= Arr::get($request->all(), 'keyword', '');

        $languages_quizz = [];
        $languages_option = [];

        if($languages == "ja")
        {
            $languages_quizz = static::JAPAN_QUIZZ_LANGUAGES;
            $languages_option = static::JA_OPTION_LANGUAGES_LESS;
        }
        else if($languages=="en"){
            $languages_quizz = static::ENG_QUIZZ_LANGUAGES;
            $languages_option = static::ENG_OPTION_LANGUAGES_LESS ;
        }
        else {
            $languages_quizz = static::BOTH_LANGUAGES;
            $languages_option = static::BOTH_LANGUAGES_OPTION;
        }

        $query = Quiz::query();
            $query->with([
                'options' => function ($query) use($languages_option) {
                    $query->select($languages_option)->orderBy('position', 'ASC');
                }
            ]);

        if(!empty($keyword))
        {
            $query->where('title_ja','LIKE', '%' . $keyword . '%');
            $query->orWhere('title_en','LIKE', '%' . $keyword . '%');
            return  $query->paginate(static::ITEM_PER_PAGE,$languages_quizz);
        }
        return  $query->paginate(static::ITEM_PER_PAGE,$languages_quizz);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($this->validate_quizz($request)) return response()->json(["message" => "Record Faild"],400); //validate failse => res => Faild
        //validate variables to insert quizz to database
        //all of attributes are required
        //img max vollum is 10mb, only type of image following jpeg,jpg,png,gif
        $path_je = null;
        $path_en = null;

        if($request->file('picture_ja') != null)
        {
                $pathImage_ja = $request->file('picture_ja');
                $path_je = $pathImage_ja->store('public/images');
                $path_je = str_replace('public/','',$path_je);
        }// now, get file from req

        if($request->file('picture_en') != null)
        {
                $pathImage_en = $request->file('picture_en');
                $path_en = $pathImage_en->store('public/images');//
                $path_en = str_replace('public/','',$path_en);
        } //  // let create file and id for this image, this will return uploads/images/{id}.{jpeg,jpg,png,gif}

        $Quizz = Quiz::create([
            'title_ja'=> $request->title_ja,
            'title_en' =>	$request->title_en,
            'sentence_ja' =>$request->sentence_ja,
            'sentence_en'	=>$request->sentence_en,
            'picture_ja'=> $path_je,
            'picture_en'	=> $path_en,
            'explain_correct_ja'=>$request->explain_correct_ja,
            'explain_correct_en'	=>$request->explain_correct_en,
            'explain_incorrect_ja'	=>$request->explain_incorrect_ja,
            'explain_incorrect_en'	=>$request->explain_incorrect_en,
            'admin_created' => $request->user_id
        ]); //create new quizz and get the id of this

        $id = $Quizz->id;
        $count = $Quizz->count();
        $options = $request->options; //get options of quiz fromr req

        try
        {
            for ($i=0; $i < count($options); $i++) {
            $options[$i]["quiz_id"] = $id; //add new quiz id to options to prepare for create options for quiz,
            // because can not get the quiz_id after this created
            }
            Option::insert($options);
            return response()->json([
                "message" => "Record successfully",
                "infor_page" => $this->return_page_infor($request->languages,(ceil($count/10)))
            ],200); //if sucessfully -> response successfully
        }
        catch(\Exception $e)
        {
            $Quizz->delete();//delete current quizz if options are not succesfully to create
        }

        return response()->json(["message" => "Record Faild"],400); //res=> faild
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $languages = Arr::get($request->all(), 'la', ''); //get laguages from param
        $languages_quizz = [];
        $languages_option = [];

        if($languages == "ja")  //set languages
        {
            $languages_quizz = static::JAPAN_QUIZZ_LANGUAGES;
            $languages_option = static::JAPAN_OPTION_LANGUAGES;
        }
        else if($languages == "en")
        {
            $languages_quizz = static::ENG_QUIZZ_LANGUAGES;
            $languages_option = static::ENG_OPTION_LANGUAGES;
        }
        else {
            $languages_quizz = static::BOTH_LANGUAGES;
            $languages_option = static::BOTH_LANGUAGES_OPTION;
        }

        $quiz_detail =  Quiz::where('id',$id)->with([
            'options' => function ($query) use($languages_option) {
                $query->select($languages_option)->orderBy('position', 'ASC'); //select option order by position 1,2,3,4,n+1
            }
        ])->first($languages_quizz); //select quiz by languages

        if(empty($quiz_detail))
            return  response()->json(["message" => "Can not found"]); //not match any quizz
        else return  $quiz_detail;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Quiz $quizz)
    {
        if($this->validate_quizz($request) == true)
            return response()->json(["message" => "Record Faild"],400); //check valid input of quizz information
        $path_je = $quizz->picture_ja;
        $path_en = $quizz->picture_en;
            if($request->file('picture_ja') != null)
            {
                $pathImage_ja = $request->file('picture_ja');
                $path_je = $pathImage_ja->store('public/images');
                Storage::delete(['public/'.$quizz->picture_ja]);
            }// now, get file from req
            if($request->file('picture_en') != null)
            {
                $pathImage_en = $request->file('picture_en');
                $path_en = $pathImage_en->store('public/images');//
                Storage::delete(['public/'.$quizz->picture_en]);
            } //  // let create file and id for this image, this will return uploads/images/{id}.{jpeg,jpg,png,gif}
            $path_je = str_replace('public/','',$path_je);
            $path_en = str_replace('public/','',$path_en);
            if(!empty($request->updateIMG_ja) && $request->updateIMG_ja == 3)
            {
                Storage::delete(['public/'.$quizz->picture_ja]);
                $path_je = null;
            }
            if(!empty($request->updateIMG_en) && $request->updateIMG_en == 3)
            {
                Storage::delete(['public/'.$quizz->picture_en]);
                $path_en = null;
            }
            if(!$quizz->update([ // processing to edit new information for quizz
                "title_ja" => $request->title_ja,
                "title_en" => $request->title_en,
                "sentence_ja" => $request->sentence_ja,
                "sentence_en" => $request->sentence_en,
                "picture_ja" => $path_je, //image path
                "picture_en" => $path_en, //image path
                "explain_correct_ja" => $request->explain_correct_ja,
                "explain_correct_en" => $request->explain_correct_en,
                "explain_incorrect_ja" => $request->explain_incorrect_ja,
                "explain_incorrect_en" => $request->explain_incorrect_en,
            ])) return response()->json(["message" => "Edit Error"],200);
            $id_delete_list = [];
            $add_new_list = [];
            if(isset($request->options)) //if have any current options
            {
                if(!$this->validate_current_options($request)) //check validate for them
                { // check status one by one.
                    foreach ($request->options as $key => $value)
                    {
                        switch ($value['status']) { // 1 = new update, 0 = nothing change, -1 = delete this option
                            case 1:
                                unset($value['status']);
                                $quizz->options()->where('id',$value['id'])->update($value);
                                break;
                            case -1:
                                unset($value['status']);
                                array_push($id_delete_list,$value['id']); //push to array id, prepare to delete many rows
                            break;
                            case -2:
                                unset($value['status']);
                                $value['quiz_id'] = $quizz->id;
                                array_push($add_new_list,$value);
                            break;
                            default:
                                break;
                        }
                    }
                    $quizz->options()->whereIn('id',$id_delete_list)->delete(); //delete all of option whereIn id option
                    $quizz->options()->insert($add_new_list);
                } else  return response()->json(["message" => "Options Validate Faild"],400);
            }
            return response()->json([
                "message" => "Edit successfully",
                "page_infor" => $this->return_page_infor($request->languages,$request->current_page)
            ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function forcedelete(Request $request)
    {
        $error_message = "errorPage.errorQuizHasResponse";
        $avalible_to_delete = [];
        $can_not_delete = [];
        $Quiz = Quiz::whereIn('id',$request->id_list)->get();
        $quiz_in_survey = DB::table('survey_quiz')->whereIn('quiz_id',$request->id_list)->count();
        for($i = 0; $i < count($Quiz); $i++)
        {
            try{
                if($Quiz[$i]->delete())
                {
                    array_push($avalible_to_delete,$Quiz[$i]['id']);
                    Storage::delete(['public/'.$Quiz[$i]['picture_ja'],'public/'.$Quiz[$i]['picture_en']]);
                }
            }
            catch(\Exception $e)
            {
                array_push($can_not_delete,$Quiz[$i]['id']);
            }
        }
        if($quiz_in_survey > 0)
        {
            $error_message = "errorPage.errorQuizInSurvey";
        }
        if(count($avalible_to_delete) == 0)
        return response()->json([
            "error" => $error_message,//$error_message,
            "deleted" => $avalible_to_delete,
            "delete_faild" => $can_not_delete,
            "infor_page" => $this->return_page_infor($request->languages,$request->current_page)
        ],500);
        return response()->json([
            "message" => "Delete Successfully",
            "deleted" => $avalible_to_delete,
            "delete_faild" => $can_not_delete,
            "infor_page" => $this->return_page_infor($request->languages,$request->current_page)
        ],200);
    }

    private function validate_quizz($request)
    {
        $Validater = Validator::make($request->all(),[
            'title_ja'=> '|required|max:255',
            'title_en' =>	'|required|max:255',
            'sentence_ja' =>'|required|max:5000',
            'sentence_en'	=>'|required|max:5000',
            'picture_ja'=>'image:jpeg,jpg,png,gif',
            'picture_en'	=>'image:jpeg,jpg,png,gif',
            'explain_correct_ja'=>	'|required|max:5000',
            'explain_correct_en'	=>'|required|max:5000',
            'explain_incorrect_ja'	=>'|required|max:5000',
            'explain_incorrect_en'	=>'|required|max:5000',
        ]); //validate variables to insert quizz to database
        if($Validater->fails()) return true;
        else return false;
    }

    private function validate_new_quizz($request)
    {
        $Validater = Validator::make($request->all(),[
            'new_options.*.option_ja' => '|required|max:255',
            'new_options.*.option_en' => '|required|max:255',
            'new_options.*.correct' => '|required|boolean',
            'new_options.*.position' => '|required|integer',
        ]); //validate variables to insert quizz to database
        if($Validater->fails())
            return true;
        else return false;
    }

    private function validate_current_options($request)
    {
        $Validater = Validator::make($request->all(),[
            'options.*.option_ja' => '|required|max:255',
            'options.*.option_en' => '|required|max:255',
            'options.*.correct' => '|required|boolean',
            'options.*.position' => '|required|integer',
            'options.*.status' => '|required|integer',
            'options.*.id' => 'integer'
        ]); //validate variables to insert quizz to database
        if($Validater->fails())return true;
        else return false;
    }

    private function return_page_infor($languages,$current_page)
    {
        $languages_quizz = [];
        $languages_option = [];
        if($languages == "ja")
        {
            $languages_quizz = static::JAPAN_QUIZZ_LANGUAGES;
            $languages_option = static::JA_OPTION_LANGUAGES_LESS;
        }
        else if($languages=="en"){
            $languages_quizz = static::ENG_QUIZZ_LANGUAGES;
            $languages_option = static::ENG_OPTION_LANGUAGES_LESS ;
        }
        else {
            $languages_quizz = static::BOTH_LANGUAGES;
            $languages_option = static::BOTH_LANGUAGES_OPTION;
        }

        $query = Quiz::query();
            $query->with([
                'options' => function ($query) use($languages_option) {
                    $query->select($languages_option)->orderBy('position', 'ASC');
                }
            ]);
        return  $query->paginate(static::ITEM_PER_PAGE,$languages_quizz,'page',$current_page);
    }
}

