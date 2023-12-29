<?php

namespace App\Http\Controllers;

use Mail;
use App\Mail\RequestMail;
use Illuminate\Validation\Rule;
use App\Models\UserProject;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JoinController extends Controller
{
    //
    public function index($project_id){
        $teams = UserProject::where('ProjectId', $project_id)
        ->with('user')
        ->with('project')
        ->get()
        ->map(function($team){
            $team->Role = ($team->Role = 1)?'Admin':'User';
            $team->Status = ($team->Status = 1)?'Active':'Pending Invite';
            return $team;
        });
        return response()->json([
            'metadata' => $teams,
            'message' => 'Get all records from Tag',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function SendEmail(Request $request){
        $validator = Validator::make($request->all(),[
            'ProjectID' => 'required',
            'UserID' => ['required',        
                        Rule::unique('userproject')->where(function ($query) use ($request) {
                            return $query->where('ProjectID', $request->ProjectID);
                        })],
            'Role' => 'required',
            
        ],[
            'ProjectID.required' => 'Id của project không được để trống ',
            'UserID.required' => 'Id của user không được để trống',
            'UserID.unique' => 'Cặp ProjectID và UserID đã tồn tại',
            'Role.required' => 'Role của người dùng trong dự án đó phải được chọn'
        ]);
        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $project = Project::find($request->ProjectID);
        if($project->invite_permission == 1 && User::returnRole($request->ProjectID) == 0){
            // nếu project chỉ cho admin mời
            // Thằng đăng nhập cũng không phải admin
            return response([
                "status" => "error",
                "message" => 'Bạn không phải admin dự án và dự án không cấp phép cho bạn mời người ngoài',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $userAddPendingInvite = UserProject::create($request->all());
        //Send Email
        $logUser = auth()->user()->id;
        $userIds = [$logUser, $request->UserID];
        $users = User::findMany($userIds);
        
        $dataReturn = [
            'userSend' => $users->firstWhere('id', $logUser),
            'userReceive' => $users->firstWhere('id', $request->UserID),
            'project' => Project::find($request->ProjectID)
        ];
        Mail::to($dataReturn['userReceive']->email)->send(new RequestMail($dataReturn));

        return response([
            "status" => "success",
            "message" => 'Gửi mail thành công cho người cần mời',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK); 
    }

    public function AcceptRequest($project_id,$user_id){
        $userProject = UserProject::where('ProjectID',$project_id)->where('UserID',$user_id)->update(['Status' => 1]);
        return Redirect::away('https://www.youtube.com/watch?v=mnjaCqz-Qi8');
    }

    public function updateRole($project_id,$user_id,$role){
        $logUser = User::returnRole($project_id);
        if($logUser == 0){
            return response([
                "status" => "error",
                "message" => 'Bạn không phải admin dự án nên không cho thay đổi role',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $userProject = UserProject::where('ProjectID',$project_id)->where('UserID',$user_id)->update(['Role' => $role]);

        return response()->json([
            'message' => 'Chuyển role thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy($project_id,$user_id){
        $logUser = User::returnRole($project_id);
        if($logUser == 0){
            return response([
                "status" => "error",
                "message" => 'Bạn không phải admin dự án nên không cho xóa người ra khỏi dự án',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        $userProject = UserProject::where('ProjectID',$project_id)->where('UserID',$user_id)->delete();
        return response()->json([
            'metadata' => $userProject,
            'message' => 'Xóa người dùng thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
