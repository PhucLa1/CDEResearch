<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\UserProject;
use Illuminate\Validation\Rule;
use App\Models\Folder;
use App\Models\Files;
use App\Models\FolderPermission;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listFolderAndFiles($project_id, $parent_id)
    {
        //parent id là id của folder được chọn, nếu là lớn nhất thì parent id là 0
        $folders = Folder::where('project_id', '=', $project_id)
            ->where('parent_id', '=', $parent_id)
            ->with('user')
            ->get();
        $files = Files::where('project_id', '=', $project_id)
            ->where('folder_id', '=', $parent_id)
            ->where('status', '=', 1)
            ->with('user')
            ->get();
        $dataReturn = [
            'folders' => $folders,
            'files' => $files
        ];
        return response()->json([
            'metadata' => $dataReturn,
            'message' => 'Get all records from Folder,Files',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function listFolderCanMove(Request $request)
    {
        $type = $request->type;
        $parent_id = $request->parent_id;
        $project_id = $request->project_id;
        $folder_id = $request->folder_id;
        if ($type == 'files') {
            $folderCanMove = Folder::where('project_id', '=', $project_id)
                ->where('parent_id', '=', $parent_id)
                ->get();
            return response()->json([
                'metadata' => $folderCanMove,
                'message' => 'Get all records from Folder',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } else {
            $folderCanMove = Folder::where('project_id', '=', $project_id)
                ->where('parent_id', '=', $parent_id)
                ->where('id', '!=', $folder_id)
                ->get();
            return response()->json([
                'metadata' => $folderCanMove,
                'message' => 'Get all records from Folder',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('folder')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id)->where('parent_id', $request->parent_id);
                })
            ],
            'parent_id' => 'required',
            'project_id' => 'required',
        ], [
            'name.unique' => 'Tên folder đã bị trùng trong một dự án',
            'name.required' => 'Không được để trống tên của folder',
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
        $dataAdd = $request->all();
        $dataAdd['user_id'] = auth()->user()->id;
        $folder = Folder::create($dataAdd);
        $dataReturn = Folder::latest()->first();

        //Thêm dữ liệu vào folder permission, mac dinh la se la permission la 1 //Có quyen chinh sửa, 0: chỉ đc xem
        UserProject::where('project_id', $request->project_id)->get()->each(function ($item) use ($dataReturn) {
            // Thêm một bản ghi vào bảng 'folder_permission' cho mỗi phần tử
            FolderPermission::create([
                'user_id' => $item->user_id,
                'folder_id' => $dataReturn->id,
                'permission' => 1,
            ]);
        });
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
    public function upload(Request $request)
    {
        $file = $request->file('file');
        Storage::disk('google')->put('uploads/' . $file->getClientOriginalName(), file_get_contents($file));
        return 'File was saved to Google Drive';
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show($id)
    {
        $folder = Folder::findOrFail($id);
        if (!$folder) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy bản ghi',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'metadata' => $folder,
            'message' => 'Update a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                Rule::unique('folder')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id)->where('parent_id', $request->parent_id);
                })
            ],
            'parent_id' => 'required',
            'project_id' => 'required',
        ], [
            'name.unique' => 'Tên folder đã bị trùng trong một dự án',
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
        $folder = Folder::findOrFail($id);
        if (!$folder) {
            return response([
                "status" => "error",
                "message" => 'Không tìm thấy bản ghi',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $folderPermis = FolderPermission::where('folder_id', '=', $folder->id)->where('user_id', '=', auth()->user()->id)->first()->permission;
        if (User::returnRole($request->project_id) != 1 || $folderPermis != 1) {
            return response([
                "status" => "error",
                "message" => 'Không có quyền',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $dataAdd = $request->all();
        $dataAdd['user_id'] = auth()->user()->id;
        $folder->update($dataAdd);
        return response()->json([
            'metadata' => $folder,
            'message' => 'Update a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $project_id)
    {
        $folder = Folder::find($id);
        if (!$folder) {
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        if (User::returnRole($project_id) != 1) {
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không có quyền xóa',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $folder->delete();
        return response()->json([
            'message' => 'Delete One Record Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
