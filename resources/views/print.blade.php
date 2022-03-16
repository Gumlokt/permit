<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>КПП ЗАО Пургаз</title>

    <link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css">
  </head>
  <body>

    @foreach ($pages as $page)
      <page class="page">
        @foreach ($page as $permit)
          <div class="permit">
            <img src="/images/logo.jpg" class="permit__logo" alt="Purgaz logo">

            <div class="permit__content">
              <h1 class="permit__surname">{{ $permit->people->surname }}</h1>
              <h2 class="permit__name">{{ $permit->people->forename }} {{ $permit->people->patronymic }}</h2>

              <ul class="permit__items">
                <li class="permit__item">
                  <h3 class="permit__title">Должность:</h3>
                  <p class="permit__value">{{ $permit->people->position }}</p>
                </li>

                <li class="permit__item">
                  <h3 class="permit__title">Организация:</h3>
                  <p class="permit__value">{{ $permit->companies->name }}</p>
                </li>

                <li class="permit__item">
                  <h3 class="permit__title">Действие:</h3>
                  <p class="permit__value">с {{ date_format(date_create($permit->start), 'd.m.Y') }} г. по {{ date_format(date_create($permit->end), 'd.m.Y') }} г.</p>
                </li>
              </ul>
            </div>

          </div>
        @endforeach
      </page>
    @endforeach

  </body>
</html>
