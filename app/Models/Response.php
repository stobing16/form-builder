<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;
    protected $fillable = ['form_id', 'name', 'email', 'phone'];

    public function details()
    {
        return $this->hasMany(ResponseDetail::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
