<?php

namespace faiverson\gateways\adapters\fractal\abstracts;

use faiverson\gateways\exceptions\RepositoryException;
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

  protected $transformer;

  /**
   * @var object model
   */
  protected $model;


  protected $paginate = 10;

  public function __construct(Fractable $fractal)
  {
    $this->model = app()->make($this->model());
    if (!$this->model instanceof Model) {
      throw new RepositoryException(
        "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
      );
    }
    $this->fractal = $fractal;
    $this->meta = config('repositories.meta') ? config('repositories.meta') : null;
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
    $columns = ['*'],
    $limit = null,
    $offset = null,
    $order_by = null,
    $filters = [],
    $with = [],
    $data = null
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
    return $this->transformResponse($data, $resource);
  }

  /**
   * @param null $data
   * @param int $perPage
   * @param array $columns
   * @return mixed
   * @throws RepositoryException
   */
  public function paginate(
    $order_by = null,
    $perPage = null,
    $columns = ['*'],
    $filters = [],
    $with = [],
    $pageName = 'page',
    $page = null,
    $data = null
  ) {
    $query = $this->model;
    foreach ($with as $join) {
      $query = $query->with($join);
    }
    $query = $this->setFilters($query, $filters);

    if ($order_by != null) {
      foreach ($order_by as $column => $dir) {
        $query = $query->orderBy($column, $dir);
      }
    }

    $resource = $query->paginate($perPage ? $perPage : $this->paginate, $columns, $pageName, $page);
    $this->transform = $this->getTransformer();
    $this->setIncludes($data);
    $this->fractal->setMeta($this->meta);
    return $this->fractal->paginatedCollection($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
  }

  /**
   * @param array $data
   * @return mixed
   */
  public function create(array $fields, $data = null)
  {
    $item = $this->model->create($this->setAttributes($fields));
    return $this->transformResponse($data, $item);
  }

  /**
   * @param array $data
   * @return mixed
   */
  public function firstOrCreate(array $fields, array $args = [], $data = null)
  {
    $item = $this->model->firstOrCreate($this->setAttributes($fields), $args);
    return $this->transformResponse($data, $item);
  }

  /**
   * @param array $data
   * @return mixed
   */
  public function firstOrNew(array $fields, array $args = [], $data = null)
  {
    $item = $this->model->firstOrCreate($this->setAttributes($fields), $args);
    return $this->transformResponse($data, $item);
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
      return $this->transformResponse($data, $item);
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
    return $this->transformResponse($data, $item);
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
  public function find($id, $columns = ['*'], $data = null)
  {
    $item = $this->model->find($id, $columns);
    return $this->transformResponse($data, $item);
  }

  /**
   * @param $id
   * @param array $columns
   * @return mixed
   */
  public function findOrFail($id, $columns = ['*'], $data = null)
  {
    $item = $this->model->findOrFail($id, $columns);
    return $this->transformResponse($data, $item);
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
    return $this->transformResponse($data, $resource);
  }

  public function transformResponse($data, $resource)
  {
    $this->transform = $this->getTransformer();
    $this->setIncludes($data);
    $this->fractal->setMeta($this->meta);
    if ($resource instanceof Collection) {
      return $this->collection($resource);
    }
    else if ($resource instanceof Model) {
      return $this->item($resource);
    }
    elseif(is_array($resource)) {
      return $this->item($resource);
    }
    return $this->primitive($resource);
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

  protected function getTransformer()
  {
    $transformer = app()->make($this->transformer());
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
   */
  private function item($resource)
  {
    return $this->fractal->item($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
  }

  /**
   * @param array $data
   * @param $id
   * @return mixed
   */
  private function collection($resource)
  {
    return $this->fractal->collection($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
  }

  /**
   * @TODO this is not implemented in FRACTAL class yet
   * @param array $data
   * @param $resource
   * @return mixed
   */
  private function primitive($resource)
  {
    return $this->fractal->primitive($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
  }
}
