<?php

    use App\Models\DeviceState;
    use Illuminate\Http\Request;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Auth\ForgotPasswordController;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    // Auth::ROUTES(['register' => FALSE]);

    /* Here are the routes that need authentication.
    */
    Route::GROUP(['middleware' => 'auth'], function ()
    {
        // Index controller.
        Route::GET('/', 'TableController@index')->name('table');
        // The graph is rendered.
        Route::GET('graph/{device_id}', 'DashboardController@index');
        // The index table is rerendered.
        Route::GET('/all', ['uses' => 'TableController@renderAll'])->name('rerender');
    });

    /*
     * Function for addin a device.
     * Parameters: Request, a JSON variable with the device information
     * for the GET method.
     */
    Route::GET('/devices/add', 'DeviceController@save')->name('add');

    /*
     * Function for saving the device states.
     * Parameters: Request, a JSON variable with the device information
     * for the POST method.
     */
    Route::POST('/devices/save', 'DeviceController@save')->name('save');

    /*
     * Function for updating the device states.
     * Parameters: Request, a JSON variable with the device information
     * for the GET method.
     */
    Route::PUT('/devices/update', 'DeviceController@update')->name('update');

    /*
     * Function for deleting a device state.
     * Parameters: Request, a JSON variable with the device information
     * for the GET method.
     */
    Route::GET('/devices/delete', 'DeviceController@delete')->name('delete');

    Route::get('/forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::post('/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

    Auth::ROUTES();

    Route::GET('/home', [
        App\Http\Controllers\HomeController::class,
        'index'
    ])->name('home');
