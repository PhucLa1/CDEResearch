<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\Files;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Activities;
use Spatie\PdfToImage\Pdf;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */


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
            $version->update(['status' => 0]);
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

        Activities::addActivity('Files', "đã thêm mới một file {$name}", auth()->user()->id, $request->project_id);
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
    public function convert(Request $request)
    {
        $fileData = Storage::disk('google')->get("1713628307.Nguyen_Ngoc_Son.pdf");
        return $fileData;
    }
    //Lấy hết thông tin của 1 file ra
    public function show($id, $option)
    {
        $file = Files::findOrFail($id);
        $fileData = Storage::disk('google')->get($file->url);
        if ($option == 2) {
            return $fileData;
        }
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

    public function historyOfFiles($first_version)
    {
        $historyFiles = Files::where('first_version', $first_version)
            ->get();
        return response()->json([
            'metadata' => $historyFiles,
            'message' => 'Lấy ra các files cũ thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
    public function dowload($id, $project_id)
    {
        $file = Files::findOrFail($id);
        if (!$file) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file đó đâu',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $url = $file->url;
        $fileContent = Storage::disk('google')->get($url);
        $downloadsFolder = rtrim(shell_exec('echo %USERPROFILE%\Downloads'));
        $localFilePath = $downloadsFolder . '/' . $url;
        file_put_contents($localFilePath, $fileContent);
        Activities::addActivity('Files', "đã tải file {$file->name}", auth()->user()->id, $project_id);
        return response()->json([
            'metadata' => $file,
            'message' => 'Tải xuống thành công',
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
    public function update(Request $request, $id, $option)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('files')->where(function ($query) use ($request, $id) {
                    return $query->where('project_id', $request->project_id)
                        ->where('folder_id', $request->folder_id)
                        ->where('id', '!=', $id);
                })
            ],
            'folder_id' => 'required',
            'project_id' => 'required',
        ], [
            'name.required' => 'Tên phải điền',
            'name.unique' => 'Tên file đã trùng',
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
        $file = Files::findOrFail($id);
        if (User::returnRole($request->project_id) != 1 || $file->user_id != auth()->user()->id) {
            return response([
                "status" => "error",
                "message" => 'Không phải admin, cũng như người upload file nên không có quyền chỉnh sửa',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }

        $nameInDB = $file->name;
        $name = $request->name;
        if ($nameInDB !== $name) { //Thay tên khác, thêm 1 ver
            //Thêm dữ liệu mới
            if ($file->versions == 5) {
                //Xóa file
                FilesController::destroy($file->first_version);
            }
            $file->update(['status' => 0]);
            $fileAdd = Files::create([
                'folder_id' => $request->folder_id,
                'project_id' => $request->project_id,
                'name' => $request->name,
                'size' => $file->size,
                'user_id' => auth()->user()->id,
                'versions' => $file->versions + 1,
                'url' =>  time() . '.' . $file->name,
                'first_version' => $file->first_version
            ]);


            //Add lên gg drive
            $fileContent = Storage::disk('google')->get($file->url);
            Storage::disk('google')->put(time() . '.' . $file->name, $fileContent);
            if ($option == 1) {
                Activities::addActivity('Files', "đã thay đổi tên file {$nameInDB} sang thành {$request->name}", auth()->user()->id, $request->project_id);
            }

            return response()->json([
                'metadata' => $fileAdd,
                'message' => 'Thêm mới bản ghi thành công khi đổi tên',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
        $file->update($request->all());
        $content = $option == 2 ? "đã di chuyển file {$nameInDB} sang thư mục khác" : "thêm tag cho file {$nameInDB}";
        Activities::addActivity('Files', $content, auth()->user()->id, $request->project_id);
        return response()->json([
            'metadata' => $file,
            'message' => 'Update thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public static function destroy($id)
    {
        $file = Files::findOrFail($id);
        if (!$file) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy file đó đâu',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        if ($file->status == 1) {
            $deletedFile = Files::where('first_version', $file->first_version)->delete();
            return response()->json([
                'message' => 'Xóa bản ghi thành công',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
        //GIảm version đi 1
        $effectedFile = Files::where('first_version', $file->first_version)->update(['versions' => DB::raw('versions - 1')]);
        //Lấy thằng files cũ nhất mà có cùng version cha
        $first_version = $file->first_version;
        $file->delete();
        $firstVersion = Files::where('first_version', $first_version)->first();
        if (!$firstVersion) {
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

    public function deleteByPermis($id, $project_id)
    {
        if (User::returnRole($project_id) != 1) {
            return response([
                "status" => "error",
                "message" => 'Không phải admin không có quyền xóa file',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $name = Files::findOrFail($id)->name;
        Activities::addActivity('Files', "đã xóa file {$name} khỏi dự án", auth()->user()->id, $project_id);
        Files::destroy($id);
    }
}
