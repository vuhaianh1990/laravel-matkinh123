<nav class="navbar navbar-expand-md navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
  <a class="navbar-brand col-sm-2"
     href="{{ route('admin.home') }}">{{ config('app.name', 'Quản Lý Mắt Kính') }}</a>
  <input class="form-control form-control-dark col-sm-9" type="text" placeholder="Search" aria-label="Search" autofocus="autofocus">
  <ul class="navbar-nav col-sm-1 px-3 float-right">
    <!-- Authentication Links -->
    @guest
      <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
    @else
      <li class="nav-item dropdown">
        <a class="nav-link text-nowrap" href="#" id="navbarDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
          {{ Auth::user()->name }}
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ route('logout') }}"
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </li>
    @endguest
  </ul>
</nav>