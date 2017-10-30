<?php

namespace faiverson\gateways\contracts;

interface Validable
{

  /**
   * With
   *
   * @param array
   * @return self
   */
  public function with(array $input);

  /**
   * Passes
   *
   * @return boolean
   */
  public function passes();

  /**
   * Errors
   *
   * @return array
   */
  public function errors();
}
