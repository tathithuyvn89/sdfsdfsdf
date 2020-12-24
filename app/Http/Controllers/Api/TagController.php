<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use App\Laravue\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    const ITEM_PER_PAGE = 10;

    public function index(Request $request)
    {
        TagResource::withoutWrapping();
        $params = $request->all();
        $keyword = Arr::get($params,'keyword');

        if(!empty($keyword))
        {
            $query = Tag::query();
            $query->where('name_ja','LIKE', '%' . $keyword . '%');
            $query->orWhere('name_en','LIKE', '%' . $keyword . '%');
            return TagResource::collection($query->paginate(static::ITEM_PER_PAGE,['id', 'name_ja', 'name_en']));
        }

        return  TagResource::collection(Tag::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en']));
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

        $count = Tag::create([
            "name_ja" => $request->name_ja,
            "name_en" => $request->name_en
        ])->count();

        return response()->json([
            "message" => "Record successfully",
            "page_infor" => Tag::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',ceil($count/10))
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
        $tag = Tag::where('id', $id)->first(["id","name_ja","name_en"]);
        if(empty($tag))
        {
            return response()->json(["message" => "Can not found"],404);
        }
        return new TagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $Validater = Validator::make($request->all(),[
            'name_ja' => '|required|max:255',
            'name_en' => '|required|max:255', 
        ]);

        if($Validater->fails())
        {
            return response()->json(["message" => "wrong fomat"],404);
        }

        $tag->name_ja = $request->name_ja;
        $tag->name_en = $request->name_en;
        $tag->save();
        return response()->json([
            "message" => "Edit successfully",
            "page_infor" => Tag::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }

    public function forceDelete(Request $request)
    {
        $id = $request->id_list;
        $avalible_to_delete = [];
        $can_not_delete = [];
        $tags = Tag::get()->whereIn('id',$id);
        foreach ($tags as $key => $value) {
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

        if(count($avalible_to_delete)==0)
        return response()->json([
            "error"=>"errorPage.errorTagDelete",
            "deleted" => TagResource::collection($avalible_to_delete),
            "delete_faild" => TagResource::collection($can_not_delete),
            "page_infor" => Tag::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],500);

        return response()->json([
            "message" => "Delete successfully",
            "deleted" => TagResource::collection($avalible_to_delete),
            "delete_faild" => TagResource::collection($can_not_delete),
            "page_infor" => Tag::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],200);
    }
}
