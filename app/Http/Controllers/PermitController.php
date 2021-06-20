<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Permit;
use App\Models\Person;


class PermitController extends Controller {
  public $data = [];

  public function index() {
    return Permit::all();
  }

  public function show($id) {
    return Permit::find($id);
  }

  public function store(Request $request) {
    $this->data['company'] = Company::store($request->all());
    $this->data['person'] = Person::store($request->all());

    return Permit::store($request->all(), $this->data);
  }

  public function update(Request $request, $id) {
    $permit = Permit::findOrFail($id);
    $permit->update($request->all());

    return $permit;
  }

  public function delete(Request $request, $id) {
    $permit = Permit::findOrFail($id);
    $permit->delete();

    return 204;
  }
}
