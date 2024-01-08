<?php

namespace App\Rules;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class pairUnique implements Rule
{
    protected $table;
    protected $Column;
    protected $Value;

    public function __construct($table, $Column,$Value){
        $this->table = $table;
        $this->Column = $Column;
        $this->Value = $Value;

    }
    
    public function passes($attribute, $value)
    {
        // Logic kiểm tra và trả về true nếu hợp lệ, false nếu không hợp lệ
    }

    public function message()
    {
        return 'The validation failed.';
    }
}
