<?php

namespace faiverson\gateways\contracts;

/**
 * RepositoryInterface provides the standard functions
 * to be expected of ANY repository.
 */
interface RepositoryInterface
{
    public function model();

    public function all(
        $columns = ['*'],
        $limit = null,
        $offset = null,
        $order_by = null,
        $filters = [],
        $with = []
    );

    public function paginate(
        $order_by = null,
        $perPage = null,
        $columns = ['*'],
        $filters = [],
        $with = [],
        $pageName = 'page',
        $page = null
    );

    public function find($id, $columns = ['*']);

    public function findOrFail($id, $columns = ['*']);

    public function findBy(
        $field,
        $value,
        $columns = ['*'],
        $limit = null,
        $offset = null,
        $order_by = null,
        $with = []
    );

    public function firstOrCreate(array $data);

    public function firstOrNew(array $data);

    public function create(array $attributes);

    public function update(array $attributes, $id);

    public function updateOrCreate(array $data, array $extra);

    public function destroy($ids);

    public function setAttributes(array $data);

    public function setFilters($query, Array $filters);
}
