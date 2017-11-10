<?php

namespace faiverson\gateways\adapters\fractal\abstracts;

use faiverson\gateways\abstracts\Repository as BaseRepository;
use faiverson\gateways\contracts\RepositoryInterface;
use faiverson\gateways\exceptions\RepositoryException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use League\Fractal\TransformerAbstract;

/**
 * Class Fractal Repository
 *
 */
abstract class Repository extends BaseRepository implements RepositoryInterface
{
    protected $fractal;

    protected $transformer;

    public function __construct(Application $app, Fractable $fractal)
    {
        parent::__construct($app);
        $transformer = $app->make($this->transformer());
        if (!$transformer instanceof TransformerAbstract) {
            $txt = "Class {$this->transformer()} must be an instance of League\\Fractal\\TransformerAbstract";
            throw new RepositoryException($txt);
        }
        $this->meta = isset($app['config']['repositories']['meta']) ? $app['config']['repositories']['meta'] : null;
        $this->transform = $transformer;
        $this->fractal = $fractal;
    }

    /**
     * Specify Transformer class name
     *
     * @return string class name
     */
    abstract public function transformer();

    /**
     * @param $resource
     * @param array $data
     *
     * We look into the dependency repository to get the proper response
     * using the transformer set by the repository
     * @return bool
     */
    public function response($resource, $data = [], $transformer = null)
    {
        if ($transformer) {
            $this->setTransformer($transformer, $data);
        }

        return $this->transformResponse($this->setAttributes($data), $resource);
    }

    public function setTransformer($transformer, $data = null)
    {
        $transformer = new $transformer($data);
        if (!$transformer instanceof TransformerAbstract) {
            $txt = "Class {$transformer} must be an instance of League\\Fractal\\TransformerAbstract";
            throw new RepositoryException($txt);
        }

        $this->transform = $transformer;
    }

    public function transformResponse($data, $resource)
    {
        $this->setIncludes($data);
        $this->fractal->setMeta($this->meta);
        if ($resource instanceof Paginator) {
            return $this->paginateCollection($resource);
        } elseif ($resource instanceof Collection) {
            return $this->collection($resource);
        } else {
            if ($resource instanceof Model) {
                return $this->item($resource);
            } elseif (is_array($resource)) {
                return $this->basic($resource);
            }
        }
        return $this->primitive($resource);
    }

    public function setIncludes($data)
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
    private function collection($resource)
    {
        return $this->fractal->collection($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
    }

    private function paginateCollection($resource)
    {
        return $this->fractal->paginatedCollection($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
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

    private function basic($resource)
    {
        return $this->fractal->item($resource);
    }

    /**
     * @param array $data
     * @param $resource
     * @return mixed
     */
    private function primitive($resource)
    {
        return $this->fractal->primitive($resource, $this->transform, ($this->transform)::JSON_OBJ_TYPE);
    }
}