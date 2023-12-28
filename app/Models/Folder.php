<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $table = 'folder';
    protected $fillable = ['FolderName','ParentID','UserID','Tag','ProjectID'];

    public function user()
    {
        return $this->belongsTo(User::class,'UserID');
    }
}
