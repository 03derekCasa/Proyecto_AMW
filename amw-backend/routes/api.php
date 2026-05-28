<?php

use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CommentLikeController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
Rutas públicas
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}/comments', [CommentController::class, 'index']);

Route::get('/artists', [ArtistController::class, 'index']);
Route::get('/artists/{id}', [ArtistController::class, 'show'])->whereNumber('id');

/*
Rutas protegidas
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    Perfil
    */

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/image', [ProfileController::class, 'uploadImage']);
    Route::post('/profile/cover-image', [ProfileController::class, 'uploadCoverImage']);

    Route::post('/upload/image', [UploadController::class, 'image']);

    /*
    Usuarios y perfiles públicos
    */

    Route::get('/users', [UserController::class, 'index']);

    Route::get('/users/{id}/public-profile', [UserController::class, 'publicProfile'])
        ->whereNumber('id');

    /*
    Seguidores
    */

    Route::post('/users/{user}/follow', [FollowController::class, 'store'])
        ->whereNumber('user');

    Route::delete('/users/{user}/follow', [FollowController::class, 'destroy'])
        ->whereNumber('user');

    /*
    Posts
    */

    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/my-posts', [PostController::class, 'myPosts']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    /*
    Likes
    */

    Route::post('/posts/{id}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{id}/like', [LikeController::class, 'destroy']);
    Route::get('/my-likes', [LikeController::class, 'myLikes']);

    /*
    Comentarios
    */

    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::post('/comments/{comment}/like', [CommentLikeController::class, 'store']);
    Route::delete('/comments/{comment}/like', [CommentLikeController::class, 'destroy']);

    /*
    Notificaciones
    */

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/summary', [NotificationController::class, 'summary']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);

    /*
        Mensajes
    */

    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);

    Route::patch('/conversations/{conversation}/read', [ConversationController::class, 'markAsRead'])
        ->whereNumber('conversation');

    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index'])
        ->whereNumber('conversation');

    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])
        ->whereNumber('conversation');
});
