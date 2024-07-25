<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\ApiClientManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        $subscribers = [
            0 => ['id' => 1, 'firstname' => 'Clark', 'lastname' => 'Kent'],
            1 => ['id' => 2, 'firstname' => 'Lois', 'lastname' => 'Lane'],
            2 => ['id' => 3, 'firstname' => 'Bruce', 'lastname' => 'Wayne'],
            3 => ['id' => 4, 'firstname' => 'Barry', 'lastname' => 'Allen'],
            4 => ['id' => 5, 'firstname' => 'Diana', 'lastname' => 'Prince']
        ];
        $restrictions = [
            0 => ['user_id' => 2, 'firstname' => 'Lois', 'lastname' => 'Lane'],
            1 => ['user_id' => 5, 'firstname' => 'Diana', 'lastname' => 'Prince']
        ];
        $members_ids = array_diff(getArrayKeys($subscribers, 'id'), getArrayKeys($restrictions, 'user_id'));

        foreach ($members_ids as $member_id):
            print_r($member_id . '<hr><br>');
        endforeach;

        // $array1 = ['un', 'quatre', 'deux', 'trois'];
        // $array2 = ['neuf', 'un', 'huit', 'deux', 'trois', 'cinq', 'six', 'sept', 'quatre', 'dix'];

        // $array3 = count($array1) > count($array2) ? array_diff($array1, $array2) : array_diff($array2, $array1);

        // dd($array3);

        // if (Session::has('user_demo')) {
        //     return view('demo.welcome');

        // } else {
        //     return redirect()->route('login');
        // }
    }

}
