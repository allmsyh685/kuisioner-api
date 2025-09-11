<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('age');
            $table->string('location');
            $table->enum('education_level', ['smp', 'sma', 'mahasiswa', 'lainnya']);
            $table->enum('ai_usage_frequency', ['Beberapa kali dalam sebulan', 'Beberapa kali dalam seminggu', 'belum pernah', 'setiap hari']);
            $table->enum('ai_purpose', ['Pendidikan', 'pekerjaan', 'hiburan', 'Lainnya']);
            $table->enum('ai_tool_used', ['chatgpt', 'deepseek', 'grok', 'lainnya']);
            $table->enum('difficulty_without_ai', ['benar sekali', 'benar', 'tidak benar', 'tidak benar sama sekali']);
            $table->enum('anxiety_without_ai', ['benar sekali', 'benar', 'tidak benar', 'tidak benar sama sekali']);
            $table->enum('ai_important_routine', ['benar sekali', 'benar', 'tidak benar', 'tidak benar sama sekali']);
            $table->enum('more_productive_with_ai', ['benar sekali', 'benar', 'tidak benar', 'tidak benar sama sekali']);
            $table->enum('rely_on_ai_decisions', ['benar sekali', 'benar', 'tidak benar', 'tidak benar sama sekali']);
            $table->enum('ai_better_than_humans', ['benar sekali', 'benar', 'tidak benar', 'tidak benar sama sekali']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};



