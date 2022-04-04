<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Tenant;

class FiberSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tenant = Tenant::find(7);

        if (empty($tenant->settings->portal)) {
            $portal["portal"]["logo"] = config('app.asset_url') . "/fiber/logo.72.png?v=" . Str::random(6);
            $portal["portal"]["body"]["fontFamily"] = "Open,Sans-serif";
            $portal["portal"]["body"]["color"] = "#555";
            $portal["portal"]["headings"]["fontFamily"] = "'Rockwell Bold', 'Rockwell Regular', 'Rockwell', sans-serif";
            $portal["portal"]["headings"]["color"] = "#74d1f6";
            $portal["portal"]["back_link"] = [
                "url" => "https://www.fiber.nl",
                "title" => "Terug naar Fiber.nl >"
            ];
        } else {
            $portal = [];
        }
        $newSettings = array_merge((empty($tenant->settings) ? [] : (array) $tenant->settings), $portal);

        Tenant::where(function ($query) {
            $query->where('id', 7);
        })->update(['settings' => $newSettings]);
    }
}
