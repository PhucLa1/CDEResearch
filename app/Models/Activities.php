<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;
    protected $table = 'activities';
    protected $fillable = [
        'type', 'content', 'user_id','project_id'
    ];
    //Làm phần teams trước

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Hàm thêm vào activity
    public static function addActivity($type, $content, $user_id,  $project_id)
    {
        $dataAdd = Activities::create([
            'type' => $type,
            'content' => $content,
            'user_id' => $user_id,
            'project_id' => $project_id
        ]);
    }
}
