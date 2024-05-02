<?php

namespace App\Repositories;

use App\Models\{
    Question,
    Answer
};
use Illuminate\Support\Facades\Auth;

class QuestionRepository
{
    public function questions($filter)
    {
        $query = Question::with('options', 'answer');
        if (!empty($filter)) {
            foreach ($filter as $key => $type) {
                $query = $query->orWhere('type', $type);
            }
        }

        return $query->get();
    }

    public function saveAnswers($questions)
    {
        $user_id = Auth::user()->id;
        foreach ($questions as $key => $question) {
            $check = Answer::where(['user_id' => $user_id, 'question_id' => $question['id']])->first();

            if ($check) {
                $check->answer_id = $question['type'] != 'text' ? $question['answer'] : null;
                $check->answer = $question['type'] == 'text' ? $question['answer'] : null;
                $check->save();
            } else {
                Answer::create([
                    'user_id' => $user_id,
                    'question_id' => $question['id'],
                    'answer_id' => $question['type'] != 'text' ? $question['answer'] : null,
                    'answer' => $question['type'] == 'text' ? $question['answer'] : null
                ]);
            }
        }

        return true;
    }
}
