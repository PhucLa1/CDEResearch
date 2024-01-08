<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderPermission extends Model
{
    use HasFactory;
    protected $table = 'explorer_permissions';
    protected $fillable = ['user_id','explorer_id','permission'];
}
