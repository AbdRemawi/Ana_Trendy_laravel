<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Update Product Size Enum to Simplified Values
 *
 * Changes:
 * - OLD: S, M, L, XL, XXL
 * - NEW: S, MD, LG
 *
 * This migration simplifies the size options for better UX and mobile display.
 * Existing data will be mapped to the new values:
 * - S -> S (unchanged)
 * - M -> MD
 * - L -> LG
 * - XL -> LG (mapped to large)
 * - XXL -> LG (mapped to large)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL doesn't support direct enum modification
        // We need to recreate the column

        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        // Update existing data to new values before changing the enum
        DB::statement("
            UPDATE products
            SET size = CASE
                WHEN size = 'S' THEN 'S'
                WHEN size = 'M' THEN 'MD'
                WHEN size = 'L' THEN 'LG'
                WHEN size = 'XL' THEN 'LG'
                WHEN size = 'XXL' THEN 'LG'
                ELSE size
            END
        ");

        // Alter the column to use the new enum values
        DB::statement("
            ALTER TABLE products
            MODIFY COLUMN size ENUM('S', 'MD', 'LG')
            NOT NULL
        ");

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        // Map back to old values
        DB::statement("
            UPDATE products
            SET size = CASE
                WHEN size = 'S' THEN 'S'
                WHEN size = 'MD' THEN 'M'
                WHEN size = 'LG' THEN 'L'
                ELSE size
            END
        ");

        // Restore original enum
        DB::statement("
            ALTER TABLE products
            MODIFY COLUMN size ENUM('S', 'M', 'L', 'XL', 'XXL')
            NOT NULL
        ");

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
};
