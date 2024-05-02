<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    Question,
    QuestionOption
};

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            [
                'title' => 'What is the most interesting thing about you?',
                'type' => Question::QUESTION_TEXT_TYPE,
                'options' => []
            ],
            [
                'title' => 'What do you like to do for fun?',
                'type' => Question::QUESTION_TEXT_TYPE,
                'options' => []
            ],
            [
                'title' => 'Gender',
                'type' => Question::QUESTION_SINGLE_SELECT_TYPE,
                'options' => [
                    [
                        'title' => 'Male',
                        'order' => 1
                    ],
                    [
                        'title' => 'Female',
                        'order' => 2
                    ],
                    [
                        'title' => 'Non-Binary',
                        'order' => 3
                    ],
                    [
                        'title' => 'Other',
                        'order' => 4
                    ]
                ]
            ],
            [
                'title' => 'Relationship status',
                'type' => Question::QUESTION_SINGLE_SELECT_TYPE,
                'options' => [
                    [
                        'title' => 'Single',
                        'order' => 1
                    ],
                    [
                        'title' => 'In a Relationship',
                        'order' => 2
                    ],
                    [
                        'title' => 'Married',
                        'order' => 3
                    ],
                    [
                        'title' => 'Its Complicated',
                        'order' => 4
                    ]
                ]
            ],
            [
                'title' => 'Do you have children',
                'type' => Question::QUESTION_SINGLE_SELECT_TYPE,
                'options' => [
                    [
                        'title' => 'Yes',
                        'order' => 1
                    ],
                    [
                        'title' => 'No',
                        'order' => 2
                    ]
                ]
            ],
            [
                'title' => 'Do you drink alcohol',
                'type' => Question::QUESTION_SINGLE_SELECT_TYPE,
                'options' => [
                    [
                        'title' => 'Never',
                        'order' => 1
                    ],
                    [
                        'title' => 'Socially',
                        'order' => 2
                    ],
                    [
                        'title' => 'Weekly',
                        'order' => 3
                    ],
                    [
                        'title' => 'Daily',
                        'order' => 4
                    ]
                ]
            ],
            [
                'title' => 'Do you smoke',
                'type' => Question::QUESTION_SINGLE_SELECT_TYPE,
                'options' => [
                    [
                        'title' => 'Never',
                        'order' => 1
                    ],
                    [
                        'title' => 'Socially',
                        'order' => 2
                    ],
                    [
                        'title' => 'Weekly',
                        'order' => 3
                    ],
                    [
                        'title' => 'Daily',
                        'order' => 4
                    ]
                ]
            ],
            [
                'title' => 'Do you take illicit drugs',
                'type' => Question::QUESTION_SINGLE_SELECT_TYPE,
                'options' => [
                    [
                        'title' => 'Never',
                        'order' => 1
                    ],
                    [
                        'title' => 'Socially',
                        'order' => 2
                    ],
                    [
                        'title' => 'Weekly',
                        'order' => 3
                    ],
                    [
                        'title' => 'Daily',
                        'order' => 4
                    ]
                ]
            ]
        ];

        foreach ($questions as $key => $question) {
            $create_question = Question::create([
                'title' => $question['title'],
                'type' => $question['type']
            ]);

            foreach ($question['options'] as $option_key => $option) {
                QuestionOption::create([
                    'question_id' => $create_question['id'],
                    'title' => $option['title'],
                    'order' => $option['order']
                ]);
            }
        }
    }
}
