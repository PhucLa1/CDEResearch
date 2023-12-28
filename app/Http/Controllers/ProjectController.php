<?php

namespace App\Http\Controllers;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $project = Project::all();
            return response()->json([
                'metadata' => $project,
                'message' => 'Get all records from Project',
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
        $project = new Project();
        $validator = Validator::make($request->all(),[
            'ProjectName'=>'required|max:255', 
        ],[
            'ProjectName.required'=>'Project Name must not be empty',
            'ProjectName.max'=>'Length of Project name cannot be larger than 255'
        ]);
        $imageName = time().'.'.$request->thumbnail->extension();
        $request->thumbnail->move(public_path('Upload'), $imageName);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $project = Project::create([
            'ProjectName'=>$request->ProjectName,
            'thumbnail'=>$imageName,
            'Note'=>$request->Note,
            'StartDate'=>$request->StartDate,
            'FinishDate'=>$request->FinishDate,
            'Status'=>$request->Status
        ]);
        return response()->json([
            'metadata' => $project,
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
        $project = Project::findOrFail($id);
        if(!$project){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND); 
        }
        return response()->json([
            'metadata' => $project,
            'message' => 'Update a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(),[
            'ProjectName'=>'required|max:255'
        ],[
            'ProjectName.required'=>'Project Name must not be empty',
            'ProjectName.max'=>'Length of Project name cannot be larger than 255'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        if(!$project){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND); 
        }
        $project->update($request->all());
        return response()->json([
            'metadata' => $project,
            'message' => 'Update a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if(!$project){
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND); 
        }
        $project->delete();
        return response()->json([
            'message' => 'Delete One Record Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
