<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\ApiClientManager;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use stdClass;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class HomeController extends Controller
{
    public static $api_client_manager;

    public function __construct()
    {
        $this::$api_client_manager = new ApiClientManager();
        $this->middleware('auth')->except(['changeLanguage', 'index']);
    }

    // ==================================== HTTP GET METHODS ====================================
    /**
     * GET: Change language
     *
     * @param  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLanguage($locale)
    {
        app()->setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->back();
    }

    /**
     * GET: Welcome/Home page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $year = 2024;
        $portion = 2;

        $dates = getHalfYearDates($year, $portion);

        dd($dates);

        // $weekMap = [
        //     'zero' => 'SUNDAY',
        //     'one' => 'MONDAY',
        //     'two' => 'TUESDAY',
        //     'three' => 'WEDNESDAY',
        //     'four' => 'THURSDAY',
        //     'five' => 'FRIDAY',
        //     'six' => 'SATURDAY',
        // ];
        // $object = new stdClass();

        // foreach ($weekMap as $key => $week) {
        //     $object->$key = $week;
        // }

        // dd($object);
        // dd(Carbon::DECEMBER);

        // $weekMap = [
        //     0 => 'SUNDAY',
        //     1 => 'MONDAY',
        //     2 => 'TUESDAY',
        //     3 => 'WEDNESDAY',
        //     4 => 'THURSDAY',
        //     5 => 'FRIDAY',
        //     6 => 'SATURDAY',
        // ];
        // $dayOfTheWeek = Carbon::now()->dayOfWeek;
        // $weekday = $weekMap[$dayOfTheWeek];

        // $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY)->toDateString();

        // dd($endOfWeek);

        // $year = date('Y');
        // $month = date('m');
        // $day = date('d');
        // $date = Carbon::parse($year . '-' . $month . '-' . $day);
        // $weekNumber = $date->weekNumberInMonth;
        // $dates = getStartAndEndOfWeekInMonth($year, $month, $weekNumber);

        // dd($dates);

        // if (Session::has('user_demo')) {
        //     return view('demo.welcome');

        // } else {
        //     return redirect()->route('login');
        // }
    }

}
