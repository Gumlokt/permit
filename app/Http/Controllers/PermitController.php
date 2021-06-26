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
    $request->validate([
      'number' => 'required|numeric',
      'surname' => 'required',
      'forename' => 'required',
      'patronymic' => 'required',
      'company' => 'required',
      'position' => 'required',
      'dateStart' => 'required|date_format:d.m.Y',
      'dateEnd' => 'required|date_format:d.m.Y',
    ]);

    // dateStart can not be older then dateEnd
    if($this->handleDateStart($request->input('dateStart')) > $this->handleDateEnd($request->input('dateEnd'))) {
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
    $request->validate([
      'number' => 'required|numeric',
      'surname' => 'required',
      'forename' => 'required',
      'patronymic' => 'required',
      'position' => 'required',
      'company' => 'required',
      'dateStart' => 'required|date_format:d.m.Y',
      'dateEnd' => 'required|date_format:d.m.Y',
    ]);

    if($this->handleDateStart($request->input('dateStart')) > $this->handleDateEnd($request->input('dateEnd'))) {
      return response()->json([
        'error' => true,
        'message' => 'Дата начала действия пропуска не может быть позднее даты окончания действия пропуска.',
      ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    }

    $clearedInputs = $this->clearSpaces($request->all()); // array of cleared user inputs

    $permit = Permit::find($id);

    $sql = [
      'people_id' => $permit->people_id,
      'companies_id' => $permit->companies_id,
      'number' => $clearedInputs['number'],
      'start' => $this->handleDateStart($clearedInputs['dateStart']),
      'end' => $this->handleDateEnd($clearedInputs['dateEnd']),
    ];

    // разберемся с компанией
    $company = Company::find($permit->companies_id); // company, that already stored in DB
    if ($company->name != $clearedInputs['company']) {
      $newCompany = Company::where('name', '=', $clearedInputs['company']) // company, that user is typed (in update permit form)
        ->first();

      $countOtherPermits = Permit::where('id', '!=', $id)
        ->where('companies_id', '=', $permit->companies_id)
        ->count();

      if ($newCompany) {
        $sql['companies_id'] = $newCompany->id;

        if (!$countOtherPermits) {
          Company::destroy($permit->companies_id);
        }
      } else {
        if ($countOtherPermits) {
          $newCompany = Company::store($clearedInputs);
          $sql['companies_id'] = $newCompany->id;
        } else {
          $company->update([ 'name' => $clearedInputs['company'] ]);
        }
      }
    }

    // разберемся с работниками
    $person = Person::find($permit->people_id); // person, that already stored in DB
    if ($person->surname != $clearedInputs['surname'] or $person->forename != $clearedInputs['forename'] or $person->patronymic != $clearedInputs['patronymic'] or $person->position != $clearedInputs['position']) {
      $newPerson = Person::where('surname', '=', $clearedInputs['surname']) // person data, that user is typed (in update permit form)
        ->where('forename', '=', $clearedInputs['forename'])
        ->where('patronymic', '=', $clearedInputs['patronymic'])
        ->where('position', '=', $clearedInputs['position'])
        ->first();

        $countOtherPermits = Permit::where('id', '!=', $id)
          ->where('people_id', '=', $permit->people_id)
          ->count();

      if ($newPerson) {
        $sql['people_id'] = $newPerson->id;

        if (!$countOtherPermits) {
          Person::destroy($permit->people_id);
        }
      } else {
        if ($countOtherPermits) {
          $newPerson = Person::store($clearedInputs);
          $sql['people_id'] = $newPerson->id;
        } else {
          $person->update([
            'surname' => $clearedInputs['surname'],
            'forename' => $clearedInputs['forename'],
            'patronymic' => $clearedInputs['patronymic'],
            'position' => $clearedInputs['position'],
          ]);
        }
      }
    }


    $permit->update($sql);

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
