<?php

use Illuminate\Database\Seeder;

class SeverityTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('severity')->delete();
        
        \DB::table('severity')->insert([
					[
						'id' => 1, 'severity' => 'Emergency', 'keyword' => 'emerg', 
						'description' => 'System is unusable', 'condition' => 'A panic condition.',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 2, 'severity' => 'Alert', 'keyword' => 'alert', 
						'description' => 'Action must be taken immediately', 
						'condition' => 'A condition that should be corrected immediately, such as a corrupted system database.',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 3, 'severity' => 'Critical', 'keyword' => 'crit', 
						'description' => 'Critical conditions', 'condition' => 'Hard device errors.',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 4, 'severity' => 'Error', 'keyword' => 'err', 
						'description' => 'Error conditions', 'condition' => '',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 5, 'severity' => 'Warning', 'keyword' => 'warn', 
						'description' => 'Warning conditions', 'condition' => '',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 6, 'severity' => 'Notice', 'keyword' => 'notice', 
						'description' => 'Normal but significant conditions', 
						'condition' => 'Conditions that are not error conditions, but that may require special handling.',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 7, 'severity' => 'Informational', 'keyword' => 'info', 
						'description' => 'Informational messages', 'condition' => '',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
						'id' => 8, 'severity' => 'Debug', 'keyword' => 'debug', 
						'description' => 'Debug-level messages', 
						'condition' => 'Messages that contain information normally of use only when debugging a program.',
						'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					]
        ]);        
    }
}