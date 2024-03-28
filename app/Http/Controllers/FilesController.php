<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file =  $request->file;
        if(!$file){
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        //Lấy các thuộc tính của file
        $name = $file->getClientOriginalName();
        $size = $file->getClientSize();
        $user_id = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required',
            'project_id' => 'required',
        ], [
            'parent_id.required' => 'Không được để trống ID của folder cha',
            'project_id.required' => 'Không được để trống id của project',
        ]);
        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //Get versions last
        $version = Files::where('status','=',0)
        ->where('name','=',$name)->latest('created_at')->first();
        if ($version) {
            $versions = $version->versions+1;
        } else {
            $versions = 1;
        }
        //Add in gg drive
        Storage::disk('google')->put($name, file_get_contents($file));
        //Prepare data to insert into db
        $dataAdd = $request->all();
        $dataAdd['name'] = $name;
        $dataAdd['size'] = $size;
        $dataAdd['user_id'] = $user_id;
        $dataAdd['versions'] = $versions;
        $dataAdd['url'] = $name;
        //Add in db
        $fileAdd = Files::create($dataAdd);
        return response()->json([
            'metadata' => $dataAdd,
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
    public function edit(string $id)
    {
        //
    }

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
