<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'project';
    protected $fillable = [
                            'ProjectName','UserID','FileID','Title',
                            'Description','StartDate','FinishDate',
                            'TDStatus','Priorities','Tag'
                        ];
}
