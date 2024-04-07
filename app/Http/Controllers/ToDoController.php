<?php

namespace App\Http\Controllers;
use Mail;
use App\Mail\TodoMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\ToDo;
use App\Models\Tag;
use App\Models\User;

class ToDoController extends Controller
{
    public function index($project_id,$todo_permission){
        try{
            if($todo_permission == 0){
                $todos = ToDo::where('project_id','=',$project_id)->get();
                return response()->json([
                    'metadata' => $todos,
                    'message' => 'Lấy mọi bản ghi từ Todo',
                    'status' => 'success',
                    'statusCode' => Response::HTTP_OK
                ], Response::HTTP_OK);
            }
            $loguser = auth()->user()->id;
            $todos = ToDo::where('project_id','=',$project_id)->where('user_id','=',$loguser)->get();
            return response()->json([
                'metadata' => $todos,
                'message' => 'Lấy mọi bản ghi từ ToDo',
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

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'start_date' => 'required',
            'finish_date' => 'required|after:start_date',
            'project_id' => 'required'
        ],[
            'title.required' => 'Title must not be empty',
            'start_date.required' => 'StartDate must not be empty',
            'finish_date.required' => 'FinishDate must not be empty',
            'finish_date.after' => 'FinishDate must be larger than StartDate',
            'project_id.required' => 'ProjectID must not be empty'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = $request->all();
        $data['name'] = 'TODO';
        $todo = Todo::create($data);
        $dataReturn = Todo::latest()->with('user')->with('file.user')->first();
        if($request->assgin_to){
            Mail::to($request->assgin_to)->send(new TodoMail($dataReturn));
        }
        return response()->json([
            'metadata' => $dataReturn,
            'message' => 'Tạo mới bản ghi thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id, $project_id){
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'start_date' => 'required',
            'finish_date' => 'required|after:start_date',
            'project_id' => 'required'
        ],[
            'title.required' => 'Không được để trống title',
            'start_date.required' => 'Không được để trống ngày bắt đầu',
            'finish_date.required' => 'Không được để trống ngày kết thúc',
            'finish_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu',
            'project_id.required' => 'Không được để trống project id'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if(User::returnRole($project_id) != 1){
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không có quyền chỉnh sửa',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $
        $todo = ToDo::findOrFail($id);
        $todo->update($request->all());
        return response()->json([
            'metadata' => $todo,
            'message' => 'Create a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function show($id){
        $todo = ToDo::find($id);
        return response()->json([
            'metadata' => $todo,
            'message' => 'Lấy 1 bản ghi thành công từ ToDo',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy($id,$project_id){
        $todo = ToDo::findOrFail($id);
        if(User::returnRole($project_id) != 1){
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không có quyền xóa',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $todo->delete();
        return response()->json([
            'message' => 'Xóa 1 bản ghi thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }


}
