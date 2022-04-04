<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\BackendApi;
use Carbon\Carbon;

class BackendApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now('utc')->toDateTimeString();
        $data = [
            ['backend_api' => 'm7', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['backend_api' => 'brightblue', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['backend_api' => 'lineProvisioning', 'status' => 1, 'created_at' => $now, 'updated_at' => $now]
        ];
        BackendApi::insert($data);
    }
}
