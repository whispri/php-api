<?php

namespace App\Repositories;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all(array $params = [])
    {
        $builder = $this->model::search('*');
        if (!empty($params['query'])) {
            $builder = $this->model::search($params['query']);
        }
        if (!empty($params['filters']) && is_array($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                $builder->where($field, $value);
            }
        }
        if (!empty($params['sort_by'])) {
            $dataExclude = ['created_at', 'updated_at', 'id'];
            if (!in_array($params['sort_by'], $dataExclude)) {
                $params['sort_by'] = $params['sort_by'] . '.keyword';
            }
            $builder->orderBy($params['sort_by'], $params['sort_order']);
        }

        if (!empty($params['page']) && !empty($params['per_page'])) {
            return $builder->take($params['page'])->paginate($params['per_page']);
        }
        return $builder->take(10000)->paginate(10000);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model::find($id, $columns);
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }
    public function update(array $data, $id)
    {
        $model = $this->find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }
    public function delete($id)
    {
        $model = $this->find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }
    public function findBy($field, $value, $columns = ['*'])
    {
        return $this->model::where($field, $value)->first($columns);
    }

    public function createOrUpdate(array $data, $id = null)
    {
        if ($id) {
            $model = $this->find($id);
            if ($model) {
                $model->update($data);
                return $model;
            }
        }
        return $this->create($data);
    }

    public function firstOrCreate(array $data)
    {
        return $this->model::firstOrCreate($data);
    }
}
