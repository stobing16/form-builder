<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseDetail extends Model
{
    use HasFactory;
    protected $fillable = ['response_id', 'question_id', 'answer'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
