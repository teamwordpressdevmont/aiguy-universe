<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\JwtAuthController;
use App\Http\Controllers\Api\MainController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\AIToolsController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CommunityController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/google-register' , [AuthController::class, 'googleRegister']);

//For Public APIs Start
Route::get('/generate-token', [MainController::class, 'generateAccessToken']);
Route::get('/get-token', [MainController::class, 'getAccessToken']);

Route::middleware(['verifyAccessToken'])->group(function () {

    //Search API
    Route::get( '/search', [MainController::class, 'searchToolsBlogsCourses'] )->name( 'search' );
    
    //Submit Contact Form API
    Route::post('/submit-contact' , [ContactController::class, 'submitContactForm']);

    //Categories API's
    Route::group(['prefix' => 'categories'], function () {
        Route::get( '/get', [AIToolsController::class, 'fetchCategories1'] )->name( 'category.get' );
        Route::get( '/', [AIToolsController::class, 'fetchCategories2'] )->name( 'category.get2' );
    });

    //Blogs API's
    Route::group( ['prefix' => 'blog'], function() {
        Route::get( '/', [BlogController::class, 'getBlogs'] )->name( 'get.all.blogs' );
    } );
    
    //Course API's
    Route::group( ['prefix' => 'course'], function() {
        Route::get( '/', [CourseController::class, 'getCourses'] )->name( 'get.all.courses' );
    } );
    
    //AI Tools API's
    Route::group( ['prefix' => 'ai-tool'], function() {
        Route::get('/', [AIToolsController::class, 'getTools'])->name('aitools.gettools');
    } );

    //Fetch Reviews API
    Route::group( ['prefix' => 'review'], function() {
        Route::get('/', [AIToolsController::class, 'getReviews'])->name('aitools.getreviews');
    } );
});
//For Public APIs End

Route::middleware('auth:api')->group(function () {
    
    Route::middleware('role:user')->group(function(){
        
        Route::get('/user', [AuthController::class, 'userProfile'] )->name( 'user.profile' );
        
        Route::get('/user/dashboard', function() {
            return response()->json(['message' => 'Welcome User']);
        });
        
        //Profile API
        Route::group( ['prefix' => 'profile'], function(){
            Route::post( '/update', [AuthController::class, 'profileUpdate'] )->name( 'profile.update' );
            Route::post( '/delete', [AuthController::class, 'profileDelete'] )->name( 'profile.delete' );
        } );
        
        //Add Review API
        Route::group( ['prefix' => 'review'], function() {
            Route::post('/add', [AIToolsController::class, 'writeReview'])->name('aitools.writereviews');
        } );

        //Collection API's
        Route::group( ['prefix' => 'collection'], function() {
            Route::get( '/', [CollectionController::class, 'getCollectionsData'] )->name( 'collection.get' );
            Route::post( '/add', [CollectionController::class, 'createCollection'] )->name('collection.add');
            Route::post( '/add-ai-tool', [ CollectionController::class, 'addToolInCollection' ] )->name( 'collection.add.tool' );
            Route::post( '/remove-ai-tool', [ CollectionController::class, 'removeToolInCollection' ] )->name( 'collection.remove.tool' );
            Route::post('/update', [CollectionController::class, 'collectionUpdate'] )->name( 'collection.update' );
            Route::get('/share', [CollectionController::class, 'collectionShare'] )->name( 'collection.share' );
            Route::post('/delete', [CollectionController::class, 'collectionDelete'] )->name( 'collection.delete' );
        } );
        
        //Comment API's
        Route::group( ['prefix' => 'comment'], function() {
            Route::get( '/', [CommentController::class, 'fetchQuestionAnswers'] )->name( 'comment.get' );
            Route::post( '/add-question', [CommentController::class, 'addQuestion'] )->name('comment.question.add');
            Route::post( '/add-answer', [CommentController::class, 'addAnswers'] )->name('comment.answers.add');
            Route::post( '/like-dislike', [ CommentController::class, 'addLikeDislikeOnAnswers' ] )->name( 'comment.add.like.dislike' );
        } );

        //Community API's
        Route::group( ['prefix' => 'community'], function() {
            Route::get( '/', [CommunityController::class, 'fetchQuestionAnswers'] )->name( 'community.get' );
            Route::post( '/add-question', [CommunityController::class, 'addQuestionCommunity'] )->name('community.question.add');
            Route::post( '/add-answer', [CommunityController::class, 'addAnswersCommunity'] )->name('community.answer.add');
            Route::post( '/like-dislike', [ CommunityController::class, 'addLikeDislikeOnAnswers' ] )->name( 'community.add.like.dislike' );
        } );

        //Logout API
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});










