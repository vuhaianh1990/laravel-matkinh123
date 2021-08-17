<nav class="col-md-2 d-none d-md-block bg-light sidebar">
  <div class="sidebar-sticky">
    <ul class="nav flex-column" style="margin-top:50px;">
      <li class="nav-item">
        <a class="nav-link" href="{{ admin_route('home') }}">
          <span data-feather="home"></span>
          Tổng Hợp <span class="sr-only">(current)</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ admin_route('revenue') }}">
          <span data-feather="dollar-sign"></span>
          Khoản thu
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ admin_route('cost') }}">
          <span data-feather="credit-card"></span>
          Khoản Chi
        </a>
      </li>


      {{-- <li class="nav-item">
        <a class="nav-link" href="#">
          <span data-feather="file"></span>
          Orders
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <span data-feather="shopping-cart"></span>
          Products
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <span data-feather="users"></span>
          Customers
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <span data-feather="bar-chart-2"></span>
          Reports
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <span data-feather="layers"></span>
          Integrations
        </a>
      </li> --}}
    </ul>

    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
      <span>Kết quả hoạt động kinh doanh</span>
      <a class="d-flex align-items-center text-muted" href="#">
        <span data-feather="plus-circle"></span>
      </a>
    </h6>
    <ul class="nav flex-column mb-2">
      <li class="nav-item">
        <a class="nav-link" href="{{ admin_route('month_business_results') }}">
          <span data-feather="file-text"></span>
            Tuần hiện tại
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ admin_route('month_business_results') }}">
          <span data-feather="file-text"></span>
            Tháng hiện tại
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <span data-feather="file-text"></span>
            Năm hiện tại
        </a>
      </li>
    </ul>
  </div>
</nav>