<?php

use app\Http\Controllers\API\AttendeesApiController;
use app\Http\Controllers\API\EventsApiController;
use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * ---------------
 * Organisers
 * ---------------
 */


/*
 * ---------------
 * Events
 * ---------------
 */
Route::resource('events', EventsApiController::class);


/*
 * ---------------
 * Attendees
 * ---------------
 */
Route::resource('attendees', AttendeesApiController::class);


/*
 * ---------------
 * Orders
 * ---------------
 */

/*
 * ---------------
 * Users
 * ---------------
 */

/*
 * ---------------
 * Check-In / Check-Out
 * ---------------
 */
