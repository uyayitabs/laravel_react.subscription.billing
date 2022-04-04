<?php

namespace App\Services;

use Logging;
use App\Models\StatusType;
use Spatie\QueryBuilder\QueryBuilder;

class StatusTypeService
{
    public function list($relation = null)
    {
        return QueryBuilder::for(StatusType::class, request())
            ->allowedFields(StatusType::$fields)
            ->allowedIncludes(StatusType::$includes)
            ->defaultSort('-id')
            ->allowedSorts(StatusType::$sorts);
    }

    public function show($id)
    {
        return QueryBuilder::for(StatusType::where("id", $id))
            ->allowedFields(StatusType::$fields)
            ->allowedIncludes(StatusType::$scopes);
    }

    public function create($queryOnly = true)
    {
        $statusAttributes = request(StatusType::$fields);
        $statusType = StatusType::create($statusAttributes);
        return $this->getOne(['id' => $statusType->id], $queryOnly);
    }

    public function update(array $data, StatusType $statusType)
    {
        if (!is_null($statusType)) {
            $log['old_values'] = $statusType->getRawDBData();
            $statusType->update($data);

            $log['new_values'] = $statusType->getRawDBData();
            $log['changes'] = $statusType->getChanges();

            Logging::information('Update Status Type', $log, 1, 1);
        }
        return $this->list();
    }

    public function delete(StatusType $statusType)
    {
        $statusType->delete();
        return $this->list();
    }

    public function count()
    {
        return StatusType::get()->count();
    }

    public function getOne($where = [], $queryOnly = true)
    {
        $query = QueryBuilder::for(StatusType::where($where))
            ->allowedIncludes(StatusType::$scopes);
        return $queryOnly ? $query : $query->first();
    }

    /**
     * Get the status id
     * @param string status type
     * @return StatusType
     */
    public function getStatusTypeIdByType($type)
    {
        return StatusType::where('type', $type)->first();
    }
}
