<?php

namespace App\Http\Controllers;

use Mail;
use App\Mail\RequestMail;
use App\Models\Activities as ModelsActivities;
use Illuminate\Validation\Rule;
use App\Models\UserProject;
use App\Models\User;
use App\Models\Project;
use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JoinController extends Controller
{
    //
    public function index($project_id)
    {
        $teams = UserProject::where('project_id', $project_id)
            ->with('user')
            ->with('project')
            ->get()
            ->map(function ($team) {
                $team->role = ($team->role == 1) ? 'Admin' : 'User';
                $team->status = ($team->status == 1) ? 'Active' : 'Pending Invite';
                return $team;
            });
        return response()->json([
            'metadata' => $teams,
            'message' => 'Get all records from Teams',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function SendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required'],
            'email' => ['required'],
            'role' => ['required'],

        ], [
            'project_id.required' => 'Id của project không được để trống ',
            'email.required' => 'Bặt buộc phải điền trường email',
            'role.required' => 'Role của người dùng trong dự án đó phải được chọn'
        ]);
        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //Check id của email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response([
                "status" => "error",
                "message" => 'Chưa có email này',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $user_id = $user->id;
        $checkUserUnique = UserProject::where('user_id', $user_id)->where('project_id', $request->project_id)->first();

        if ($checkUserUnique) {
            return response([
                "status" => "error",
                "message" => 'User đã tồn tại trong dự án rồi',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $project_id = $request->project_id;
        $project = Project::find($project_id);
        if ($project->invite_permission == 1 && User::returnRole($project_id) == 0) {
            // nếu project chỉ cho admin mời
            // Thằng đăng nhập cũng không phải admin
            return response([
                "status" => "error",
                "message" => 'Bạn không phải admin dự án và dự án không cấp phép cho bạn mời người ngoài',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $dataAdd['user_id'] = $user_id;
        $dataAdd['project_id'] = $request->project_id;
        $dataAdd['role'] = $request->role;
        $userAddPendingInvite = UserProject::create($dataAdd);
        //Send Email
        $logUser = auth()->user()->id;
        $userIds = [$logUser, $user_id];
        $users = User::findMany($userIds);

        $dataReturn = [
            'userSend' => $users->firstWhere('id', $logUser),
            'userReceive' => $users->firstWhere('id', $user_id),
            'project' => Project::find($request->project_id)
        ];
        Mail::to($dataReturn['userReceive']->email)->send(new RequestMail($dataReturn));

        //Thêm vào activity
        $name = $dataReturn['userReceive']->name;
        $roleInProject = $request->role == 1 ? 'Admin' : 'Users';
        Activities::addActivity('Teams',"đã mời {$name} với vai trò {$roleInProject} vào dự án",auth()->user()->id,$project_id);
        return response([
            "status" => "success",
            "message" => 'Gửi mail thành công cho người cần mời',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function AcceptRequest($project_id, $user_id)
    {
        $userProject = UserProject::where('project_id', $project_id)->where('user_id', $user_id)->update(['status' => 1]);
        return Redirect::away('https://www.youtube.com/watch?v=mnjaCqz-Qi8'); // a return về đường link về trang web nhé
        
    }

    public function updateRole($project_id, $user_id, $role)
    {
        $logUser = User::returnRole($project_id);
        if ($logUser == 0) {
            return response([
                "status" => "error",
                "message" => 'Bạn không phải admin dự án nên không cho thay đổi role',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //phải có ít nhất một admin
        $roleAdminCount = UserProject::where('project_id', $project_id)->where('status', 1)->where('role', 1)->count();
        if ($roleAdminCount == 1 && $role == 0 && $user_id == auth()->user()->id) {
            return response([
                "status" => "error",
                "message" => 'Có mỗi ông là admin mà ông lại chuyển về user, dự án phải có ít nhất 1 admin',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $userProject = UserProject::where('project_id', $project_id)->where('user_id', $user_id)->update(['role' => $role]);
        $presentRole = User::find($user_id)->role == 1 ? 'Admin' : 'User';
        $updateRole = $role == 1 ? 'Admin' : 'User';
        Activities::addActivity('Teams',"đã thay đổi role của  {$presentRole} sang {$updateRole}",auth()->user()->id,$project_id);
        return response()->json([
            'message' => 'Chuyển role thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy($project_id, $user_id)
    {
        $logUser = auth()->user()->id;
        $userProject = UserProject::where('project_id', $project_id)->where('user_id', $user_id)->with('user')->first();
        if (User::returnRole($project_id) == 0 && $logUser != $userProject->user_id) {
            //Không phải admin và người xóa không phải là chính mình
            return response([
                "status" => "error",
                "message" => 'Bạn không phải admin dự án nên không cho xóa người ra khỏi dự án',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $userProject->delete();
        Activities::addActivity('Teams',"đã xóa người dùng {$userProject->user->name} ra khỏi dự án",auth()->user()->id,$project_id);
        return response()->json([
            'metadata' => $userProject,
            'message' => 'Xóa người dùng thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
