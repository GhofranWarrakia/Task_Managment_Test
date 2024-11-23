<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
        protected $fillable = ['message', 'stack_trace', 'context','file' ,'line','trace'];

}
