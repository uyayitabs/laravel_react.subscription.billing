<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Repository
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct($model)
    {
        $this->model = $model;
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

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model::with($relations);
    }

    // Get the associated model
    public function getAll($where = [])
    {
        return $this->model::where($where)->get();
    }

    // Get the associated model
    public function getOne($where = [])
    {
        return $this->model::where($where)->first();
    }
}
