<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['text', 'radio'];

        QuestionType::create([
            'type' => 'text',
            'label' => 'Jawaban Singkat',
            'has_options' => false,
            'is_active' => true,
        ]);

        QuestionType::create([
            'type' => 'longtext',
            'label' => 'Paragraf',
            'has_options' => false,
            'is_active' => true,
        ]);

        QuestionType::create([
            'type' => 'radio',
            'label' => 'Pilihan Ganda',
            'has_options' => true,
            'is_active' => true,
        ]);

        QuestionType::create([
            'type' => 'checkbox',
            'label' => 'Kotak Centang',
            'has_options' => true,
            'is_active' => true,
        ]);

        QuestionType::create([
            'type' => 'select',
            'label' => 'Select Option',
            'has_options' => true,
            'is_active' => true,
        ]);
    }
}
