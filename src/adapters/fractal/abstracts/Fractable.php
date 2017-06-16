<?php

namespace faiverson\gateways\adapters\fractal\abstracts;

use Illuminate\Pagination\AbstractPaginator;
use League\Fractal\TransformerAbstract;

interface Fractable
{
    /**
     * @param $includes
     * @internal param $connection
     * @return mixed
     */
    public function parseIncludes($includes);
    /**
     * @param mixed $data
     * @param \League\Fractal\TransformerAbstract|callable $transformer
     * @param string $resourceKey
     * @return array
     */
    public function item($data, TransformerAbstract $transformer = null, $resourceKey = null);
    /**
     * @param $data
     * @param \League\Fractal\TransformerAbstract|callable $transformer
     * @param string $resourceKey
     * @return array
     */
    public function collection($data, TransformerAbstract $transformer = null, $resourceKey = null);
    /**
     * @param AbstractPaginator $paginator
     * @param \League\Fractal\TransformerAbstract|callable $transformer
     * @param string $resourceKey
     * @return mixed
     */
    public function paginatedCollection(AbstractPaginator $paginator, TransformerAbstract $transformer = null, $resourceKey = null);
}