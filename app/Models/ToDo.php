<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\TagCast;


class ToDo extends Model
{
    use HasFactory;
    protected $table = 'todo';
    protected $fillable = [
        'name', 'explorer_id', 'title',
        'descriptions', 'start_date', 'finish_date',
        'priorities', 'tag', 'project_id', 'assgin_to'
    ];
    protected $casts = [
        'tag' => TagCast::class,
    ];
}
