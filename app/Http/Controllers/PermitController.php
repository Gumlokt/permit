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
        'permits.id',
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
    $validated = $request->validate([
      'number' => 'required|numeric',
      'surname' => 'required',
      'forename' => 'required',
      'patronymic' => 'required',
      'company' => 'required',
      'position' => 'required',
      'dateStart' => 'required|date_format:d.m.Y',
      'dateEnd' => 'required|date_format:d.m.Y',
    ]);

    // start date can not be older end date
    if(date_format(date_create_from_format('d.m.Y H:i:s', $request->input('dateStart') . ' 00:00:00'), 'Y-m-d H:i:s') > date_format(date_create_from_format('d.m.Y H:i:s', $request->input('dateEnd') . ' 00:00:00'), 'Y-m-d H:i:s')) {
      return response()->json([
        'error' => true,
        'message' => 'Дата начала действия пропуска не может быть позднее даты окончания действия пропуска.',
      ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    }

    $clearedInputs = $this->clearSpaces($request->all());

    $this->data['company'] = Company::store($clearedInputs);
    $this->data['person'] = Person::store($clearedInputs);

    return Permit::store($clearedInputs, $this->data);
  }


  public function update(Request $request, $id) {
    $validated = $request->validate([
      'number' => 'required|numeric',
      'surname' => 'required',
      'forename' => 'required',
      'patronymic' => 'required',
      'company' => 'required',
      'position' => 'required',
      'dateStart' => 'required|date_format:d.m.Y',
      'dateEnd' => 'required|date_format:d.m.Y',
    ]);

    if(date_format(date_create_from_format('d.m.Y H:i:s', $request->input('dateStart') . ' 00:00:00'), 'Y-m-d H:i:s') > date_format(date_create_from_format('d.m.Y H:i:s', $request->input('dateEnd') . ' 00:00:00'), 'Y-m-d H:i:s')) {
      return response()->json([
        'error' => true,
        'message' => 'Дата начала действия пропуска не может быть позднее даты окончания действия пропуска.',
      ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    }
    
    $clearedInputs = $this->clearSpaces($request->all());

    $permit = Permit::findOrFail($id);

    // // разберемся с компанией
    // $company = Company::find($permit->companies_id);
    // $newCompany = Company::where('name', '=', $clearedInputs->company)->first();

    // $this->data['company'] = Company::store($clearedInputs);

    // $permit->update($this->clearSpaces($request->all()));

    return $permit;
  }


  public function delete(Request $request, $id) {
    $permit = Permit::findOrFail($id);
    $permit->delete();

    $this->clearDb($permit);

    return 204;
  }


  // Delete unused companies and persons from DB if they are exist
  private function clearDb($deletedPermit) {
    $anotherPermit = Permit::where('companies_id', '=', $deletedPermit->companies_id)->first();
    if (!$anotherPermit) {
      Company::destroy($deletedPermit->companies_id);
    }

    $anotherPermit = Permit::where('people_id', '=', $deletedPermit->people_id)->first();
    if (!$anotherPermit) {
      Person::destroy($deletedPermit->companies_id);
    }
  }
}
