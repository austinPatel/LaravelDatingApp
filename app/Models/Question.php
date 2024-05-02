<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Question extends Model
{
    use HasFactory;

    const QUESTION_TEXT_TYPE = 'text';
    const QUESTION_SINGLE_SELECT_TYPE = 'single_select';
    const QUESTION_MULTI_SELECT_TYPE = 'multi_select';

    protected $fillable = [
        'title',
        'type'
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order', 'asc');
    }

    public function answer()
    {
        return $this->hasOne(Answer::class)->where('user_id', Auth::user()->id);
    }
}
