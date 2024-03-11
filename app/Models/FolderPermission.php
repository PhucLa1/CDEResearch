<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderPermission extends Model
{
    use HasFactory;
    protected $table = 'folder_permis';
    protected $fillable = ['user_id','folder_id','permission'];
}
