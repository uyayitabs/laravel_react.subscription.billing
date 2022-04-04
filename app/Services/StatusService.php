<?php

namespace App\Services;

use Logging;
use App\Models\Status;
use Spatie\QueryBuilder\QueryBuilder;

class StatusService
{
    public function list($relation = null)
    {
        $query = QueryBuilder::for(Status::class, request())
            ->allowedFields(Status::$fields)
            ->allowedIncludes(Status::$includes)
            ->defaultSort('-id')
            ->allowedSorts(Status::$sorts);

        return $query;
    }

    public function show($id)
    {
        return QueryBuilder::for(Status::where("id", $id))
            ->allowedFields(Status::$fields)
            ->allowedIncludes(Status::$scopes);
    }

    public function create($queryOnly = true)
    {
        $statusAttributes = request(Status::$fields);
        $status = Status::create($statusAttributes);
        return $this->getOne(['id' => $status->id], $queryOnly);
    }

    public function update(array $data, Status $status)
    {
        if (!is_null($status)) {
            $log['old_values'] = $status->getRawDBData();
            $status->update($data);

            $log['new_values'] = $status->getRawDBData();
            $log['changes'] = $status->getChanges();

            Logging::information('Update Status', $log, 1, 1);
        }
        return $this->list();
    }

    public function delete(Status $status)
    {
        $status->delete();
        return $this->list();
    }

    public function count()
    {
        return Status::get()->count();
    }

    public function getOne($where = [], $queryOnly = true)
    {
        $query = QueryBuilder::for(Status::where($where))
            ->allowedIncludes(Status::$scopes);
        return $queryOnly ? $query : $query->first();
    }

    public function getOptions($statusTypeId)
    {
        $query = Status::where("status_type_id", $statusTypeId);
        return QueryBuilder::for($query)
            ->allowedFields(Status::$fields)
            ->allowedIncludes(Status::$scopes);
    }

    /**
     * Get the status
     * @param string status type
     * @param string status
     * @return object status
     */
    public function getStatus($type, $status)
    {
        return Status::whereHas('type', function ($query) use ($type) {
            $query->where('type', $type);
        })->where('status', $status)->first();
    }


    /**
     * Get the statuses by type
     * @param string status type
     * @param string status
     * @return object status
     */
    public function getStatusesByType($type)
    {
        return Status::whereHas('type', function ($query) use ($type) {
            $query->where('type', $type);
        });
    }


    /**
     * Get the status id
     * @param string status type
     * @param string status
     * @return object status
     */
    public function getStatusId($type, $status)
    {
        return $this->getStatus($type, $status) ? $this->getStatus($type, $status)->id : null;
    }

    public function getStatusByTypeAndId($type, $id)
    {
        return Status::whereHas(
            'type',
            function ($query) use ($type) {
                $query->where('type', $type);
            }
        )->where('id', $id)->first();
    }
}
