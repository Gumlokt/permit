<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model {
  use HasFactory;
  protected $fillable = [ 'name' ];

  public static function store($input) {
    return self::firstOrCreate(['name' => $input['company']]);
  }
}
