<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProject extends Model
{
    use HasFactory;
    protected $table = 'userproject';
    protected $fillable = ['UserID','ProjectID','Role','Status'];

    public function user(){
        return $this->belongsTo(User::class,'UserID');
    }
    public function project(){
        return $this->belongsTo(Project::class,'ProjectID');
    }
}
