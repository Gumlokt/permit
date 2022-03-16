<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Log::debug('text');


use App\Models\Company;
use App\Models\Permit;
use App\Models\Person;


class PersonController extends Controller {
  //
  public function autocomplete(Request $request) {
    $field = $request->input('field');
    $term = $request->input('term');
    
    // Log::debug($field);

    switch ($field) {
      case 'company': $table = 'companies'; $field = 'name'; break;
      default: $table = 'people'; break;
    }

    $selection = DB::table($table)
    ->select($field)
    ->where($field, '!=', '')
    ->where(function ($query) use ($field, $term) {
        $query->where($field, 'LIKE', '%' . $term . '%');
      })
    ->distinct()
    ->orderBy($field, 'ASC')
    ->get();

    $result = [];
    foreach ($selection as $entry) {
      $result[] = $entry->$field;
    }


    return $result;
  }



}
