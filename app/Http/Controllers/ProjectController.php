<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\UserProject;
use App\Models\User;
use App\Models\Activities;
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
            $project = Project::join('user_project','project.id','=','user_project.project_id')
                                ->where('user_project.user_id',auth()->user()->id)->get();
            return response()->json([
                'metadata' => $project,
                'message' => 'Lấy tất cả bản ghi của dự án',
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
            'name' => 'required',
            'start_date' => 'required',
            'finish_date' => 'required|after:StartDate'
        ], [
            'name.required' => 'Không được để trống tên',
            'start_date.required' => 'Không được để trống ngày bắt đầu',
            'finish_date.required' => 'Không được để trống ngày kết thúc',
            'finish_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu'
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
        $data['user_id'] = auth()->user()->id;
        $data['thumbnails'] = $imageName;
        $project = Project::create($data);
        $userProjectAdd = [
            'user_id' => $data['user_id'],
            'project_id' => $project->id,
            'role' => 1,
            'status' => 1
        ];
        $userProject = UserProject::create($userProjectAdd);
        Activities::addActivity('project','đã tạo mới dự án',auth()->user()->id,$project->id);
        return response()->json([
            'metadata' => $project,
            'message' => 'Thêm mới 1 dự án thành công',
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
        if (User::returnRole($id) == 0) {
            return response([
                "status" => "error",
                "message" => 'không phải admin nên không thể sửa dự án',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required',
            'finish_date' => 'required|after:StartDate',
        ], [
            'name.required' => 'Không được để trống tên',
            'start_date.required' => 'Không được để trống ngày bắt đầu',
            'finish_date.required' => 'Không được để trống ngày kết thúc',
            'finish_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu'
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
                "message" => 'Không tìm thấy bản ghi',
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
        Activities::addActivity('project','đã chỉnh sửa thông tin dự án',auth()->user()->id,$project->id);
        return response()->json([
            'metadata' => $project,
            'message' => 'Sửa dự án thành công',
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
        if(User::returnRole($id) == 0){
            //kp admin
            return response([
                "status" => "error",
                "message" => 'Không phải admin nên không thể thay đổi quyền hạn dự án',
                'statusCode' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        $project->update($request->all());
        Activities::addActivity('project','đã thay đổi quyền của của người dùng trong dự án',auth()->user()->id,$project->id);
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
        if (User::returnRole($id) == 0) {
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
