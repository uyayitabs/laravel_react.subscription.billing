<?php

namespace App\Services;

use Jajo\JSONDB;
use Illuminate\Support\Facades\Storage;

class MaintenanceService
{
    protected $jsondb;

    public function __construct()
    {
        $this->jsondb = new JSONDB(storage_path('app/json'));
    }

    public function maintanance($maintenance = 1)
    {
        $maintenance = boolval($maintenance);
        $exists = \File::exists(storage_path('app/json/config.json'));
        if (!$exists) {
            Storage::put('json/config.json', '[]');
        }

        $mt = $this->jsondb->select('config')
            ->from('config.json')
            ->where(['config' => 'maintenance'])
            ->get();

        if ($mt) {
            $this->jsondb->update(['value' => boolval($maintenance)])
                ->from('config.json')
                ->where(['config' => 'maintenance'])
                ->trigger();
        } else {
            $this->jsondb->insert('config.json', ['config' => 'maintenance', 'value' => $maintenance]);
        }
    }
}
