<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Folder;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listFolderAndFiles(Request $request)
    {
        $project_id = $request->query('project_id',1);
        $folder_id = $request->query('folder_id',0);
        $folders = Folder::where('ProjectId','=', $project_id)
                            ->where('ParentID','=',$folder_id)
                            ->with('user')
                            ->get();
        return response()->json([
                'metadata' => $folders,
                'message' => 'Get all records from Folder',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'FolderName' => 'required',
            'ParentID' => 'required',
            'ProjectID' => 'required',
        ],[
            'FolderName.required' => 'Không được để trống tên của folder',
            'ParentID.required' => 'Không được để trống ID của folder cha',
            'ProjectID.required' => 'Không được để trống id của project',
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $dataAdd = $request->all();
        $dataAdd['UserID'] = auth()->user()->id;
        $folder = Folder::create($dataAdd);
        return response()->json([
            'metadata' => $folder,
            'message' => 'Create a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK); 

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
