<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Permit</title>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
  <link href="{{ asset('vendor/fonts/Roboto/roboto.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">

  <!-- <style>
    .app {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      background-color: #333;
      border: 1px solid #f00;
    }
  </style> -->
</head>
<body>
  <div id="app"></div>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
