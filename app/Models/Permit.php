<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model {
  use HasFactory;
  protected $fillable = [ 'people_id', 'companies_id', 'number', 'start', 'end' ];

  public function companies() {
    return $this->belongsTo('App\Models\Company', 'companies_id');
  }

  public function people() {
    return $this->belongsTo('App\Models\Person', 'people_id');
  }


  public static function store($request, $permitData) {
    $sql = [
      'people_id' => $permitData['person']->id,
      'companies_id' => $permitData['company']->id,
      'number' => $request['number'],
      'start' => $request['dateStart'],
      'end' => $request['dateEnd']
    ];

    return self::firstOrCreate($sql);
  }
}
