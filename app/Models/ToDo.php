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
        'name', 'files_id ', 'title',
        'descriptions', 'start_date', 'finish_date',
        'priorities', 'tag', 'project_id', 'assgin_to'
    ];
    protected $casts = [
        'tag' => TagCast::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assgin_to', 'email');
    }
    public function file()
    {
        return $this->belongsTo(Files::class, 'files_id');
    }
}
