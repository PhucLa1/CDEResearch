<?php

namespace App\Models;

use App\Casts\TagCast;
use Defuse\Crypto\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Explorer extends Model
{
    use HasFactory;
    protected $table = 'explorer';
    protected $fillable = [
        'name', 'versions',
        'note', 'parent_id', 
        'project_id','user_id',
        'tag','type','url'
    ];

    protected $casts = [
        'Tag' => TagCast::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'ProjectID');
    }

}
