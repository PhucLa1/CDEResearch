<?php

namespace App\Exports;

use App\Models\Activities;
use Google\Service\Docs\Request;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActivitiesExport implements FromCollection, WithTitle, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $project_id;
    protected $arrayType;
    protected $arrayUserId;
    protected $start_date;
    protected $finish_date;

    public function __construct($project_id, $arrayType, $arrayUserId, $start_date, $finish_date)
    {
        $this->project_id = $project_id;
        $this->arrayType = $arrayType;
        $this->arrayUserId = $arrayUserId;
        $this->start_date = $start_date;
        $this->finish_date = $finish_date;
    }
    public function collection()
    {
        $activities = Activities::where('project_id', $this->project_id)
            ->whereBetween('activities.created_at', [$this->start_date, $this->finish_date])
            ->join('users', 'activities.user_id', '=', 'users.id')
            ->select(
                'activities.id',
                'activities.type',
                DB::raw("CONCAT(users.name, ' ', activities.content) as content"),
                'users.name as user_name',
                'users.email as user_email',
                DB::raw("DATE_FORMAT(activities.created_at, '%d-%m-%Y %H:%i:%s') as formatted_created_at")
            );
        if (count($this->arrayType) != 0) {
            $activities = $activities->whereIn('type', $this->arrayType);
        }
        if (count($this->arrayUserId) != 0) {
            $activities = $activities->whereIn('user_id', $this->arrayUserId);
        }
        //return Activities::all();
        return $activities->get();
    }
    public function title(): string
    {
        return 'Lịch sử hoạt động của dự án';
    }
    public function headings(): array
    {
        return [
            'STT',
            'Loại công việc',
            'Nội dung công việc',
            'Người thực hiện',
            'Email',
            'Thời gian thực hiện'
            //Thêm các tên cột khác tùy theo nhu cầu của bạn
        ];
    }
}
