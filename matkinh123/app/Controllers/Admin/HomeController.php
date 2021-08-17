<?php

namespace Matkinh123\Controllers\Admin;

use Illuminate\Http\Request;
use Matkinh123\Controllers\Controller;
use Matkinh123\Models\Revenue;
use Matkinh123\Models\Cost;
use DB;
use Lang;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return admin_view('home');
    }
}
