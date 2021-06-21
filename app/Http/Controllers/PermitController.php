<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Company;
use App\Models\Permit;
use App\Models\Person;


class PermitController extends Controller {
  public $data = [];

  public function index() {
    $permits = DB::table('permits')
      ->join('companies', 'permits.companies_id', '=', 'companies.id')
      ->join('people', 'permits.people_id', '=', 'people.id')
      ->select(
        'permits.number',
        'people.surname',
        'people.forename',
        'people.patronymic',
        'companies.name',
        'people.position',

        'permits.start',
        'permits.end'
      )
      ->get();

      return $permits;
      // return Permit::all();
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
