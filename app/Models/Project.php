<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'project';
    protected $fillable = [
                            'name','user_id','thumbnails','note','start_date',
                            'finish_date','todo_permission','invite_permission'
                        ];
}
