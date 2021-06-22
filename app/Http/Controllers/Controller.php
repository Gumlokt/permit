<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected function clearSpaces(Array $allInputs) {
    $clearedInputs = [];

    foreach ($allInputs as $key => $value) {
      $clearedInputs[$key] = trim(preg_replace('/\s{2,}/', ' ', $value));
    }

    return $clearedInputs;
  }
}
