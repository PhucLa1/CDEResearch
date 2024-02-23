<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\UserProject;
use Illuminate\Validation\Rule;
use File;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function listFolderAndFiles($project_id, $folder_id)
    // {
    //     $folders = Folder::where('ProjectID', '=', $project_id)
    //         ->where('ParentID', '=', $folder_id)
    //         ->with('user')
    //         ->get();
    //     $files = Files::where('FolderID', $folder_id)
    //         ->where('ProjectID', $project_id)
    //         ->with('user')
    //         ->get();
    //     $dataReturn = [
    //         'folders' => $folders,
    //         'files' => $files,
    //     ];
    //     return response()->json([
    //         'metadata' => $dataReturn,
    //         'message' => 'Get all records from Folder',
    //         'status' => 'success',
    //         'statusCode' => Response::HTTP_OK
    //     ], Response::HTTP_OK);
    // }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'FolderName' => [
        //         'required',
        //         Rule::unique('folder')->where(function ($query) use ($request) {
        //             return $query->where('ProjectID', $request->ProjectID)->where('ParentID',$request->ParentID);
        //         })
        //     ],
        //     'ParentID' => 'required',
        //     'ProjectID' => 'required',
        // ], [
        //     'FolderName.unique' => 'Tên folder đã bị trùng trong một dự án',
        //     'FolderName.required' => 'Không được để trống tên của folder',
        //     'ParentID.required' => 'Không được để trống ID của folder cha',
        //     'ProjectID.required' => 'Không được để trống id của project',
        // ]);
        // if ($validator->fails()) {
        //     return response([
        //         "status" => "error",
        //         "message" => $validator->errors(),
        //         'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
        //     ], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
        // $dataAdd = $request->all();
        // $dataAdd['UserID'] = auth()->user()->id;
        // $folder = Folder::create($dataAdd);

        // //Thêm dữ liệu vào folder permission
        
        // return response()->json([
        //     'metadata' => $folder,
        //     'message' => 'Create a record successfully',
        //     'status' => 'success',
        //     'statusCode' => Response::HTTP_OK
        // ], Response::HTTP_OK);
        //Storage::cloud()->put('test.txt', 'Hello World');
        //return 'File was saved to Google Drive';
    
    }


    /**
     * Display the specified resource.
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        Storage::disk('google')->put('uploads/' . $file->getClientOriginalName(), file_get_contents($file));
        return 'File was saved to Google Drive';
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
    public function destroy($id,$project_id)
    {
        // $folder = Folder::find($id);
        // if(!$folder){
        //     return response([
        //         "status" => "error",
        //         "message" => 'Record not found',
        //         'statusCode' => Response::HTTP_NOT_FOUND
        //     ], Response::HTTP_NOT_FOUND); 
        // }
        // $logUser = auth()->user()->id;
        // $roleInProject = UserProject::where('UserID','=',$logUser)->where('ProjectID','=',$project_id)->first()->Role;
        // if($roleInProject != 1){
        //     return response([
        //         "status" => "error",
        //         "message" => 'Không phải admin nên không có quyền xóa',
        //         'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
        //     ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        // }
        // $folder->delete();

        // //Xóa tất cả bản ghi bên folder permiss

        
        // //Xóa tất cả bản ghi bên files
        

        // return response()->json([
        //     'message' => 'Delete One Record Successfully',
        //     'status' => 'success',
        //     'statusCode' => Response::HTTP_OK
        // ], Response::HTTP_OK);
    }
}
