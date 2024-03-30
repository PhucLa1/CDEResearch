<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comment';
    protected $fillable = [
        'user_id', 'another_id',
        'type', 'content'
    ];
    //type là 0: folder, 1:file, 2 : todo
}
