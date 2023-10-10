<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Public route
//Route::resource('products', ProductController::class);
 Route::post('/register',[AuthController::class, 'register']); 
 Route::post('/login',[AuthController::class, 'login']); 
 Route::post('/account/verify/{token}',[AuthController::class, 'verifyAccount'])->name('verify.email');
 Route::post('/reset',[AuthController::class, 'reset']);
 Route::get('/password/reset/{token}',[AuthController::class, 'resetAccount'])->name('reset.password');
 Route::post('/password/reset',[AuthController::class, 'saveResetAccount']);
 // Route::get('/products', [ProductController::class, 'index']);
 // Route::get('/products/{id}', [ProductController::class, 'show']);
 // Route::get('/products/search/{name}', [ProductController::class, 'search']);    
 
// Route::group(['middleware' => ['auth:sanctum', 'is_verify_email']], function () {

//Protected route
 Route::group(['middleware' => ['auth:sanctum']], function () {
 	
 	//Profile Route
 Route::get('/profile', [SettingsController::class, 'profile']);
 Route::post('/profile', [SettingsController::class, 'updateProfile']);	
 Route::post('/change-password', [SettingsController::class, 'changePassword']);
 Route::get('/profile-image', [SettingsController::class, 'getProfilePic']);
 Route::post('/profile-image', [SettingsController::class, 'profileImage']);	

 //Setting Route
 Route::get('/currency', [SettingsController::class, 'currency']);
 Route::post('/currency', [SettingsController::class, 'updateCurrency']);
 Route::get('/lang', [SettingsController::class, 'lang']);
 Route::post('/lang', [SettingsController::class, 'updateLang']);

 //Banks Route
 Route::get('/bank', [BankController::class, 'bank']);
 Route::post('/bank', [BankController::class, 'addBank']);
 Route::get('/editbank/{id}', [BankController::class, 'editBank']);
 Route::post('/updatebank/{id}', [BankController::class, 'updateBank']);
 Route::delete('/deletebank/{id}',[BankController::class, 'deleteBank']);

 //User Route
 Route::get('/users', [SettingsController::class, 'users']);
 Route::get('/edituser/{id}', [SettingsController::class, 'EditUser']);
 Route::post('/updateuser/{id}', [SettingsController::class, 'updatedUser']);
 Route::delete('/deleteuser/{id}',[SettingsController::class, 'deleteUser']);

 //Setting Route
 Route::get('/settings', [SettingsController::class, 'settings']);
 Route::post('/settings', [SettingsController::class, 'updateSetting']);

 // Route::post('/products',[ProductController::class, 'store']);
 // Route::put('/products/{id}',[ProductController::class, 'update']);
 // Route::delete('/products/{id}',[ProductController::class, 'destroy']);
 Route::post('/logout',[AuthController::class, 'logout']);  
});
