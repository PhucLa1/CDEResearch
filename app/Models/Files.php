<?php

namespace App\Models;

use App\Casts\TagCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    protected $table = 'files';
    protected $fillable = [
        'name', 'versions',
        'note', 'folder_id',
        'project_id', 'user_id',
        'tag', 'url', 'size', 'first_version', 'status'
    ];
    protected $casts = [
        'tag' => TagCast::class,
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
