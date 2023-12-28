<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ToDo extends Model
{
    use HasFactory;
    protected $table = 'todo';
    protected $fillable = [
                            'Name','UserID','FileID','Title',
                            'Description','StartDate','FinishDate',
                            'TDStatus','Priorities','Tag','ProjectID'
                        ];


}
