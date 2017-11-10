<?php

namespace faiverson\gateways\adapters\fractal\serializer;

use InvalidArgumentException;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\JsonApiSerializer;

class ApiSerializer extends JsonApiSerializer
{
  /**
   * Serialize an item.
   *
   * @param string $resourceKey
   * @param array $data
   *
   * @return array
   */
  public function item($resourceKey, array $data)
  {
    if($this->isResourceObject($resourceKey)) {
      $resource = parent::item($resourceKey, $data);
    }
    else {
      $resource = ['data' => $data];
    }

    return $resource;
  }

  /**
   * Get the mandatory fields for the serializer
   *
   * @return array
   */
  public function isResourceObject($resourceKey)
  {
    return $resourceKey != null ? true : false;
  }
}
