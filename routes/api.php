<?php
namespace App\Http\Controllers;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/books', [BookController::class, 'index']);         // List all books
Route::get('/books/{id}', [BookController::class, 'show']);     // Show single book by id
Route::post('/books', [BookController::class, 'store']);        // Create book
Route::put('/books/{id}', [BookController::class, 'update']);   // Update book
Route::delete('/books/{id}', [BookController::class, 'destroy']);// Delete book

Route::get('/audiobooks', [AudiobookController::class, 'index']);// List all audiobooks
Route::post('/audiobooks', [AudiobookController::class, 'store']);// Create audiobook
Route::get('/audiobooks/{id}', [AudiobookController::class, 'show']);// show audiobook by id
Route::put('/audiobooks/{id}', [AudiobookController::class, 'update']);// update audiobook
Route::delete('/audiobooks/{id}', [AudiobookController::class, 'destroy']);//delete audiobook

Route::get('/tags', [TagController::class, 'index']);
Route::post('/tags', [TagController::class, 'store']);
Route::get('/tags/{id}', [TagController::class, 'show']);
Route::put('/tags/{id}', [TagController::class, 'update']);
Route::delete('/tags/{id}', [TagController::class, 'destroy']);

// Attach tags to a book
Route::post('/books/{bookId}/tags', [TagController::class, 'attachToBook']);


Route::get('/cart/{userId}', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'store']);
Route::delete('/cart/{cartItemId}', [CartController::class, 'destroy']);
Route::get('/carts', [CartController::class, 'allCarts']);

Route::get('/purchases', [PurchaseController::class, 'index']);
Route::post('/purchases', [PurchaseController::class, 'store']);
Route::get('/purchases/{id}', [PurchaseController::class, 'show']);
Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy']);

Route::get('/users/{userId}/library', [PurchaseController::class, 'getUserLibrary']);


Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/user/{userId}', [TransactionController::class, 'userTransactions']);
Route::post('/transactions', [TransactionController::class, 'store']);
