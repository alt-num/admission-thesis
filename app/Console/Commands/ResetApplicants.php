<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetApplicants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:applicants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely reset all applicant-related data without touching exams, questions, or admission staff';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting applicant data reset...');
        $this->newLine();

        try {
            // Truncate tables in the specified order using CASCADE
            $tables = [
                'exam_answers',
                'exam_subsection_scores',
                'exam_attempts',
                'applicant_exam_schedules',
                'applicant_course_results',
                'applicant_declarations',
                'applicant_users',
                'applicants',
            ];

            // Optional tables (only if they exist)
            $optionalTables = [
                'exam_activity_logs',
                'anti_cheat_logs',
            ];

            // Truncate required tables
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::statement("TRUNCATE {$table} CASCADE;");
                    $this->info("Cleared: {$table}");
                } else {
                    $this->warn("⚠ Table '{$table}' does not exist, skipping...");
                }
            }

            // Truncate optional tables if they exist
            foreach ($optionalTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::statement("TRUNCATE {$table} CASCADE;");
                    $this->info("Cleared: {$table}");
                }
            }

            $this->newLine();
            $this->info('✅ Applicant data reset completed successfully!');
            $this->info('   - Exam structures remain intact');
            $this->info('   - Admission accounts remain intact');
            $this->info('   - System settings remain intact');
            $this->newLine();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error resetting applicant data: ' . $e->getMessage());
            $this->newLine();
            return Command::FAILURE;
        }
    }
}
