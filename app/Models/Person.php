<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model {
  use HasFactory;
  protected $fillable = [ 'surname', 'forename', 'patronymic', 'position' ];

  public function permits() {
    return $this->hasMany('App\Models\Permit');
  }

  public static function store($input) {
    $sql = [
      'surname' => $input['surname'],
      'forename' => $input['forename'],
      'patronymic' => $input['patronymic'],
      'position' => $input['position']
    ];

    return self::firstOrCreate($sql);
  }
}
