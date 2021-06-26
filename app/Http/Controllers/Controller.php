<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


  protected function handleDateStart(String $dateStart) {
    return date_format(date_create_from_format('d.m.Y H:i:s', $dateStart . ' 00:00:00'), 'Y-m-d H:i:s');
  }


  protected function handleDateEnd(String $dateEnd) {
    return date_format(date_create_from_format('d.m.Y H:i:s', $dateEnd . ' 23:59:59'), 'Y-m-d H:i:s');
  }


  protected function clearSpaces(Array $allInputs) {
    $clearedInputs = [];

    foreach ($allInputs as $key => $value) {
      $clearedInputs[$key] = trim(preg_replace('/\s{2,}/', ' ', $value));
    }

    return $clearedInputs;
  }
}
