<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Controllers\Portal\AiToolDataController;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Portal\AiToolCategoryController;
use App\Http\Controllers\Portal\BlogDataController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Portal\CourseDataController;
use App\Http\Controllers\Portal\BlogCategoryController;
use App\Http\Controllers\Portal\ContactDataController;
use App\Http\Controllers\Portal\CourseCategoryController;
use App\Http\Controllers\Portal\UserDataController;
use App\Http\Controllers\Portal\CollectionController;
use App\Http\Controllers\Portal\ReviewController;
use App\Http\Controllers\Portal\QuestionAnswerController;


//
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// logout
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login'); // Redirect to homepage or login
})->name('logout');


// Redirection
Route::get('/aiguy', function () {
    return view('aiguy.login'); // or your desired view
})->middleware('auth');


Route::get('/', function () {
    return redirect('/login');
});

//Google register route
Route::get('auth/google', function () {
    
    return Socialite::driver('google')->redirect();
})->name('google.login');


//Google register callback route
Route::get('auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
    
    $user = User::updateOrCreate([
        'email' => $googleUser->email,
    ], [
        'full_name' => $googleUser->name,
        'google_id' => $googleUser->id,
        'avatar' => $googleUser->avatar,
        'google_token'  => $googleUser->token,
        'email_verified'    => $googleUser->user['verified_email'],
        'password' => bcrypt(str()->random(16)), // Set a random password
    ]); 
    
    // dd($googleUser->user['verified_email']);
    
    $token = $user->createToken('API Token')->accessToken;

    dd($token);
});

Route::middleware('role:admin')->group(function(){
    
    // Admin Ai-Tools Category
    Route::group(['prefix'  => 'ai-tools-category'], function() {
        Route::get('/', [AiToolCategoryController::class, 'list'])->name('tools.categories.list');
        Route::get('/view/{id}', [AiToolCategoryController::class, 'show'])->name('tools.categories.show');
        Route::get('/add', [AiToolCategoryController::class, 'addEdit'])->name('tools.categories.addEdit');
        Route::post('/store', [AiToolCategoryController::class, 'store'])->name('tools.categories.store');
        Route::get('/edit/{id}', [AiToolCategoryController::class, 'edit'])->name('tools.category.edit');
        Route::put('/update/{id}', [AiToolCategoryController::class, 'update'])->name('tools.categories.update');
        Route::get('/delete/{id}', [AiToolCategoryController::class, 'destroy'])->name('tools.category.delete');
    });
    
    // Admin Ai-Tools
    Route::group(['prefix'  => 'ai-tools'], function() {
        Route::get('/', [AiToolDataController::class, 'list'])->name('ai-tools.list');
        Route::get('/view/{id}', [AiToolDataController::class, 'view'])->name('ai-tools.view');
        Route::get('/add', [AiToolDataController::class, 'addEdit'])->name('ai-tools.addEdit');
        Route::post('/ai-tools/store', [AiToolDataController::class, 'store'])->name('tools.store');
        Route::get('/edit/{id}', [AiToolDataController::class, 'edit'])->name('tools.edit');
        Route::put('/update/{id}', [AiToolDataController::class, 'update'])->name('tools.update');
        Route::get('/delete/{id}', [AiToolDataController::class, 'destroy'])->name('ai-tools.delete');
    });
    
    // Admin Blog
    Route::group(['prefix'  => 'blog'], function() {
        Route::get('/', [BlogDataController::class, 'list'])->name('blog.list');
        Route::get('/view/{id}', [BlogDataController::class, 'view'])->name('blog.view');
        Route::get('/add', [BlogDataController::class, 'addEdit'])->name('blog.addEdit');
        Route::post('/store', [BlogDataController::class, 'store'])->name('blog.store');
        Route::get('/edit/{id}', [BlogDataController::class, 'edit'])->name('blog.edit');
        Route::put('/update/{id}', [BlogDataController::class, 'update'])->name('blog.update');
        Route::get('/delete/{id}', [BlogDataController::class, 'destroy'])->name('blog.delete');
    });
    
    // Admin Blogs Category
    Route::group(['prefix'  => 'blog-category'], function() {
        Route::get('/', [BlogCategoryController::class, 'list'])->name('blog.categories.list');
        Route::get('/add', [BlogCategoryController::class, 'addEdit'])->name('blog.categories.addEdit');
        Route::post('/store', [BlogCategoryController::class, 'store'])->name('blog.categories.store');
        Route::get('/edit/{id}', [BlogCategoryController::class, 'edit'])->name('blog.category.edit');
        Route::put('/update/{id}', [BlogCategoryController::class, 'update'])->name('blog.categories.update');
        Route::get('/delete/{id}', [BlogCategoryController::class, 'destroy'])->name('blog.category.delete');
    });
    
    // Admin Courses
    Route::group(['prefix'  => 'courses'], function() {
        Route::get('/', [CourseDataController::class, 'list'])->name('courses.list');
        // Route::get('categories/{id}', [CourseDataController::class, 'show'])->name('courses.show');
        Route::get('/add', [CourseDataController::class, 'addEdit'])->name('courses.addEdit');
        Route::post('/store', [CourseDataController::class, 'store'])->name('courses.store');
        Route::get('/edit/{id}', [CourseDataController::class, 'edit'])->name('courses.edit');
        Route::put('/update/{id}', [CourseDataController::class, 'update'])->name('courses.update');
        Route::get('/delete/{id}', [CourseDataController::class, 'destroy'])->name('courses.delete');
    });
    
     // Admin Course Category
    Route::group(['prefix'  => 'course-category'], function() {
        Route::get('/', [CourseCategoryController::class, 'list'])->name('course.categories.list');
        Route::get('/add', [CourseCategoryController::class, 'addEdit'])->name('course.categories.addEdit');
        Route::post('/store', [CourseCategoryController::class, 'store'])->name('course.categories.store');
        Route::get('/edit/{id}', [CourseCategoryController::class, 'edit'])->name('course.category.edit');
        Route::put('/update/{id}', [CourseCategoryController::class, 'update'])->name('course.categories.update');
        Route::get('/delete/{id}', [CourseCategoryController::class, 'destroy'])->name('course.category.delete');
    });

    // User
     Route::group(['prefix'  => 'user'], function() {
        Route::get('/', [UserDataController::class, 'list'])->name('user.list');
        Route::get('/view/{id}', [UserDataController::class, 'view'])->name('user.view');
    });

    // Collection
    Route::group(['prefix'  => 'collection'], function() {
        Route::get('/', [CollectionController::class, 'list'])->name('collection.list');
        Route::get('/view/{id}', [CollectionController::class, 'view'])->name('collection.view');
    });

    // Review
    Route::group(['prefix'  => 'review'], function() {
        Route::get('/', [ReviewController::class, 'list'])->name('review.list');
        Route::put('/update-status/{id}', [ReviewController::class, 'updateStatus'])->name('review.updateStatus');
    });

    // Question Answer
    Route::group(['prefix'  => 'question-answer'], function() {
        Route::get('/questions', [QuestionAnswerController::class, 'questionsList'])->name('question-answer.questions-list');
        Route::put('/update-status/{id}', [QuestionAnswerController::class, 'updateStatus'])->name('question-answer.updateStatus');
        Route::get('/questions-view/{id}', [QuestionAnswerController::class, 'questionsView'])->name('question-answer.question-view');

        Route::get('/answers', [QuestionAnswerController::class, 'answerList'])->name('question-answer.answer-list');
        Route::put('/update-status-answer/{id}', [QuestionAnswerController::class, 'updateStatusAnswer'])->name('question-answer.updateStatusAnswer');
    });
    
    
    Route::get('/contact', [ContactDataController::class, 'list'])->name('contact');
});

require __DIR__.'/auth.php';
