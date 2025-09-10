<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Enable pgvector extension (PostgreSQL)
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
    }

    public function down(): void
    {
        // Do not drop extension by default as others may depend on it
        // DB::statement('DROP EXTENSION IF EXISTS vector');
    }
};

