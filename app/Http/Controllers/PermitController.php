<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Log::debug('text');


use App\Models\Company;
use App\Models\Permit;
use App\Models\Person;


class PermitController extends Controller {
  public $permitData = [];

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
        'people.position',

        'companies.name as company',

        'permits.start as dateStart',
        'permits.end as dateEnd'
      )
      ->orderByDesc('permits.id')
      ->get();

      return $permits;
      // return Permit::all();
  }


  public function last($year) {
    $lastNumber = Permit::where('start', '<=', $year . '-12-31 23:59:59')->max('number');

    if(!$lastNumber) {
      $lastNumber = 0;
    }

    return $lastNumber; // last permit number depending on selected year
  }


  public function show($id) {
    return Permit::find($id);
  }


  public function store(Request $request) {
    // if the validation fails, the Laravel will automatically send the status code 422
    // 422 UNPROCESSABLE ENTITY - HTTP Status code which means that the server understands the content type of the request entity but was unable to process the contained instructions
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

    $clearedInputs = $this->clearSpaces($request->all()); // array of cleared user inputs

    $clearedInputs['dateStart'] = $this->handleDateStart($clearedInputs['dateStart']); // now dates are converted from 'd.m.Y' format to 'Y-m-d H:i:s' and can be safely compared
    $clearedInputs['dateEnd'] = $this->handleDateEnd($clearedInputs['dateEnd']);

    // // dateStart can not be older then dateEnd
    // if($clearedInputs['dateStart'] > $clearedInputs['dateEnd']) {
    //   return response()->json([
    //     'error' => true,
    //     'problem' => 'Дата начала действия вновь вводимого пропуска позднее даты окончания действия.',
    //     'solution' => 'Исправьте срок действия вновь вводимого пропуска.',
    //   ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    // }
    $res = $this->checkErrorsOfPermitDates($clearedInputs);

    if ($res) {
      return response()->json($res['answer'], $res['statusCode']);
    }

    // $currentDate = date('Y-m-d') . ' 00:00:00';
    // // dateEnd can not be older then currentDate
    // if($currentDate > $clearedInputs['dateEnd']) {
    //   return response()->json([
    //     'error' => true,
    //     'problem' => 'Запрещён ввод заведомо истёкших пропусков.',
    //     'solution' => 'Исправьте дату окончания действия вновь вводимого пропуска на текущую либо более позднюю.',
    //   ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    // }

    // Get permits with the same data, except for the permit number and dates, if they are exist

    // $duplicatePermit = $this->checkDuplication($clearedInputs);
    // if($duplicatePermit) {
    //   return response()->json([
    //     'error' => true,
    //     'problem' => 'В базе данных уже зарегистрирован пропуск № ' . $duplicatePermit->number . ' с такими же введёнными значениями, у которого дата окончания дейстия позже даты начала действия вновь вводимого пропуска.',
    //     'solution' => 'Для добавления в базу вновь вводимого пропуска необходимо перевести ранее зарегистрированный и до сих пор действующий пропуск № ' . $duplicatePermit->number . ' в разряд истёкших.',
    //   ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    // }

    $this->permitData['company'] = Company::store($clearedInputs);
    $this->permitData['person'] = Person::store($clearedInputs);

    return Permit::store($clearedInputs, $this->permitData);
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

    $clearedInputs = $this->clearSpaces($request->all()); // array of cleared user inputs

    $clearedInputs['dateStart'] = $this->handleDateStart($clearedInputs['dateStart']); // now dates are converted from 'd.m.Y' format to 'Y-m-d H:i:s' and can be safely compared
    $clearedInputs['dateEnd'] = $this->handleDateEnd($clearedInputs['dateEnd']);

    // // dateStart can not be older then dateEnd
    // if($clearedInputs['dateStart'] > $clearedInputs['dateEnd']) {
    //   return response()->json([
    //     'error' => true,
    //     'problem' => 'Дата начала действия вновь вводимого пропуска позднее даты окончания действия.',
    //     'solution' => 'Исправьте срок действия вновь вводимого пропуска.',
    //   ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    // }

    $res = $this->checkErrorsOfPermitDates($clearedInputs, $id);

    if ($res) {
      return response()->json($res['answer'], $res['statusCode']);
    }

    // $currentDate = date('Y-m-d') . ' 00:00:00';
    // // dateEnd can not be older then currentDate
    // if($currentDate > $clearedInputs['dateEnd']) {
    //   return response()->json([
    //     'error' => true,
    //     'problem' => 'Запрещён ввод заведомо истёкших пропусков.',
    //     'solution' => 'Исправьте дату окончания действия вновь вводимого пропуска на текущую либо более позднюю.',
    //   ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    // }


    // // Get permits with the same data, except for the permit number and dates, if they are exist
    // $duplicatePermit = $this->checkDuplication($clearedInputs, $id);
    // if($duplicatePermit) {
    //   return response()->json([
    //     'error' => true,
    //     'problem' => 'В базе данных уже зарегистрирован пропуск № ' . $duplicatePermit->number . ' с такими же введёнными значениями, у которого дата окончания дейстия позже даты начала действия вновь вводимого пропуска.',
    //     'solution' => 'Для добавления в базу вновь вводимого пропуска необходимо перевести ранее зарегистрированный и до сих пор действующий пропуск № ' . $duplicatePermit->number . ' в разряд истёкших.',
    //   ], 409); // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
    // }

    $permit = Permit::find($id);

    $sql = [
      'people_id' => $permit->people_id,
      'companies_id' => $permit->companies_id,
      'number' => $clearedInputs['number'],
      'start' => $clearedInputs['dateStart'],
      'end' => $clearedInputs['dateEnd'],
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


  public function expire(Request $request, $id) {
    $permit = Permit::findOrFail($id);
    $yesterday = date('Y-m-d', time() - 86400) . ' 23:59:59'; // 86 400 sec = 60 sec * 60 min * 24 hour
    $permit->update(['end' => $yesterday]);

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




  // Delete unused companies and persons from DB if they are exist
  private function checkErrorsOfPermitDates($clearedInputs, $id = 0) { // second argument is needed only on update operation
    // dateStart can not be older then dateEnd
    if($clearedInputs['dateStart'] > $clearedInputs['dateEnd']) {
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'Задана дата начала действия вновь вводимого пропуска позднее даты окончания действия.',
          'solution' => 'Задайте корретный срок действия вновь вводимого пропуска.',
        ]
      ];
    }

    // dateEnd can not be older then currentDate
    $currentDate = date('Y-m-d') . ' 00:00:00';
    if($currentDate > $clearedInputs['dateEnd']) {
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'Запрещён ввод заведомо истёкших пропусков.',
          'solution' => 'Исправьте дату окончания действия вновь вводимого пропуска на текущую либо более позднюю.',
        ]
      ];
    }

    // the validity period of the permits expires no later than December 31 of the year of issue and cannot be extended for the next year
    if(date_format(date_create_from_format('Y-m-d H:i:s', $clearedInputs['dateStart']), 'Y') != date_format(date_create_from_format('Y-m-d H:i:s', $clearedInputs['dateEnd']), 'Y')) {
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'Запрещён ввод пропусков, срок действия которых начинается в одном году, а заканчивается следующем.',
          'solution' => 'Исправьте срок действия вновь вводимого пропуска так, чтобы год начала действия и год окончания действия были одинаковыми.',
        ]
      ];
    }

    // you can't "jump" in a year or more - the validity period of the permits must be in the current or next year
    if((date_format(date_create_from_format('Y-m-d H:i:s', $clearedInputs['dateStart']), 'Y') - date('Y')) > 1 ) {
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'Запрещён ввод пропусков далёкого будущего, т.е. пропусков, у которых срок действия выходит за рамки текущего либо следующего годов.',
          'solution' => 'Исправьте год действия вновь вводимого пропуска на текущий либо следующий.',
        ]
      ];
    }

    // max count of not expired permits must be 2 or less
    $validPermitsCount = DB::table('permits')
      ->join('companies', 'permits.companies_id', '=', 'companies.id')
      ->join('people', 'permits.people_id', '=', 'people.id')
      ->where('people.surname', '=', $clearedInputs['surname'])
      ->where('people.forename', '=', $clearedInputs['forename'])
      ->where('people.patronymic', '=', $clearedInputs['patronymic'])
      ->where('people.position', '=', $clearedInputs['position'])
      ->where('companies.name', '=', $clearedInputs['company'])
      ->where('permits.end', '>=', $currentDate) // select valid (not expired) permits
      ->where(function($query) use ($id) {
        if ($id) {
          return $query->where('permits.id', '!=', $id);
        }
      })
      ->orderByDesc('permits.id')
      ->count();

    if($validPermitsCount > 1) {
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'В базе данных уже зарегистрированы два пропуска с такими же введёнными значениями и лишь разными, но ещё не истёкшими сроками действия.',
          'solution' => 'Проверьте правильность ввода данных, вероятно, вы собирались оформить пропуск на другое лицо или организацию.',
        ]
      ];
    }

    // relevant only for update() action
    // relevant only for updating an existing permit, as long as there is another registered permit with the same data and validity period in the future
    if ($id) {
      $editingPermit = Permit::find($id);

      // find out if there is a permit with the same input data and validity period in the future
      $duplicatePermit = DB::table('permits')
        ->join('companies', 'permits.companies_id', '=', 'companies.id')
        ->join('people', 'permits.people_id', '=', 'people.id')
        ->select(
          'permits.id',
          'permits.number',

          'people.surname',
          'people.forename',
          'people.patronymic',
          'people.position',

          'companies.name as company',

          'permits.start as dateStart',
          'permits.end as dateEnd'
        )
        ->where('people.surname', '=', $clearedInputs['surname'])
        ->where('people.forename', '=', $clearedInputs['forename'])
        ->where('people.patronymic', '=', $clearedInputs['patronymic'])
        ->where('people.position', '=', $clearedInputs['position'])
        ->where('companies.name', '=', $clearedInputs['company'])
        ->where('permits.start', '>', $editingPermit->end) // select permit with the validity period in the future
        ->where(function($query) use ($id) {
          if ($id) {
            return $query->where('permits.id', '!=', $id);
          }
        })
        ->orderByDesc('permits.id')
        ->first();

      if($duplicatePermit) {
        if($clearedInputs['dateEnd'] > $duplicatePermit->dateStart) {
          return [
            'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
            'answer' => [
              'error' => true,
              'problem' => 'Произошло пересечение сроков действия редактируемого пропуска с пропуском № ' . $duplicatePermit->number . ', содержащего такие же значения, но с другим сроком дейстия, который начнётся с ' . date_format(date_create_from_format('Y-m-d H:i:s', $duplicatePermit->dateStart), 'd.m.Y') . '.',
              'solution' => 'Для успешного редактирования выбранного пропуска введите дату окончания действия, которая не попадала бы в срок действия ранее зарегистрированного пропуска, у которого срок дейстия наступит в будущем, т.е. дата должна быть ранее, чем ' . date_format(date_create_from_format('Y-m-d H:i:s', $duplicatePermit->dateStart), 'd.m.Y') . '.',
            ]
          ];
        }
      }

      return false;
    }

    // find out if there is a permit with the same input data and not expired validity period
    $duplicatePermit = DB::table('permits')
      ->join('companies', 'permits.companies_id', '=', 'companies.id')
      ->join('people', 'permits.people_id', '=', 'people.id')
      ->where('people.surname', '=', $clearedInputs['surname'])
      ->where('people.forename', '=', $clearedInputs['forename'])
      ->where('people.patronymic', '=', $clearedInputs['patronymic'])
      ->where('people.position', '=', $clearedInputs['position'])
      ->where('companies.name', '=', $clearedInputs['company'])
      ->where('permits.end', '>=', $clearedInputs['dateStart']) // select valid (not expired) permits
      ->where(function($query) use ($id) {
        if ($id) {
          return $query->where('permits.id', '!=', $id);
        }
      })
      ->orderByDesc('permits.id')
      ->first();

    if($duplicatePermit) {
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'В базе данных уже зарегистрирован пропуск № ' . $duplicatePermit->number . ' с такими же введёнными значениями, у которого дата окончания дейстия позже даты начала действия вновь вводимого пропуска.',
          'solution' => 'Для добавления в базу вновь вводимого пропуска не изменяя его срока действия, необходимо перевести ранее зарегистрированный и до сих пор действующий пропуск № ' . $duplicatePermit->number . ' в разряд истёкших. Либо задайте другой срок действия вновь вводимого пропуска.',
        ]
      ];
    }

    // relevant only for store() action
    // find out if there is a permit with the same input data and has a start date later than the current date
    $duplicatePermit = DB::table('permits')
      ->join('companies', 'permits.companies_id', '=', 'companies.id')
      ->join('people', 'permits.people_id', '=', 'people.id')
      ->where('people.surname', '=', $clearedInputs['surname'])
      ->where('people.forename', '=', $clearedInputs['forename'])
      ->where('people.patronymic', '=', $clearedInputs['patronymic'])
      ->where('people.position', '=', $clearedInputs['position'])
      ->where('companies.name', '=', $clearedInputs['company'])
      ->where('permits.start', '>', $currentDate) // select not started permit
      ->where(function($query) use ($id) {
        if ($id) {
          return $query->where('permits.id', '!=', $id);
        }
      })
      ->orderByDesc('permits.id')
      ->first();

    if($duplicatePermit) {
      Log::debug($duplicatePermit->number);
      return [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'В базе данных уже зарегистрирован пропуск № ' . $duplicatePermit->number . ' на указанное лицо и организацию, у которого дата начала дейстия ещё не наступила.',
          'solution' => 'Вместо создания ещё одного пропуска на будущий период, отредактируйте срок действия зарегистрированного ранее и ещё не вступившего в действие пропуска № ' . $duplicatePermit->number . ', задав ему нужный вам период.',
        ]
      ];
    }


    return false;
  }


  // // select not expired permits with the same imputs
  // public function checkDuplication($clearedInputs, $id = 0) { // second argument is needed only on update operation
  //   $duplicatePermit = DB::table('permits')
  //     ->join('companies', 'permits.companies_id', '=', 'companies.id')
  //     ->join('people', 'permits.people_id', '=', 'people.id')
  //     ->select(
  //       'permits.id',
  //       'permits.number',

  //       'people.surname',
  //       'people.forename',
  //       'people.patronymic',
  //       'people.position',

  //       'companies.name as company',

  //       'permits.start as dateStart',
  //       'permits.end as dateEnd'
  //     )
  //     ->where('people.surname', '=', $clearedInputs['surname'])
  //     ->where('people.forename', '=', $clearedInputs['forename'])
  //     ->where('people.patronymic', '=', $clearedInputs['patronymic'])
  //     ->where('people.position', '=', $clearedInputs['position'])
  //     ->where('companies.name', '=', $clearedInputs['company'])
  //     ->where('permits.end', '>=', $clearedInputs['dateStart']) // select valid (not expired) permits
  //     ->where(function($query) use ($id) {
  //       if ($id) {
  //         return $query->where('permits.id', '!=', $id);
  //       }
  //     })
  //     ->orderByDesc('permits.id')
  //     ->first();

  //   return $duplicatePermit;
  // }
}
