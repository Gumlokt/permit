<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model {
  use HasFactory;
  protected $fillable = [ 'people_id', 'companies_id', 'number', 'start', 'end' ];

  public static function store($request, $data) {

    $dateStart =  date_format(date_create_from_format('d.m.Y H:i:s', $request['dateStart'] . ' 00:00:00'), 'Y-m-d H:i:s');
    $dateEnd = date_format(date_create_from_format('d.m.Y H:i:s', $request['dateEnd'] . ' 23:59:59'), 'Y-m-d H:i:s');

    $sql = [
      'people_id' => $data['company']->id,
      'companies_id' => $data['person']->id,
      'number' => $request['number'],
      'start' => $dateStart,
      'end' => $dateEnd
    ];

    return self::firstOrCreate($sql);
  }
}