<?php

namespace faiverson\gateways\adapters\fractal\abstracts;

use faiverson\gateways\exceptions\RepositoryException;
use faiverson\gateways\adapters\fractal\Fractal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

/**
 * Class Repository
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var object fractal
     */
    protected $fractal;

    /**
     * @var object model
     */
    protected $model;

    /**
     * @var string transformer
     */
    protected $transformer;

    public function __construct(Fractal $fractal)
    {
        $this->model = app($this->model());
        if (!$this->model instanceof Model) {
            throw new RepositoryException(
                "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
        $this->fractal = $fractal;
        $this->transformer = $this->transformer();
    }

    /**
     * Specify Model class name
     *
     * @return model object
     */
    abstract public function model();

    /**
     * Specify Transformer class name
     *
     * @return string class name
     */
    abstract public function transformer();

    /**
     * @param null $data
     * @param array $columns
     * @param null $limit
     * @param null $offset
     * @param null $order_by
     * @param array $filters
     * @param array $with
     * @return mixed
     * @throws RepositoryException
     */
    public function all(
        $data = null,
        $columns = ['*'],
        $limit = null,
        $offset = null,
        $order_by = null,
        $filters = [],
        $with = []
    ) {
        $query = $this->model;
        foreach ($with as $join) {
            $query = $query->with($join);
        }
        $query = $this->setFilters($query, $filters);
        if ($limit != null) {
            $query = $query->take($limit);
        }
        if ($offset != null) {
            $query = $query->skip($offset);
        }

        if ($order_by != null) {
            foreach ($order_by as $column => $dir) {
                $query = $query->orderBy($column, $dir);
            }
        }

        $resource = $query->get($columns);
        return $this->collection($resource, $data);
    }

    /**
     * @param null $data
     * @param int $perPage
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function paginate($data = null, $perPage = 15, $columns = ['*'])
    {
        $resource = $this->model->paginate($perPage, $columns);
        $this->setIncludes($data);
        $transformer = $this->setTransformer($data);

        return $this->fractal->paginatedCollection($resource, $transformer, $transformer::JSON_OBJ_TYPE);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $item = $this->model->create($this->setAttributes($data));
        $this->item($item, $data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $data)
    {
        $item = $this->model->firstOrCreate($this->setAttributes($data));
        $this->item($item, $data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrNew(array $data)
    {
        $item = $this->model->firstOrNew($this->setAttributes($data));
        $this->item($item, $data);
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
        if($item) {
            $item->update($this->setAttributes($data));
            return $this->item($item, $data);
        }

        return false;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $data, array $extra)
    {
        $item = $this->model->updateOrCreate($this->setAttributes($data), $this->setAttributes($extra));
        return $this->item($item, $data);
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
     * @param null $data
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function find($id, $data = null, $columns = ['*'])
    {
        $item = $this->model->find($id, $columns);
        return $this->item($item, null);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @param null $limit
     * @param null $offset
     * @param null $order_by
     * @return mixed
     */
    public function findBy(
        $attribute,
        $value,
        $data = null,
        $columns = array('*'),
        $limit = null,
        $offset = null,
        $order_by = null
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

        if ($order_by != null) {
            foreach ($order_by as $column => $dir) {
                $query = $query->orderBy($column, $dir);
            }
        }
        $resource = $query->where($attribute, $value)->get($columns);
        return $this->collection($resource, $data);
    }

    public function transformResponse($data, $resource)
    {
        $this->setIncludes($data);
        if ($resource instanceof Collection) {
            return $this->collection($resource, $data);
        }
        else if ($resource instanceof Model) {
            return $this->item($resource, $data);
        }
        throw new RepositoryException('The resource is not a Collection or a Model');
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

    public function setFilters($query, Array $filters)
    {
        return $query;
    }

    protected function setTransformer($data)
    {
        $transformer = new $this->transformer($data);
        if (!$transformer instanceof TransformerAbstract) {
            $txt = "Class {$this->transformer()} must be an instance of League\\Fractal\\TransformerAbstract";
            throw new RepositoryException($txt);
        }

        return $transformer;
    }

    protected function setIncludes($data)
    {
        if (!empty($data['include'])) {
            $this->fractal->parseIncludes($data["include"]);
        }
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     * @throws RepositoryException
     * @internal param string $attribute
     */
    private function item($resource, $data)
    {
        $this->setIncludes($data);
        $transformer = $this->setTransformer($data);
        return $this->fractal->item($resource, $transformer, $transformer::JSON_OBJ_TYPE);
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     * @throws RepositoryException
     * @internal param string $attribute
     */
    private function collection($resource, $data)
    {
        $this->setIncludes($data);
        $transformer = $this->setTransformer($data);
        return $this->fractal->collection($resource, $transformer, $transformer::JSON_OBJ_TYPE);
    }
}
