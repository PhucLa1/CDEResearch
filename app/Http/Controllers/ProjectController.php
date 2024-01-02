<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\UserProject;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $project = Project::all();
            return response()->json([
                'metadata' => $project,
                'message' => 'Get all records from Project',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
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
        $imageName = null;
        $validator = Validator::make($request->all(), [
            'ProjectName' => 'required',
            'StartDate' => 'required',
            'FinishDate' => 'required|after:StartDate'
        ], [
            'ProjectName.required' => 'Không được để trống tên',
            'StartDate.required' => 'Không được để trống ngày bắt đầu',
            'FinishDate.required' => 'Không được để trống ngày kết thúc',
            'FinishDate.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu'
        ]);
        if ($request->thumbnails != null) {
            $imageName = time() . '.' . $request->thumbnails->extension();
            $request->thumbnails->move(public_path('Upload'), $imageName);
            
        }

        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = $request->all();
        $data['UserID'] = auth()->user()->id;
        $data['thumbnails'] = $imageName;
        $project = Project::create($data);
        $userProjectAdd = [
            'UserID' => $data['UserID'],
            'ProjectID' => $project->id,
            'Role' => 1,
            'Status' => 1
        ];
        $userProject = UserProject::create($userProjectAdd);
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
        if (!$project) {
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
    public function update(Request $request, $id)
    {
        
        $project = Project::findOrFail($id);
        $imageName = $project->thumbnails;
        $logUser = auth()->user()->id;
        $roleInProject = UserProject::where('UserID', '=', $logUser)->where('ProjectID', '=', $project->id)->first()->Role;
        if ($logUser != $project->UserID && $roleInProject != 1) {
            return response([
                "status" => "error",
                "message" => 'Không phải người tạo ra dự án hoặc không phải admin nên không thể sửa dự án',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $validator = Validator::make($request->all(), [
            'ProjectName' => 'required',
            'StartDate' => 'required',
            'FinishDate' => 'required|after:StartDate',
        ], [
            'ProjectName.required' => 'Không được để trống tên',
            'StartDate.required' => 'Không được để trống ngày bắt đầu',
            'FinishDate.required' => 'Không được để trống ngày kết thúc',
            'FinishDate.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu'
        ]);
        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (!$project) {
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        //Thêm ảnh
        if ($request->thumbnails != null) {
            $imageName = time() . '.' . $request->thumbnails->extension();
            $request->thumbnails->move(public_path('Upload'), $imageName);
        }

        //Nếu có sắn ảnh rồi thì xóa ảnh đó đi
        if ($project->thumbnails != null) {
            //Xóa ảnh 
            $imagePath = public_path('Upload/' . $project->thumbnails);
            File::delete($imagePath);
        }
        $data= $request->all();
        $data['thumbnails'] = $imageName;
        $project->update($data);
        return response()->json([
            'metadata' => $project,
            'message' => 'Update a record successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
    public function changePermission($id,Request $request){
        $project = Project::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'todo_permission' => 'required',
            'invite_permission' => 'required',
        ], [
            'todo_permission.required' => 'Không được để trống quyền todo',
            'invite_permission.required' => 'Không được để trống quyền mời',
        ]);
        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if(User::returnRole($project->id) == 0){
            //kp admin
            return response([
                "status" => "error",
                "message" => 'Không phải admin ai cho ông thay đổi quyền hạn',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $project->update($request->all());
        return response()->json([
            'message' => 'Chuyển đổi thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        if (!$project) {
            return response([
                "status" => "error",
                "message" => 'Record not found',
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $logUser = auth()->user()->id;
        $roleInProject = UserProject::where('UserID', '=', $logUser)->where('ProjectID', '=', $project->id)->first()->Role;
        if ($roleInProject != 1) {
            return response([
                "status" => "error",
                "message" => 'Không phải admin thì không cho xóa dự án',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        //Nếu có sắn ảnh rồi thì xóa ảnh đó đi
        if ($project->thumbnails != null) {
            //Xóa ảnh 
            $imagePath = public_path('Upload/' . $project->thumbnails);
            File::delete($imagePath);
        }
        $project->delete();
        return response()->json([
            'message' => 'Delete One Record Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
