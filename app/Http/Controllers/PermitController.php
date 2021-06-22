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
        'companies.name as company',
        'people.position',

        'permits.start as dateStart',
        'permits.end as dateEnd'
      )
      ->orderByDesc('permits.id')
      ->get();

      return $permits;
      // return Permit::all();
  }

  public function last() {
    return Permit::max('number'); // last permit number
  }

  public function show($id) {
    return Permit::find($id);
  }

  public function store(Request $request) {
    $clearedInputs = $this->clearSpaces($request->all());

    $this->data['company'] = Company::store($clearedInputs);
    $this->data['person'] = Person::store($clearedInputs);

    return Permit::store($clearedInputs, $this->data);
  }

  public function update(Request $request, $id) {
    $permit = Permit::findOrFail($id);
    $permit->update($this->clearSpaces($request->all()));

    return $permit;
  }

  public function delete(Request $request, $id) {
    $permit = Permit::findOrFail($id);
    $permit->delete();

    return 204;
  }
}
