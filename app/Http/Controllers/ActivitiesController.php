<?php

namespace App\Http\Controllers;

use App\Exports\ActivitiesExport;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activities;
use App\Models\UserProject;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)  //Vừa để hiện ra vừa để tải xuống
    {
        $type = $request->type ? $request->type : '';
        $user_id = $request->user_id ? $request->user_id : '';
        $arrayType = ($type != '') ? explode(',', $type) : [];
        $arrayUserId = ($user_id != '') ? explode(',', $user_id) : [];
        $start_date = $request->start_date ? $request->start_date : '1000-11-04';
        $finish_date = $request->finish_date ? $request->finish_date : '3000-11-04';
        $export = $request->export ? $request->export : 0; //1 ,0 : 1 là có export, 0 : không export
        if(!$request->project_id){
            return response()->json([
                'message' => 'Không có project id',
                'status' => 'success',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //2 mốc thời gian trên nó sẽ vượt khỏi các giá trị
        $activities = Activities::where('project_id', $request->project_id)
            ->whereBetween('created_at', [$start_date, $finish_date])
            ->with('user');
        if(count($arrayType) != 0){
            $activities = $activities->whereIn('type', $arrayType);
        }
        if(count($arrayUserId) != 0){
            $activities = $activities->whereIn('user_id', $arrayUserId);
        }
        if($export == 1){
            $fileName = 'activities-'.time().'.xlsx';
            $excel = Excel::download(
                new ActivitiesExport($request->project_id, $arrayType, $arrayUserId, $start_date, $finish_date),
                $fileName
            );

            $excelFile = $excel->getFile(); // Lấy đối tượng tệp Excel
            $excelFile->move(public_path('Exports'), $fileName);
            return response()->json([
                'metadata' => 'http://127.0.0.1:8000/Exports/'.$fileName,
                'message' => 'Trả về đường dẫn file thành công',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
        return response()->json([
            'metadata' => $activities->get(),
            'message' => 'Lấy ra thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    //List add user in project
    public function listAllUserInProject($project_id)
    {
        $userInProject = UserProject::where('project_id',$project_id)->with('user')->get();
        return response()->json([
            'metadata' => $userInProject,
            'message' => 'Lấy ra thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
