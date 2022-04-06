<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Permit</title>

  <link href="{{ asset('vendor/google/fonts/MaterialIcons/MaterialIcons.css') }}" rel="stylesheet">
  <link href="{{ asset('vendor/google/fonts/Roboto/roboto.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">

</head>
<body>
  <div id="app"></div>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
