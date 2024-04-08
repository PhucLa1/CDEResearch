<?php

namespace App\Http\Controllers;
use Mail;
use App\Mail\TodoMail;
use App\Models\Activities;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\ToDo;
use App\Models\Tag;
use App\Models\User;
use App\Models\Project;
class ToDoController extends Controller
{
    public function index(Request $request,$project_id){
        try{
            $todo_permission = Project::findOrFail($project_id)->todo_permission;
            $loguser = auth()->user()->id;
            $owns = $request->owns ? explode(',', $request->owns) : [];
            $users = $request->users ? explode(',', $request->users) : [];
            $status = $request->status ? explode(',', $request->status) : [];
            $priorities = $request->priorities ? explode(',', $request->priorities) : [];
            if($todo_permission == 0 || User::returnRole($project_id) == 1){
                $todos = ToDo::leftJoin('users','todo.assgin_to','=','users.email')
                ->where('project_id','=',$project_id);
                if(in_array(1,$owns)){ //Người được giao nhiệm vụ
                    $todos = $todos->where('users.id',$loguser);
                }
                if(in_array(2,$owns)){
                    $todos = $todos->orWhere('todo.user_create',$loguser);
                }
                if(in_array(3,$owns)){
                    $todos = $todos->orWhere('todo.assgin_to',null);
                }
                if(count($users) != 0){
                    $todos = $todos->whereIn('users.id',$users);
                }
                if(count($status) != 0){
                    $todos = $todos->whereIn('todo.status',$status);
                }
                if(count($priorities) != 0){
                    $todos = $todos->whereIn('todo.priorities',$priorities);
                }
                return response()->json([
                    'metadata' => $todos->get(),
                    'message' => 'Lấy mọi bản ghi từ Todo',
                    'status' => 'success',
                    'statusCode' => Response::HTTP_OK
                ], Response::HTTP_OK);
            }

            //Tức là dự án chỉ hiện todo cho admin,người tạo và người được giao nhiệm vụ
            $todos = ToDo::join('users','todo.assgin_to','=','users.email')
            ->where('todo.project_id','=',$project_id)
            ->where('users.id',$loguser)//Người được giao nhiệm vụ
            ->orWhere('todo.user_create',$loguser); //Người tạo
            if(in_array(1,$owns)){ //Người được giao nhiệm vụ
                //$todos = $todos->where('users.id',$loguser);
            }
            if(in_array(2,$owns)){
                //$todos = $todos->orWhere('todo.user_create',$loguser);
            }
            if(in_array(3,$owns)){
                $todos = $todos->orWhere('todo.assgin_to',null);
            }
            if(count($users) != 0){
                $todos = $todos->whereIn('users.id',$users);
            }
            if(count($status) != 0){
                $todos = $todos->whereIn('todo.status',$status);
            }
            if(count($priorities) != 0){
                $todos = $todos->whereIn('todo.priorities',$priorities);
            }
            return response()->json([
                'metadata' => $todos->get(),
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
        $data['user_create'] = auth()->user()->id;
        $todo = Todo::create($data);
        $dataReturn = Todo::latest()->with('user')->with('file.user')->first();
        if($request->assgin_to){
            Mail::to($request->assgin_to)->send(new TodoMail($dataReturn));
        }
        //Add activity
        Activities::addActivity('Todo',`đã thêm mới một nhiệm vụ mang tên {$todo->title}`,auth()->user()->id,$request->project_id);
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
        $todoEmailInDB = $todo->assgin_to;
        $todo->update($request->all());
        $dataReturn = ToDo::with('user')->with('file.user')->findOrFail($id);
        if($todoEmailInDB != $request->assgin_to){
            Mail::to($request->assgin_to)->send(new TodoMail($dataReturn));
        }

        //add activity
        Activities::addActivity('Todo',`cập nhật nhiệm vụ mang tên {$todo->title}`,auth()->user()->id,$request->project_id);
        return response()->json([
            'metadata' => $dataReturn,
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
        Activities::addActivity('Todo',`đã xóa nhiệm vụ mang tên {$todo->title}`,auth()->user()->id,$project_id);
        $todo->delete();
        return response()->json([
            'message' => 'Xóa 1 bản ghi thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }


}
