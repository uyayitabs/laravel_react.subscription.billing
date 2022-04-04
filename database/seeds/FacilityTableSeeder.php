<?php

use Illuminate\Database\Seeder;

class FacilityTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('facility')->delete();
        
        \DB::table('facility')->insert([
					[
							'id' => 1, 'keyword' => 'user', 'description' => 'User-level messages',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 2, 'keyword' => 'mail', 'description' => 'Mail system',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 3, 'keyword' => 'daemon', 'description' => 'System daemons',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 4, 'keyword' => 'auth', 'description' => 'Security/authentication messages',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 5, 'keyword' => 'syslog', 'description' => 'Messages generated internally by syslogd',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 6, 'keyword' => 'lpr', 'description' => 'Line printer subsystem',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 7, 'keyword' => 'news', 'description' => 'Network news subsystem',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 8, 'keyword' => 'uucp', 'description' => 'UUCP subsystem',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 9, 'keyword' => 'cron', 'description' => 'Clock daemon',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 10, 'keyword' => 'authpriv', 'description' => 'Security/authentication messages',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 11, 'keyword' => 'ftp', 'description' => 'FTP deamon',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 12, 'keyword' => 'ntp', 'description' => 'NTP sussytem',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 13, 'keyword' => 'security', 'description' => 'Log audit',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 14, 'keyword' => 'console', 'description' => 'Log alert',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 15, 'keyword' => 'solaris-cron', 'description' => 'Scheduling daemon',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 16, 'keyword' => 'local0', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 17, 'keyword' => 'local1', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 18, 'keyword' => 'local2', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 19, 'keyword' => 'local3', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 20, 'keyword' => 'local4', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 21, 'keyword' => 'local5', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 22, 'keyword' => 'local6', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					],[
							'id' => 23, 'keyword' => 'local7', 'description' => 'Locally used facilities',
							'created_at' => '2019-06-15 09:25:12','updated_at' => '2019-06-15 09:25:12',
					]
        ]);        
    }
}