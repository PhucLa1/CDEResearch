<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\Tag;
use App\Models\UserProject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($project_id)
    {
        try{
            $tag = Tag::where('project_id','=',$project_id)->get();
            return response()->json([
                'metadata' => $tag,
                'message' => 'Get all records from Tag',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }catch(\Exception $e){
            return response([
                "status" => "error",
                "message" => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>[
                    'required',
                    'max:255',
                    Rule::unique('tag')->where(function ($query) use ($request) {
                        return $query->where('project_id', $request->project_id);
                    }),
                ],
            'project_id' => 'required'
        ],[
            'name.required'=>'Không được để trống tag name',
            'name.max'=>'Độ dài không được vượt quá 255',
            'project_id.required'=>'Không được để trống id của project',
            'name.unique' => 'Tên tag trong dự án đã được lấy rồi'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $tag = Tag::create($request->all());

        //Thêm mới activities
        Activities::addActivity('Tag','đã thêm mới một tag',auth()->user()->id,$request->project_id);
        return response()->json([
            'metadata' => $tag,
            'message' => 'Create a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $tag = Tag::findOrFail($id);
        if(!$tag){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'metadata' => $tag,
            'message' => 'Show a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id,$project_id)
    {
        $validator = Validator::make($request->all(),[
            'name'=>[
                'required',
                'max:255',
                Rule::unique('tag')->where(function ($query) use ($request,$id) {
                    return $query->where('project_id', $request->project_id)
                    ->where('id','!=',$id);
                }),
            ],
        ],[
            'name.required'=>'Không được để trống tag name',
            'name.max'=>'Độ dài không được vượt quá 255',
            'name.unique' => 'Tên tag đã được lấy rồi'
        ]);
        if(User::returnRole($project_id) != 1){
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không có quyền sửa',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $tag = Tag::find($id);
        if(!$tag){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $tag->update($request->all());
        Activities::addActivity('Tag',"đã chỉnh sửa tag {$tag->name}",auth()->user()->id,$request->project_id);
        return response()->json([
            'metadata' => $tag,
            'message' => 'Update a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id,$project_id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        if(User::returnRole($project_id) != 1){
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không có quyền xóa',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $name = $tag->name;
        $tag->delete();
        Activities::addActivity('Tag',"đã xóa tag {$name}",auth()->user()->id,$project_id);
        return response()->json([
            'message' => 'Delete One Record Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
    public function removeAll($project_id){
        if(User::returnRole($project_id) != 1){
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không có quyền xóa tất cả bản ghi',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $tagRemove = Tag::where('project_id',$project_id)->delete();
        Activities::addActivity('Tag',"đã xóa toàn bộ tag trong dự án",auth()->user()->id,$project_id);
        return response()->json([
            'message' => 'Delete All Record Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
