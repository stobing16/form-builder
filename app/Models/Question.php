<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'question', 'catatan', 'slug', 'question_type_id', 'is_required', 'options'];

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }
}
