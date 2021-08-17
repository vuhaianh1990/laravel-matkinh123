<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Quản Lý Mắt Kính') }}</title>

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/admin/admin.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  @yield('css')
</head>
<body>
<div id="app">
  @include(admin_theme() . 'partials.header')
  <div class="py-4">
    <div class="container-fluid">
      <div class="row">
        @include(admin_theme() . 'partials.sidebar')
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
          @yield('content')
        </main>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->

<!-- Placed at the end of the document so the pages load faster -->
<script src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<!-- Icons -->
<script src="{{ asset('js/feather.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

<script>
    feather.replace();

    // Add class Active to navigation menu
    var route = location.pathname;
    route = route.substr(route.lastIndexOf('/') + 1);
    $('.nav-item a.nav-link').each(function(){
        var $this = $(this);
        var url   = $this.attr('href');
        pathname  = url.substr(url.lastIndexOf('/') + 1);
        // if the current path is like this link, make it active
        if(pathname === route){
            $this.addClass('active');
        }
    })
</script>
<!-- Custom JS -->
@yield('js')
</body>
</html>
