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
     * Remove all data is not a column in DB
     * @param array $data
     * @return array
     */
    public function validFields(array $data)
    {
        $allow = $this->model->getFillable();
        $data = array_filter($data, function ($key) use($allow) {
            return in_array($key, $allow);
        }, ARRAY_FILTER_USE_KEY);

        return $data;
    }

    public function setFilters($query, Array $filters)
    {
        return $query;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all(
        $filters = [],
        $order_by = [],
        $columns = ['*'],
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

    public function paginate(
        $page = null,
        $pageName = 'page',
        $perPage = null,
        $columns = ['*'],
        $order_by = [],
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
        return $this->model->create($this->setAttributes($this->validFields($data)));
    }

    public function setAttributes(array $data)
    {
        $data = array_map(function ($value) {
            return is_array($value) ? $this->setAttributes($value) : (is_object($value) || is_bool($value) ? $value : trim($value));
        }, $data);

        $data = array_filter($data, function ($value) {
            return ($value !== null && $value !== '');
        });

        return $data;
    }

    public function orderQuery($query, array $order_by)
    {
        if (count($order_by) > 0) {
            foreach ($order_by as $column => $dir) {
                if(array_key_exists('field', $dir)) {
                    $query = $query->orderBy($dir['field'], (isset($dir['direction']) ? $dir['direction'] : 'ASC'));
                }
                else {
                    $query = $query->orderBy($column, ($dir ? $dir : 'ASC'));
                }
            }
        }
        return $query;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $data, array $extra = [])
    {
        return $this->model->firstOrCreate($this->setAttributes($this->validFields($data)),
            $this->setAttributes($this->validFields($extra)));
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrNew(array $data, array $extra = [])
    {
        return $this->model->firstOrNew($this->setAttributes($this->validFields($data)),
            $this->setAttributes($this->validFields($extra)));
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
            $item->update($this->setAttributes($this->validFields($data)));
            return $item;
        }

        return false;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $data, array $extra = [])
    {
        return $this->model->updateOrCreate($this->setAttributes($this->validFields($data)), $this->setAttributes($this->validFields($extra)));
    }

    /**
     * @param $id
     * @return the object deleted
     */
    public function delete($id)
    {
        $item = $this->model->find($id);
        if ($item) {
            $item->delete();
            return $item;
        }

        return false;
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
        $limit = null,
        $offset = null,
        $order_by = null,
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

        if ($order_by) {
            $query = $this->orderQuery($query, $order_by);
        }
        return $query->where($attribute, $value)->get($columns);
    }
}
