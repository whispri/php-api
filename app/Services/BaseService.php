<?php

namespace App\Services;

abstract class BaseService implements ServiceInterface
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function all($data)
    {
        $filters = $data->filters();
        $sort = $data->sort();
        $pagination = $data->pagination();
        $data = array_merge($filters, $sort, $pagination);
        return $this->repository->all($data);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }
    public function create(array $data)
    {
        return $this->repository->create($data);
    }
    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
    public function delete($id)
    {
        return $this->repository->delete($id);
    }
    public function findBy($field, $value, $columns = ['*'])
    {
        return $this->repository->findBy($field, $value, $columns);
    }

    public function createOrUpdate(array $data, $id = null)
    {
        return $this->repository->createOrUpdate($data, $id);
    }
}
