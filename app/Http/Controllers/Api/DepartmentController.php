<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;
use App\Laravue\Models\Department;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    const ITEM_PER_PAGE = 10;

    public function index(Request $request)
    {
        DepartmentResource::withoutWrapping();
        $params = $request->all();
        $keyword = Arr::get($params,'keyword');

        if(!empty($keyword))
        {
            $query = Department::query();
            $query->where('name_ja','LIKE', '%' . $keyword . '%');
            $query->orWhere('name_en','LIKE', '%' . $keyword . '%');
            return DepartmentResource::collection($query->paginate(static::ITEM_PER_PAGE,['id', 'name_ja', 'name_en']));
        }
        return DepartmentResource::collection(Department::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en']));
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
            return response()->json(["message" => 0],403);
        }

        $count = Department::create([
            "name_ja" => $request->name_ja,
            "name_en" => $request->name_en
        ])->count();
        

        return response()->json([
            "message" => "Record successfully",
            "page_infor" => Department::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',ceil($count/10))
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
        $department = Department::where('id', $id)->first(["id","name_ja","name_en"]);
        if(empty($department))
        {
            return response()->json(["message" => "Can not found"],404);
        }
        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $Validater = Validator::make($request->all(),[
            'name_ja' => '|required|max:255',
            'name_en' => '|required|max:255', 
        ]);

        if($Validater->fails())
        {
            return response()->json(["message" => "wrong fomat"],404);
        }

        $department->name_ja = $request->name_ja;
        $department->name_en = $request->name_en;
        $department->save();
            return response()->json([
                "message" => "Edit successfully",
                "page_infor" => Department::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
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
        $department = Department::get()->whereIn('id',$id);
        foreach ($department as $key => $value) {
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
            "error"=>"errorPage.errorDepartment",
            "deleted" => DepartmentResource::collection($avalible_to_delete),
            "delete_faild" => DepartmentResource::collection($can_not_delete),
            "page_infor" => Department::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],500);

        return response()->json([
            "message" => "Delete successfully",
            "deleted" => DepartmentResource::collection($avalible_to_delete),
            "delete_faild" => DepartmentResource::collection($can_not_delete),
            "page_infor" => Department::paginate(static::ITEM_PER_PAGE, ['id', 'name_ja', 'name_en'],'page',$request->current_page)
        ],200);
    }
}
