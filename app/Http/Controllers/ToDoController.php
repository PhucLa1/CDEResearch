<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\ToDo;

class ToDoController extends Controller
{
    public function index(){
        try{
            $todo = ToDo::all();
            return response()->json([
                'metadata' => $todo,
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

    public function store(Request $request, $project_id){
        $validator = Validator::make($request->all(),[
            'Title' => 'required',
            'StartDate' => 'required',
            'FinishDate' => 'required|after:StartDate',
        ],[
            'Title.required' => 'Title must not be empty',
            'StartDate.required' => 'StartDate must not be empty',
            'FinishDate.required' => 'FinishDate must not be empty',
            'FinishDate.after' => 'FinishDate must be larger than StartDate'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $data = $request->all();
        $project = 
        $todo = Todo::create($request->all());
        return response()->json([
            'metadata' => $todo,
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
        ],[
            'Title.required' => 'Title must not be empty',
            'StartDate.required' => 'StartDate must not be empty',
            'FinishDate.required' => 'FinishDate must not be empty',
            'FinishDate.after' => 'FinishDate must be larger than StartDate'
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


}
