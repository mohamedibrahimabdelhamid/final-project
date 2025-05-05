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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (require JWT Token)
Route::middleware('auth:api')->group(function () {
    Route::post('/users/{id}/upload-profile', [AuthController::class, 'uploadProfilePicture']);

    Route::post('/books', [BookController::class, 'store']);        // Create book
    Route::put('/books/{id}', [BookController::class, 'update']);   // Update book
    Route::delete('/books/{id}', [BookController::class, 'destroy']);// Delete book
    Route::get('/books/genre/{genre}', [BookController::class, 'getByGenre']); // API to retrieve books by genre`



    Route::get('/audiobooks', [AudiobookController::class, 'index']);// all audiobooks
    Route::post('/audiobooks', [AudiobookController::class, 'store']);// Create audiobook
    Route::put('/audiobooks/{id}', [AudiobookController::class, 'update']);// update audiobook
    Route::delete('/audiobooks/{id}', [AudiobookController::class, 'destroy']);//delete audiobook
    // Attach tags to a book
    Route::post('/books/{bookId}/tags', [TagController::class, 'attachToBook']);
    Route::delete('/books/{bookId}/tags/{tagId}', [TagController::class, 'detachFromBook']);

    Route::get('/cart/{userId}', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{cartItemId}', [CartController::class, 'destroy']);
    Route::get('/carts', [CartController::class, 'allCarts']);

    Route::post('/purchases', [PurchaseController::class, 'store']);
    Route::get('/library', [PurchaseController::class, 'getUserLibrary']);
    Route::get('/purchases/{id}', [PurchaseController::class, 'show']);
    Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy']);

    Route::post('/discounts', [DiscountController::class, 'store']);
    Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);


    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags/{id}', [TagController::class, 'show']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
});

Route::get('/books', [BookController::class, 'index']);       // all books
Route::get('/books/browse', [BookController::class, 'browse']);// for seaech and filter and sort
Route::get('/books/{id}', [BookController::class, 'show']);     // Show single book by id
Route::get('/audiobooks/{id}', [AudiobookController::class, 'show']);// show audiobook by id

Route::get('/reviews/book/{bookId}', [ReviewController::class, 'bookReviews']);

Route::get('/discounts', [DiscountController::class, 'index']);
Route::get('/discounts/{id}', [DiscountController::class, 'show']);

Route::get('/files/{type}/{filename}', [FileController::class, 'serve']);


