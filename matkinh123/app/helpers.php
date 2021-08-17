<?php

use Illuminate\Contracts\View\Factory as ViewFactory;

if (! function_exists('admin_route')) {
  /**
   * Generate the URL to a named admin_route.
   *
   * @param  array|string  $name
   * @param  mixed  $parameters
   * @param  bool  $absolute
   * @return string
   */
  function admin_route($name, $parameters = [], $absolute = true)
  {
      $admin_name = 'admin.' . $name;
      return app('url')->route($admin_name, $parameters, $absolute);
  }
}

if (! function_exists('admin_view')) {
  /**
   * Get the evaluated view contents for the given view.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
   */
  function admin_view($view = null, $data = [], $mergeData = [])
  {
      $factory = app(ViewFactory::class);

      if (func_num_args() === 0) {
          return $factory;
      }

      $admin_view = env('VIEW_CORE'). '::' . 'admin.' . env('ADMIN_THEME') . '.pages.' . $view;

      return $factory->make($admin_view, $data, $mergeData);
  }
}

if (! function_exists('admin_theme')) {
  function admin_theme() {
    return env('VIEW_CORE'). '::' . 'admin.' . env('ADMIN_THEME') . '.';
  }
}

if (!function_exists('getStartAndEndDateWeek')) {
  /**
   * Get Date Of Week
   *
   * @return array
   */
  function getStartAndEndDateWeek() {
      $dateOfStartWeek = date("Y-m-d", strtotime('monday this week'));
      // $dateOfEndWeek = date("Y-m-d", strtotime('monday next week'));
      $dateOfEndWeek = date("Y-m-d", strtotime('sunday this week'));
      return [$dateOfStartWeek, $dateOfEndWeek];
  }
}

if (!function_exists('getStartAndEndDateMonth'))     {
  /**
   * Get Date of Month
   *
   * @return array
   */
  function getStartAndEndDateMonth() {
      // Date
      $dateOfStartMonth = date("Y-m-d", strtotime('first day of this month'));
      // $dateOfEndMonth   =  date("Y-m-d", strtotime('first day of next month'));
      $dateOfEndMonth = date("Y-m-d");

      return [$dateOfStartMonth, $dateOfEndMonth];
  }
}

if (!function_exists('getStartAndEndDateYear'))     {
  /**
   * Get Date Of Year
   *
   * @return array
   */
  function getStartAndEndDateYear() {
      $dateOfStartYear = date('Y-m-d', strtotime('first day of January this year'));
      // $dateOfEndYear   = date('Y-m-d', strtotime('first day of January next year'));
      $dateOfEndYear = date("Y-m-d");
      return [$dateOfStartYear, $dateOfEndYear];
  }
}