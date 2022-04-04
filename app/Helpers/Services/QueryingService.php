<?php

namespace App\Helpers\Services;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\isEmpty;

class QueryingService
{
    //
    protected $queryModel;
    protected $query;
    protected $model;
    private $selectFillable;
    private $isSortSet;
    private $isFilterSet;

    private function createSubModel($valName, &$refModel)
    {
        if (!str_contains($valName, '.')) {
            return (object)[
            'fieldName' => $valName,
            'refModel' => &$refModel
            ];
        }
        $modelName = substr($valName, 0, strpos($valName, '.')); //subscription (or plan on second loop)
        $fieldName = substr($valName, strpos($valName, '.') + 1); //id or plan.id (or id on second loop)
        //First we check if model already exists in our queryModel,
        // in case we need to go deeper we have to create it first.
        if (!isset($refModel['models'][$modelName])) {
            $newModel = [
                'modelClass' => 'App\\' . $modelName,
                'modelName' => $modelName,
                'models' => [],
                'select' => [],
                'filter' => [],
                'sort' => [],
                'search' => []
            ];
            $refModel['models'][$modelName] = $newModel; //assign model in tree
        }
        //and then finally make subscription.id = id
        // or subscription.plan.id = plan.id, in which case we need to loop
        if (str_contains($valName, '.')) {
            return $this->createSubModel($fieldName, $refModel['models'][$modelName]);
        }
        return (object)[
            'fieldName' => $valName,
            'refModel' => &$refModel
        ];
    }

    // Set the associated model
    public function for($model)
    {
        $this->queryModel = [
            'model' => [
                'select' => [],
                'filter' => [],
                'sort' => [],
                'models' => [],
                'modelClass' => $model,
                'modelName' => substr($model, (strrpos($model, '\\') + 1)),
                'search' => []
            ],
        ];
        $this->query = $model::query();
        $this->model = $model;
        return $this;
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model::create($data);
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->model::find($id);
        return $record->update($data);
    }

    // update record in the database
    public function updateWhere(array $where, array $data)
    {
        $record = $this->model::where($where);
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model::destroy($id);
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model::findOrFail($id);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model::with($relations);
    }

    // Get the associated model
    public function getAll($where = [])
    {
        if ($this->query) {
            return $this->query->where($where)->get();
        }
        return $this->model::where($where)->get();
    }

    // Get the associated model
    public function getFirst($where = [])
    {
        if ($this->query) {
            return $this->query->where($where)->first();
        }
        return $this->model::where($where)->first();
    }

    // Get the associated model
    public function getSeries(int $take, int $skip, $where = [])
    {
        if ($this->query) {
            return $this->query->where($where)->skip($skip)->take($take)->get();
        }
        return $this->model::where($where)->skip($skip)->take($take)->get();
    }

    // Get the associated model
    public function getQuery()
    {
        return $this->query;
    }

    public function enableFillableSelect()
    {
        $this->selectFillable = true;
        return $this;
    }

    public function setSelectables($selectables)
    {
        if (!empty($selectables)) {
            $selectables = explode(',', $selectables);
        } elseif ($this->selectFillable) {
            $selectables = $this->model::$fields;
        }
        if (empty($selectables)) {
            return $this;
        }
        foreach ($selectables as $selectable) {
            if (empty($selectable)) {
                continue;
            }

            if (!in_array($selectable, $this->queryModel['model']['modelClass']::$fields)) {
                continue; //Cannot filter Field
            }
            $result = $this->createSubModel($selectable, $this->queryModel['model']);
            $result->refModel['select'][] = $result->fieldName;
        }
        return $this;
    }

    public function setFilter($filterString)
    {
        if (!isset($filterString) || empty($filterString)) {
            return $this;
        }
        $filters = [];
        $stringPosition = 0;
        while ($stringPosition < strlen($filterString)) {
            $posComma = strpos($filterString, ',', $stringPosition);
            $posOpenBracket = strpos($filterString, '[', $stringPosition);
            // In case of array value
            if ($posOpenBracket !== false && ($posOpenBracket > 0 || $posOpenBracket < $posComma)) {
                $posClosingBracket = strpos($filterString, ']', $stringPosition);
                if ($posClosingBracket == 0) {
                    return $this;
                }
                $posEnd = strpos($filterString, ',', $posClosingBracket);
            } else {
                $posEnd = $posComma;
            }
            if ($posEnd == 0 || !$posEnd) {
                $posEnd = strlen($filterString);
            }
            $filters[] = substr($filterString, $stringPosition, $posEnd);
            $stringPosition = $posEnd + 1;
        }

        foreach ($filters as $filter) {
            if (empty($filter)) {
                continue;
            }
            preg_match('/[><=%]/', $filter, $operatorMatch, PREG_OFFSET_CAPTURE);
            if (!isset($operatorMatch) || empty($operatorMatch)) {
                continue;
            }
            $field = substr($filter, 0, $operatorMatch[0][1]);
            $operator = $operatorMatch[0][0];
            $value = substr($filter, $operatorMatch[0][1] + 1);
            // Logic for fuzzy search filter
            if ($operator === '%') {
                $value = '%' . $value . '%';
                $operator = 'like';
            }
            // Logic for array / in filter
            if ($value[0] === '[' && $value[strlen($value) - 1] === ']') {
                $operator = 'in';
                $value = substr($value, 1, strlen($value) - 2);
                $value = explode(',', $value);
            }

            if (!in_array($field, $this->queryModel['model']['modelClass']::$filters)) {
                continue; //Cannot filter Field
            }

            $this->isFilterSet = true;
            $result = $this->createSubModel($filter, $this->queryModel['model']);
            $result->refModel['filter'][] = ['field' => $field, 'operator' => $operator, 'value' => $value];
        }
        return $this;
    }

    public function setSearch($search): self
    {
        if (!isset($search) || empty($search)) {
            return $this;
        }
        preg_match('/[ ,]/', $search, $matches, PREG_OFFSET_CAPTURE);

        if (!$matches) {
            $matches = [];
        }
        $i = -1;
        $pos = 0;

        do {
            $i++;
            $operator = $pos === 0 ? ' ' : substr($search, $pos, 1);
            $end = isset($matches[$i]) ? $matches[$i][1] : null;
            // If there are more matches go to next
            // Else go to end of string
            if ($end) {
                $searchTerm = substr($search, $pos, $end);
            } else {
                $searchTerm = substr($search, $pos);
            }

            $this->queryModel['model']['search'][] = [
                'searchTerm' => $searchTerm,
                'operator' => $operator
            ];
            // If there is a next match, set start of pos for next loop
            if (isset($matches[$i])) {
                $pos = $matches[$i][1];
            }
        } while ($i < count($matches));

        return $this;
    }

    public function setSortable($sorters): self
    {
        if (!isset($sorters) || empty($sorters)) {
            return $this;
        }
        $sorters = explode(',', $sorters);
        foreach ($sorters as $sorter) {
            if (empty($sorter)) {
                continue;
            }
            $direction = "ASC";
            if (substr($sorter, 0, 1) == '-') {
                $direction = "DESC";
                $sorter = substr($sorter, 1);
            }

            if (!in_array($sorter, $this->queryModel['model']['modelClass']::$sortables)) {
                continue; //Cannot filter Field
            }

            $this->isSortSet = true;
            $result = $this->createSubModel($sorter, $this->queryModel['model']);
            $result->refModel['sort'][] = ['field' => $sorter, 'direction' => $direction];
        }
        return $this;
    }

    public function make(): self
    {
        if (!$this->query) {
            $this->query = $this->model::query();
        }
        $this->makeQueryForModel($this->queryModel['model'], $this->query);
        return $this;
    }

    private function makeQueryForModel($model, $query)
    {
        // Hide fields that we want to select but shouldn't
        $count = count($model['select']);
        for ($i = 0; $i < $count; $i++) {
            if (in_array($model['select'][$i], $model['modelClass']::$hiders)) {
                unset($model['select'][$i]);
            }
        }

        // If select is set then select those fields
        if (!empty($model['select'])) {
            $query->select($model['select']);
        }

        // If search exists then add to query LIKE '%{search}%' for every field in DataModel - searchable
        $query->when($model['search'], function ($q) use ($model) {
            foreach ($model['search'] as $s) {
                if ($s['operator'] === ',') {
                    $q->orWhere(function ($q2) use ($s, $model) {
                        foreach ($model['select'] as $s2) {
                            if (!in_array($s2, $model['modelClass']::$searchables)) {
                                continue;
                            }
                            $q2->orWhere($s2, 'like', '%' . $s['searchTerm'] . '%');
                        }
                    });
                } else {
                    $q->where(function ($q2) use ($s, $model) {
                        foreach ($model['select'] as $s2) {
                            if (!in_array($s2, $model['modelClass']::$searchables)) {
                                continue;
                            }
                            $q2->orWhere($s2, 'like', '%' . $s['searchTerm'] . '%');
                        }
                    });
                }
            }
        });

        // Then determine order as defined by sort
        foreach ($model['sort'] as $s) {
            $query->orderBy($s['field'], $s['direction'])->whereNotNull($s['field']);
        }
        // Then apply filters as defined by filter
        foreach ($model['filter'] as $f) {
            if ($f['operator'] === 'in') {
                $query->whereIn($f['field'], $f['value']);
            } else {
                ($query->where($f['field'], $f['operator'], $f['value']));
            }
        }
        // And finally loop through submodels to make those queries too
        foreach ($model['models'] as $subModel) { //Nested logic. FUN!
            $query->with([$subModel['modelName'] => function ($q) use ($subModel) {
                $this->makeQueryForModel($subModel, $q);
            }]);
        }
    }

    public function defaultSort($sort): self
    {
        if (!$this->isSortSet) {
            $this->setSortable($sort);
        }
        return $this;
    }

    public function defaultFilter($filter): self
    {
        if (!$this->isFilterSet) {
            $this->setFilter($filter);
        }
        return $this;
    }

    public function count()
    {
        if ($this->query) {
            return $this->query->count();
        }
        return $this->model::count();
    }
}
