<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\ToDo;
use App\Models\Tag;
class ToDoController extends Controller
{
    public function index($project_id,$todo_permission){
        try{
            if($todo_permission == 0){
                $todos = ToDo::where('ProjectID','=',$project_id)->get();
                return response()->json([
                    'metadata' => $todos,
                    'message' => 'Get all records from ToDo',
                    'status' => 'success',
                    'statusCode' => Response::HTTP_OK
                ], Response::HTTP_OK);
            }
            $loguser = auth()->user()->id;
            $todos = ToDo::where('ProjectID','=',$project_id)->where('UserID','=',$loguser)->get();
            return response()->json([
                'metadata' => $todos,
                'message' => 'Get all records from ToDo',
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
            'Title' => 'required',
            'StartDate' => 'required',
            'FinishDate' => 'required|after:StartDate',
            'ProjectID' => 'required'
        ],[
            'Title.required' => 'Title must not be empty',
            'StartDate.required' => 'StartDate must not be empty',
            'FinishDate.required' => 'FinishDate must not be empty',
            'FinishDate.after' => 'FinishDate must be larger than StartDate',
            'ProjectID.required' => 'ProjectID must not be empty'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $data = $request->all();
        $data['Name'] = 'TODO';
        $todo = Todo::create($data);
        $returnData = Todo::find($todo->id);
        return response()->json([
            'metadata' => $returnData,
            'message' => 'Create a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK); 
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'Title' => 'required',
            'StartDate' => 'required',
            'FinishDate' => 'required|after:StartDate',
            'ProjectID' => 'required'
        ],[
            'Title.required' => 'Title must not be empty',
            'StartDate.required' => 'StartDate must not be empty',
            'FinishDate.required' => 'FinishDate must not be empty',
            'FinishDate.after' => 'FinishDate must be larger than StartDate',
            'ProjectID.required' => 'ProjectID must not be empty'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
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
        $array = explode(",", $todo->Tag);
        $array = array_map('intval', $array);
        $tagInToDo = Tag::whereIn('id', $array)->get();
        $todo->Tag = $tagInToDo;
        return response()->json([
            'metadata' => $todo,
            'message' => 'Get one records from ToDo',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy($id){
        $todo = ToDo::findOrFail($id);
        $todo->delete();
        return response()->json([
            'message' => 'Delete One Record Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }


}
