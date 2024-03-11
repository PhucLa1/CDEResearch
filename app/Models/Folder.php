<?php

namespace App\Models;

use App\Casts\TagCast;
use Defuse\Crypto\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $table = 'folder';
    protected $fillable = [
        'name', 'parent_id', 
        'project_id','user_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

}
