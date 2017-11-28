<?php

namespace faiverson\gateways\abstracts;

use faiverson\gateways\contracts\RepositoryInterface;
use faiverson\gateways\exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;

/**
 * Class Repository
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var mixed model
     */
    protected $model;

    protected $paginate;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->model = $app->make($this->model());
        if (!$this->model instanceof Model) {
            throw new RepositoryException(
                "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
        $this->paginate = $app['config']['repositories']['paginate'];
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * @param array $columns
     * @return mixed
     */
    public function all(
        $columns = ['*'],
        $order_by = null,
        $limit = null,
        $offset = null,
        $filters = [],
        $with = []
    ) {
        $query = $this->model;
        foreach ($with as $join) {
            $query = $query->with($join);
        }
        $query = $this->setFilters($query, $filters);
        $query = $this->orderQuery($query, $order_by);
        return $query->get($columns);
    }

    public function setFilters($query, Array $filters)
    {
        return $query;
    }

    public function paginate(
        $page = null,
        $pageName = 'page',
        $perPage = null,
        $columns = ['*'],
        $order_by = null,
        $filters = [],
        $with = []
    ) {
        $query = $this->model;
        foreach ($with as $join) {
            $query = $query->with($join);
        }
        $query = $this->setFilters($query, $filters);
        $query = $this->orderQuery($query, $order_by);

        return $query->paginate($perPage ? $perPage : $this->paginate, $columns, $pageName, $page);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($this->setAttributes($data));
    }

    public function setAttributes(array $data)
    {
        $data = array_map(function ($value) {
            return is_array($value) || is_object($value) ? $this->setAttributes($value) : trim($value);
        }, $data);

        $data = array_filter($data, function ($value) {
            return ($value !== null && $value !== '');
        });

        return $data;
    }

    public function orderQuery($query, $order_by)
    {
        if ($order_by != null) {
            foreach ($order_by as $column => $dir) {
                $query = $query->orderBy($column, ($dir ? $dir : 'ASC'));
            }
        }
        return $query;
    }
    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($this->setAttributes($data));
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrNew(array $data)
    {
        return $this->model->firstOrNew($this->setAttributes($data));
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $item = $this->model->find($id);
        if ($item) {
            $item->update($this->setAttributes($data));
            return $item;
        }

        return false;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $data, array $extra)
    {
        return $this->model->updateOrCreate($this->setAttributes($data), $this->setAttributes($extra));
    }

    /**
     * @param $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy(
        $attribute,
        $value,
        $columns = ['*'],
        $order_by = null,
        $limit = null,
        $offset = null,
        $with = []
    ) {
        $query = $this->model;

        if (!empty($with) && count($with) > 0) {
            foreach ($with as $join) {
                $query = $query->with($join);
            }
        }

        if ($limit != null) {
            $query = $query->take($limit);
        }

        if ($offset != null) {
            $query = $query->skip($offset);
        }

        $query = $this->orderQuery($query, $order_by);
        return $query->where($attribute, $value)->get($columns);
    }
}