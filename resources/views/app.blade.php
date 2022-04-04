<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>F2x Operator</title>
    <!-- Styles -->
    <link href="{{ mix('css/classic.css') }}" rel="stylesheet">
    <script type='text/javascript'>
    (function (d, t) {
    var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
    bh.type = 'text/javascript';
    bh.src = 'https://www.bugherd.com/sidebarv2.js?apikey=npbarpntsljuzdbotd6ftw';
    s.parentNode.insertBefore(bh, s);
    })(document, 'script');
    </script>
</head>
<body>
    <div id="app" class="h-100"></div>

    <!-- <script src="{{ mix('js/app.js') }}"></script> -->
</body>
</html>