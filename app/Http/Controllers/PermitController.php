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
      ->take(16)
      ->get();

    return $permits;
  }


  // Return filtered permits
  public function filter(Request $request) {
    $mainPermitFields = $request->input('mainPermitFields');

    $filteredPermits = DB::table('permits')
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
      ->where(function($query) use ($mainPermitFields) {
        if (!empty($mainPermitFields['surname'])) {
          return $query->where('surname', '=', $mainPermitFields['surname']);
        }
      })
      ->where(function($query) use ($mainPermitFields) {
        if (!empty($mainPermitFields['forename'])) {
          return $query->where('forename', '=', $mainPermitFields['forename']);
        }
      })
      ->where(function($query) use ($mainPermitFields) {
        if (!empty($mainPermitFields['patronymic'])) {
          return $query->where('patronymic', '=', $mainPermitFields['patronymic']);
        }
      })
      ->where(function($query) use ($mainPermitFields) {
        if (!empty($mainPermitFields['position'])) {
          return $query->where('position', '=', $mainPermitFields['position']);
        }
      })
      ->where(function($query) use ($mainPermitFields) {
        if (!empty($mainPermitFields['company'])) {
          return $query->where('companies.name', '=', $mainPermitFields['company']);
        }
      })
      ->orderByDesc('permits.id')
      ->get();


      return $filteredPermits;
  }


  // Return last permit ID
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

    // dateStart can not be older then dateEnd
    $res = $this->checkErrorsOfPermitDates($clearedInputs);

    if ($res) {
      return response()->json($res['answer'], $res['statusCode']);
    }

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

    $res = $this->checkErrorsOfPermitDates($clearedInputs, $id);

    if ($res) {
      return response()->json($res['answer'], $res['statusCode']);
    }

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


  public function print(Request $request) {
    $ids = explode(",", $request->input('permits'));
    $permits = Permit::find($ids);

    $pages = [];
    $count = count($permits);
    for ($p = 0, $i = 0, $j = 0; $j < $count; $i++, $j++) {
      $pages[$p][$i] = $permits[$j];

      if (15 == $i) {
        $p++;
        $i = -1;
      }
    }

    return view('print', [ 'pages' => $pages, ]);
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



  // helper function for checking the correctness of dates
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

    // currentDate can not be older then dateEnd
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
          'problem' => 'Защита от создания дубликатов! В базе данных уже зарегистрированы 2 пропуска с такими же значениями и ещё не истёкшими сроками действия.',
          'solution' => 'Проверьте правильность ввода данных, вероятно, вы собирались оформить пропуск на другое лицо или организацию.',
        ]
      ];
    }

    // relevant both for store() and update() actions to check out if there are permit duplicates
    // find out if there is a permit with the same input data and has a start date later than the current date
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
      ->where('permits.start', '>', $currentDate) // select not started permit
      ->where(function($query) use ($id) {
        if ($id) {
          return $query->where('permits.id', '!=', $id);
        }
      })
      ->orderByDesc('permits.id')
      ->first();

    if($duplicatePermit) {
      $answer = [
        'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
        'answer' => [
          'error' => true,
          'problem' => 'Защита от создания дубликатов! В базе данных уже зарегистрирован пропуск № ' . $duplicatePermit->number . ' на указанное лицо и организацию, у которого дата начала дейстия ещё не наступила.',
          'solution' => 'Вместо создания ещё одного пропуска, отредактируйте срок действия зарегистрированного ранее и ещё не вступившего в действие пропуска № ' . $duplicatePermit->number . ', задав ему нужный вам период.',
        ]
      ];

      if($id) {
        if($clearedInputs['dateStart'] > $currentDate) {
          return $answer;
        }

        if($clearedInputs['dateEnd'] >= $duplicatePermit->dateStart) {
          return [
            'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
            'answer' => [
              'error' => true,
              'problem' => 'Произошло пересечение сроков действия редактируемого пропуска с пропуском № ' . $duplicatePermit->number . ', содержащего такие же значения, но с другим сроком дейстия, который начнётся с ' . date_format(date_create_from_format('Y-m-d H:i:s', $duplicatePermit->dateStart), 'd.m.Y') . '.',
              'solution' => 'Задайте период действия пропуска так, чтобы он не пересекался с периодом действия пропуска № ' . $duplicatePermit->number . '.',
            ]
          ];
        }

        if($clearedInputs['number'] > $duplicatePermit->number and $clearedInputs['dateStart'] < $duplicatePermit->dateStart) {
          return [
            'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
            'answer' => [
              'error' => true,
              'problem' => 'Ошибка хронологии! Для пропуска с более старшим номером (а именно для пропуска №' . $clearedInputs['number'] . '), вы пытаетесь задать более ранний срок действия, в то время как, в базе есть ранее зарегистрированный пропуск № ' . $duplicatePermit->number . ', у которого срок действия ещё не начался.',
              'solution' => 'При вводе пропусков с одинаковыми значениями, но разными сроками действия следите за тем, чтобы у пропусков с более младшим номером был более ранний срок действия, а у пропусков с более старшим номером был более поздний срок действия.',
            ]
          ];
        }
      } else {
        return $answer;
      }
    }


    // relevant both for store() and update() actions
    // find out if there is a permit with the same input data and not expired validity period
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
      ->where('permits.start', '<=', $currentDate) // select valid (not expired) permits
      ->where('permits.end', '>=', $currentDate) // select valid (not expired) permits
      ->where(function($query) use ($id) {
        if ($id) {
          return $query->where('permits.id', '!=', $id);
        }
      })
      ->orderByDesc('permits.id')
      ->first();

    if($duplicatePermit) {
      if($duplicatePermit->dateEnd >= $clearedInputs['dateStart']) {
        return [
          'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
          'answer' => [
            'error' => true,
            'problem' => 'Ошибка! Произошло пересечение сроков действия с пропуском № ' . $duplicatePermit->number . ', у которого такие же значения, но дата окончания дейстия позже либо равна дате начала действия вводимого (либо редактируемого) пропуска.',
            'solution' => 'Для пропусков с одинаковыми значениями задавайте сроки действия так, чтобы они не пересеклись. Вариант №1. Отредактируйте срок действия так, чтобы он не пересекался со сроком пропуска № ' . $duplicatePermit->number . '. Вариант №2. Сначала переведите пропуск № ' . $duplicatePermit->number . ' в число истёкших и затем повторите попытку добавления/редактирования вновь.',
          ]
        ];
      }

      // relevant only for update() actions
      if($clearedInputs['number'] < $duplicatePermit->number and $clearedInputs['dateStart'] > $duplicatePermit->dateStart) {
        return [
          'statusCode' => 409, // 409 CONFLICT - HTTP Status code which means that the request could not be completed due to a conflict with the current state of the target resource
          'answer' => [
            'error' => true,
            'problem' => 'Ошибка хронологии! Для пропуска с более ранним номером (а именно для пропуска №' . $clearedInputs['number'] . '), вы пытаетесь задать срок действия, который более поздний, чем у ранее зарегистрированного пропуска № ' . $duplicatePermit->number . ', срок действия которого ещё не истёк.',
            'solution' => 'При вводе пропусков с одинаковыми значениями, но разными сроками действия следите за тем, чтобы у пропусков с более младшим номером был более ранний срок действия, а у пропусков с более старшим номером был более поздний срок действия.',
          ]
        ];
      }
    }


    return false;
  }
}
