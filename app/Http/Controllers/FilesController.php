<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file =  $request->file;
        if (!$file) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        //Lấy các thuộc tính của file
        $name = $file->getClientOriginalName();
        $size = $file->getSize();
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
        $version = Files::where('status', '=', 1)
            ->where('name', '=', $name)->orderBy('created_at', 'desc')->first();
        $canUpdate = false;
        if ($version) { //exsists
            $first_version = $version->first_version;
            $versions = $version->versions + 1;
        } else { //not exists
            $canUpdate = true; //Mình sẽ cho tạm version đầu là 0, rồi về sau update
            $first_version = 0;
            $versions = 1;
        }

        //Add in gg drive
        $googleFileName = time() . '.' . $name;
        Storage::disk('google')->put($googleFileName, file_get_contents($file));
        //Prepare data to insert into db
        $dataAdd = $request->all();
        $dataAdd['name'] = $name;
        $dataAdd['size'] = $size;
        $dataAdd['user_id'] = $user_id;
        $dataAdd['versions'] = $versions;
        $dataAdd['url'] = $googleFileName;
        $dataAdd['first_version'] = $first_version;
        //Add in db
        $fileAdd = Files::create($dataAdd);
        if ($canUpdate == true) {
            $file = Files::orderBy('id', 'desc')->first();
            if ($file) {
                $file->update(['first_version' => $file->id]);
            }
        }
        if ($versions > 5) {
            FilesController::destroy($first_version);
        }
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
    //Lấy hết thông tin của 1 file ra
    public function show($id)
    {
        $file = Files::findOrFail($id);
        if (!$file) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file đó đâu',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'metadata' => $file,
            'message' => 'Create a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function historyOfFiles($id)
    {
    }
    public function dowload($id)
    {
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $file = Files::findOrFail($id);
        if (!$file) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file đó đâu',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        //GIảm version đi 1
        $effectedFile = Files::where('first_version', $file->first_version)->update(['versions' => DB::raw('versions - 1')]);
        //Lấy thằng files cũ nhất mà có cùng version cha
        $first_version = $file->first_version;
        $file->delete();
        $firstVersion = Files::where('first_version', $first_version)->first();
        if(!$firstVersion){
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file đó đâu',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $firstVersionId = $firstVersion->id;
        //CHuyển version các thằng kia về như cũ
        $effectedFileFirstVersion = Files::where('first_version', $file->first_version)->update(['first_version' => $firstVersionId]);


        return response()->json([
            'message' => 'Xóa bản ghi thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
