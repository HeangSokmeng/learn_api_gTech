<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BorrowsController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserThreeController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return 'API';
// });

// Route::apiResource('customer', CustomerController::class);
Route::put('customer/{id?}',[CustomerController::class,'update']);
Route::get('customer', [CustomerController::class, 'index']);
Route::post('customer', [CustomerController::class, 'store']);


Route::get('product', [ProductController::class, 'index']);
Route::post('product', [ProductController::class, 'store']);
Route::post('product/{id?}', [ProductController::class, 'update']);

Route::get('order', [OrderController::class, 'index']);
Route::post('order', [OrderController::class, 'store']);
Route::put('order/{id?}', [OrderController::class, 'update']);
Route::get('customer/order', [OrderController::class, 'customerOrder']);


Route::get('staff', [StaffController::class, 'index']);
Route::post('staff', [StaffController::class, 'store']);
Route::put('staff/{id?}', [StaffController::class, 'update']);


Route::get('attendance', [AttendanceController::class, 'index']);
// Route::post('attendance', [AttendanceController::class, 'store']);
Route::put('attendance/{id?}', [AttendanceController::class, 'update']);
Route::post('/check-attendance', [AttendanceController::class, 'checkAttendance']);
Route::get('attendance/status-staff', [AttendanceController::class, 'geListStaff']);

Route::get('post', [PostsController::class, 'index']);
Route::post('post', [PostsController::class, 'store']);
Route::put('post/{id?}', [PostsController::class, 'update']);

Route::get('user', [UserController::class, 'index']);
Route::post('user', [UserController::class, 'store']);
Route::post('user/{id?}', [UserController::class, 'update']);

Route::get('comment', [CommentsController::class, 'index']);
Route::get('getPost', [CommentsController::class, 'getPost']);
Route::post('comment', [CommentsController::class, 'store']);
Route::get('comment/{post}', [CommentsController::class, 'showCommentsWithReplies']);


Route::get('book', [BooksController::class, 'index']);
Route::post('book', [BooksController::class, 'store']);
Route::put('book/{id?}', [BooksController::class, 'update']);

Route::get('user', [UserThreeController::class, 'index']);
Route::post('user', [UserThreeController::class, 'store']);
Route::put('user/{id?}', [UserThreeController::class, 'update']);

Route::get('borrow/getAll', [BorrowsController::class, 'index']);
Route::post('borrow', [BorrowsController::class, 'store']);
Route::put('borrow/{id?}', [BorrowsController::class, 'update']);
Route::get('calculateFine', [BorrowsController::class, 'calculateFine']);
Route::get('getListBorrow/{id?}', [BorrowsController::class, 'getListBorrow']);
Route::get('borrow/{id?}', [BorrowsController::class, 'calculateFine']);
Route::get('borrowDetail/{id?}', [BorrowsController::class, 'getBorrowDetail']);




