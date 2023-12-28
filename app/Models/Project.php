<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'project';
    protected $fillable = [
                            'ProjectName','thumbnail','Note','StartDate',
                            'FinishDate','Status','todo_permission','invite_permission'
                        ];
}