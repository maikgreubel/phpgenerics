<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics;

/**
 * This interface describes a resettable implementation
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface Resettable
{
  /**
   * Reset the stream pointer to beginning of stream.
   *
   * @throws Exception in case of reset has failed
   */
  public function reset();
}