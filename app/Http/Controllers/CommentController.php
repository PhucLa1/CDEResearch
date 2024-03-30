<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($type,$another_id)
    {
        $comments = Comment::where('type',$type)->where('another_id',$another_id)->with('user')->get();
        return response()->json([
            'metadata' => $comments,
            'message' => 'Create a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'another_id' => 'required',
            'content' => 'required'
        ], [
            'type.required' => 'Không được để trống type',
            'another_id.required' => 'Không được để trống id của thực thể comment',
            'content.required' => 'Không được để trống nội dung',
        ]);
        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $dataAdd = $request->all();
        $dataAdd['user_id'] = auth()->user()->id;
        $comment = Comment::create($dataAdd);
        return response()->json([
            'metadata' => $comment,
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
        $comment = Comment::findOrFail($id);
        if(!$comment){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'metadata' => $comment,
            'message' => 'Show a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id,$project_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ], [
            'content.required' => 'Không được để trống nội dung',
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
        $comment = Comment::find($id);
        if(!$comment){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $comment->update($request->all());
        return response()->json([
            'metadata' => $comment,
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
        $comment = Comment::find($id);
        if(!$comment){
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy ',
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
        $comment->delete();
        return response()->json([
            'message' => 'Xóa bản ghi thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
