<?php

namespace App\Helpers\Services;

use App\Models\LogActivity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

/// This class is meant as a replacement for addToLog
/// The method was used in a lot of varying ways
class LoggingService
{
    public function debug($message, $json_data, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'debug',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function information($message, $json_data, $facility_id, $status = 1, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'info',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function warning($message, $json_data, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'warn',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function error($message, $json_data, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'err',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function critical($message, $json_data, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'crit',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function exception(\Exception $e, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $log = $this->createLogActivity(
            $e->getMessage(),
            $e->getTraceAsString(),
            $facility_id,
            $status,
            'crit',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function exceptionWithMessage(\Exception $e, $message, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        $json_data = [];
        $json_data['exception_message'] = $e->getMessage();
        $json_data['exception_stacktrace'] = $e->getTraceAsString();
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'crit',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    public function exceptionWithData(\Exception $e, $message, $json_data, $facility_id, $status = 0, $tenant_id = null, $relatedEntityType = null, $relatedEntityId = null)
    {
        if (!isset($json_data)) {
            $json_data = [];
        }
        $json_data['exception_message'] = $e->getMessage();
        $json_data['exception_stacktrace'] = $e->getTraceAsString();
        $log = $this->createLogActivity(
            $message,
            $json_data,
            $facility_id,
            $status,
            'crit',
            $tenant_id,
            $relatedEntityType,
            $relatedEntityId
        );
        LogActivity::create($log);
    }

    private function createLogActivity($message, $json_data, $facility_id, $status, $severity, $tenant_id, $relatedEntityType, $relatedEntityId)
    {
        $log = [];
        $log['message'] = ('array' == gettype($message)) ? Str::limit(json_encode($message), 190, '') : Str::limit($message, 190, '');
        $log['json_data'] = $json_data;
        $log['tenant_id'] = isset($tenant_id) ? $tenant_id : currentTenant('id');
        $log['facility_id'] = isset($facility_id) ? $facility_id : null;
        $log['severity'] = $severity;
        $log['status'] = $status;
        $log['related_entity_type'] = $relatedEntityType;
        $log['related_entity_id'] = $relatedEntityId;

        $log['user_id'] = auth()->check() ? auth()->user()->id : null;
        $log['username'] = auth()->check() ? auth()->user()->username : null;
        $log['url'] = request()->fullUrl();
        $log['method'] = request()->method();
        $log['ip'] = request()->ip();
        $log['agent'] = request()->header('user-agent');

        return $log;
    }
}
