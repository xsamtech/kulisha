<?php
/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\AppDataController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MiscellaneousController;
use App\Http\Controllers\Web\ThirdPartyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| ROUTES FOR EVERY ROLES
|--------------------------------------------------------------------------
*/
// Generate symbolic link
Route::get('/symlink', function () { return view('symlink'); })->name('generate_symlink');
// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/language/{locale}', [HomeController::class, 'changeLanguage'])->name('change_language');
Route::get('/notifications', [HomeController::class, 'notification'])->name('notification.home');
Route::get('/notifications/{entity}', [HomeController::class, 'notificationEntity'])->name('notification.entity');
Route::get('/stories', [HomeController::class, 'story'])->name('story.home');
Route::get('/stories/{id}', [HomeController::class, 'storyDatas'])->whereNumber('id')->name('story.datas');
Route::get('/trends', [HomeController::class, 'trend'])->name('trend.home');
Route::get('/trends/{id}', [HomeController::class, 'trendDatas'])->whereNumber('id')->name('trend.datas');
Route::get('/suggestions', [HomeController::class, 'suggestion'])->name('suggestion.home');
Route::get('/news', [HomeController::class, 'news'])->name('news.home');
Route::get('/news/{id}', [HomeController::class, 'newsDatas'])->whereNumber('id')->name('news.datas');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart.home');
Route::get('/cart/{id}', [HomeController::class, 'cartDatas'])->whereNumber('id')->name('cart.datas');
Route::get('/cart/{entity}', [HomeController::class, 'cartEntity'])->name('cart.entity');
Route::get('/cart/{entity}/{id}', [HomeController::class, 'cartEntityDatas'])->whereNumber('id')->name('cart.entity.datas');
Route::get('/communities', [HomeController::class, 'community'])->name('community.home');
Route::get('/communities/{id}', [HomeController::class, 'communityDatas'])->whereNumber('id')->name('community.datas');
Route::get('/communities/{entity}', [HomeController::class, 'communityEntity'])->name('community.entity');
Route::get('/communities/{entity}/{id}', [HomeController::class, 'communityEntityDatas'])->whereNumber('id')->name('community.entity.datas');
Route::get('/events', [HomeController::class, 'event'])->name('event.home');
Route::get('/events/{id}', [HomeController::class, 'eventDatas'])->whereNumber('id')->name('event.datas');
Route::get('/events/{entity}', [HomeController::class, 'eventEntity'])->name('event.entity');
Route::get('/events/{entity}/{id}', [HomeController::class, 'eventEntityDatas'])->whereNumber('id')->name('event.entity.datas');
// Account
Route::get('/{username}', [AccountController::class, 'profile'])->name('profile.home');
Route::get('/{username}/{entity}', [AccountController::class, 'profileEntity'])->name('profile.entity');
Route::get('/messages', [AccountController::class, 'message'])->name('message.home');
Route::get('/messages/{id}', [AccountController::class, 'messageDatas'])->whereNumber('id')->name('message.datas');
Route::get('/settings', [AccountController::class, 'settings'])->name('settings.home');
Route::get('/settings/{id}', [AccountController::class, 'settingsDatas'])->whereNumber('id')->name('settings.datas');
Route::get('/settings/{entity}', [AccountController::class, 'settingsEntity'])->name('settings.entity');
Route::get('/settings/{entity}/{id}', [AccountController::class, 'settingsEntityDatas'])->whereNumber('id')->name('settings.entity.datas');

/*
|--------------------------------------------------------------------------
| ROUTES FOR "Admin" AND "Developer"
|--------------------------------------------------------------------------
*/
// Home
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
// Account
Route::get('/account', [AccountController::class, 'account'])->name('account.home');
Route::get('/account/{entity}', [AccountController::class, 'accountEntity'])->name('account.entity');
Route::get('/history', [AccountController::class, 'history'])->name('history');

/*
|--------------------------------------------------------------------------
| ROUTES FOR "Admin"
|--------------------------------------------------------------------------
*/
// AppData
Route::get('/group', [AppDataController::class, 'group'])->name('group.home');
Route::get('/group/{id}', [AppDataController::class, 'groupDatas'])->whereNumber('id')->name('group.datas');
Route::get('/group/{entity}', [AppDataController::class, 'groupEntity'])->name('group.entity');
Route::get('/group/{entity}/{id}', [AppDataController::class, 'groupEntityDatas'])->whereNumber('id')->name('group.entity.datas');
Route::get('/field', [AppDataController::class, 'field'])->name('field.home');
Route::get('/field/{id}', [AppDataController::class, 'fieldDatas'])->whereNumber('id')->name('field.datas');
Route::get('/field/{entity}', [AppDataController::class, 'fieldEntity'])->name('field.entity');
Route::get('/field/{entity}/{id}', [AppDataController::class, 'fieldEntityDatas'])->whereNumber('id')->name('field.entity.datas');
// Miscellaneous
Route::get('/miscellaneous', [MiscellaneousController::class, 'miscellaneous'])->name('miscellaneous.home');
Route::get('/miscellaneous/{entity}', [MiscellaneousController::class, 'miscellaneousEntity'])->name('miscellaneous.entity');
Route::get('/miscellaneous/{entity}/{id}', [MiscellaneousController::class, 'miscellaneousEntityDatas'])->whereNumber('id')->name('miscellaneous.entity.datas');

/*
|--------------------------------------------------------------------------
| ROUTES FOR "Developer"
|--------------------------------------------------------------------------
*/
// ThirdParty
Route::get('/apis', [ThirdPartyController::class, 'api'])->name('api.home');
Route::get('/apis/{entity}', [ThirdPartyController::class, 'apiEntity'])->name('api.entity');
Route::get('/apis/{entity}/{id}', [ThirdPartyController::class, 'apiEntityDatas'])->whereNumber('id')->name('api.entity.datas');
Route::get('/integrations', [ThirdPartyController::class, 'integration'])->name('integration.home');
Route::get('/integrations/{entity}', [ThirdPartyController::class, 'integrationEntity'])->name('integration.entity');
Route::get('/integrations/{entity}/{id}', [ThirdPartyController::class, 'integrationEntityDatas'])->whereNumber('id')->name('integration.entity.datas');

