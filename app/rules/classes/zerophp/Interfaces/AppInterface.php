<?php

namespace ZeroPhp\Interfaces;

interface AppInterface
{
  /**
   * Register GET Route
   *
   * @param string $routeName
   * @param string $function
   * @return void
   */
  public function get($route,  $function): void;

  /**
   * Register POST Route
   *
   * @param string $routeName
   * @param string $function
   * @return void
   */
  public function post($route,  $function): void;

  /**
   * Register PUT Route
   *
   * @param string $routeName
   * @param string $function
   * @return void
   */
  public function put($route,  $function): void;

  /**
   * Register DELETE Route
   *
   * @param string $routeName
   * @param string $function
   * @return void
   */
  public function delete($route,  $function): void;
}
