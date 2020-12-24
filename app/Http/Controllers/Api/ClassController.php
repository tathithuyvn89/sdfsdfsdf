<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use Illuminate\Http\Request;
use App\Laravue\Models\Classs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    const ITEM_PER_PAGE = 10; //set item per page

    public function index(Request $request)
    {
        ClassResource::withoutWrapping();
        $params = $request->all(); //get param on url ?keyword=ABC
        $keyword = Arr::get($params,'keyword');

        if(!empty($keyword)) //have any param
        {
            $query = Classs::query();
            $query->where('name_ja','LIKE', '%' . $keyword . '%');
            $query->orWhere('name_en','LIKE', '%' . $keyword . '%');
            return ClassResource::collection($query->paginate(static::ITEM_PER_PAGE,['id', 'name_ja', 'name_en']));
        }

        return  ClassResource::collection(Classs::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $Validater = Validator::make($request->all(),[
            'name_ja' => '|required|max:255',
            'name_en' => '|required|max:255', 
        ]);

        if($Validater->fails())
        {
            return response()->json(["message" => "wrong fomat"],403);
        }

        $count = Classs::create([
            "name_ja" => $request->name_ja,
            "name_en" => $request->name_en
        ])->count();

        return response()->json([
            "message" => "Record successfully",
            "page_infor" => Classs::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',ceil($count/10))
        ],200);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $class = Classs::where('id', $id)->first(["id","name_ja","name_en"]);
        if(empty($class))
        {
            return response()->json(["message" => "Can not found"],404);
        }
        return new ClassResource($class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classs $class)
    {
        $Validater = Validator::make($request->all(),[
            'name_ja' => '|required|max:255',
            'name_en' => '|required|max:255', 
        ]);

        if($Validater->fails())
        {
            return response()->json(["message" => "wrong fomat"],404);
        }

        $class->name_ja = $request->name_ja;
        $class->name_en = $request->name_en;
        $class->save();
        return response()->json([
            "message" => "Edit successfully",
            "page_infor" => Classs::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        
    }

    public function forceDelete(Request $request)
    {
        $id = $request->id_list;
        $avalible_to_delete = [];
        $can_not_delete = [];
        $classes = Classs::get()->whereIn('id',$id);
        foreach ($classes as $key => $value) {
            try 
            {
                if($value->delete())
                {
                    array_push($avalible_to_delete,$value);
                }
            }
            catch(\Exception $e)
            {
                array_push($can_not_delete,$value);
            }
        }
        if(count($avalible_to_delete) == 0)
        return response()->json([
            "error"=> "errorPage.errorClassDelete",
            "deleted" => ClassResource::collection($avalible_to_delete),
            "delete_faild" => ClassResource::collection($can_not_delete), 
            "page_infor" => Classs::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],500);

        return response()->json([
            "message" => "Delete successfully",
            "deleted" => ClassResource::collection($avalible_to_delete),
            "delete_faild" => ClassResource::collection($can_not_delete), 
            "page_infor" => Classs::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],200);
    }
}