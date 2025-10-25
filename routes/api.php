<?php

use App\Http\Controllers\Admin\DeleteProductController as AdminDeleteProductController;
use App\Http\Controllers\Admin\FetchProductApprovmentController;
use App\Http\Controllers\Admin\ProductApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SignOutController;
use App\Http\Controllers\Delete\DeleteProductController;
use App\Http\Controllers\Insert\ProductController;
use App\Http\Controllers\Select\FetchBiddingDetailsController;
use App\Http\Controllers\Select\FetchFavoriteListController;
use App\Http\Controllers\Select\FetchPersonalListController;
use App\Http\Controllers\Select\FetchProductsController;
use App\Http\Controllers\Services\AppVersionController;
use App\Http\Controllers\Services\UpdateFcmTokenController;
use App\Http\Controllers\Services\UploadImagesController;
use App\Http\Controllers\Update\AddAbidController;
use App\Http\Controllers\Update\AddCommentController;
use App\Http\Controllers\Update\AddRatingController;
use App\Http\Controllers\Update\CommentController;
use App\Http\Controllers\Update\FavoritesListController;
use App\Http\Controllers\Update\IncreaseLikesCountController;
use App\Http\Controllers\Update\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\SingInWithGoogleController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Insert\InsertNotificationController;
use App\Http\Controllers\Notifications\FetchAllNotification;
use App\Http\Controllers\Notifications\UpdateNotificationStatus;
use App\Http\Controllers\Notifications\NotificationController;


// Auth
Route::post('/auth-signUp', [RegisterController::class, 'registerUser']);
// http://192.168.1.130:8000/api/register?userName=ahmad&userEmail=ahmad@gmail.com&userPassword=12345678&userNumber=+963946618431&userGender=male
Route::post('/auth-signIn', [LoginController::class, 'login']);
// http://192.168.1.130:8000/api/login?userEmail=abdullaziz.hallak.1992@gmail.com&userPassword=12345678
// Route::post('/products', [ProductController::class, 'store']);
Route::post('/auth-reset', [PasswordResetController::class, 'sendResetLink']);
//  Route::post('/submit-reset-password', [PasswordResetController::class, 'submitResetPassword']);

//  php artisan make:migration create_password_resets_table   

Route::post('/auth-google', [SingInWithGoogleController::class, 'googleSignIn']);


        Route::post('/verify-email', [VerificationController::class, 'sendVerificationEmail']);
       Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

// Route::get('/login', function () {
//     return response()->json(['message' => 'Login not implemented in API']);
// })->name('login');
  Route::post('/auth-signOut', [SignOutController::class, 'signOut']);



Route::post('/notifications/add', [NotificationController::class, 'add']);

 Route::middleware('auth:sanctum')->group(function () {
// Route::middleware('ManualSanctumAuth')->group(function () {

    // Insert
    Route::post('/insert-product', [ProductController::class, 'store']);
    // http://192.168.1.130:8000/api/products?sellerId=1&sellerNumber=963946523882&productCategory=1&productTitle=asd&productDetails=asdR&productImage=["compressed_Screenshot_20250407-182228_Telegram.jpg"]&productImageNumber=1&productPrice=1.0&productDateTime=2025-05-20 12:51:26.654295&productStatus=1&productStatusRatio=100&productAmount=1&biddingDays=1&productCurrency=1&productCountry=1&bidDetails=[{"bidder_id":"1","country_flag":"SY","bid_amount":1.0,"bid_time":"2025-05-20T12:51:26.654670"}]&productCity=1
      Route::post('/insert-notification', [InsertNotificationController::class, 'store']);
 
 
    // Select
    Route::get('/fetch-Products', [FetchProductsController::class, 'fetch']);
    // http://192.168.1.130:8000/api/products/fetch?search&productCategory=0&offset=0&userId=1&sortBy=recently added
    Route::get('/fetch-personalList', [FetchPersonalListController::class, 'fetchPersonalList']);
    // http://192.168.1.130:8000/api/fetch-personalList?personalList=["2"]
    Route::get('/fetch-biddingDetails', [FetchBiddingDetailsController::class, 'fetchBiddingDetails']);
    // http://192.168.1.130:8000/api/fetch-biddingDetails?product_id=1
    Route::get('/fetch-favorriteList', [FetchFavoriteListController::class, 'fetchFavoriteList']);
   

    // Update
    Route::put('/update-favoriteList', [FavoritesListController::class, 'updateFavoritesList']);
    // http://192.168.1.130:8000/api/update-favoriteList?UserId=1&FavoritesList=[]
    Route::put('/update-IncreaseLikes', [IncreaseLikesCountController::class, 'updateIncreaseLikesCount']);
    // http://192.168.1.130:8000/api/update-IncreaseLikes?productId=2&productLikes=3
    Route::put('/update-AddAbid', [AddAbidController::class, 'updateAddAbid']);
    // http: //192.168.1.130:8000/api/update-AddAbid?bidding_details=[{"bidder_id":"1","country_flag":"SY","bid_amount":2.0,"bid_time":"2025-05-21T00:50:08.602829"}]&product_id=1
    Route::put('/update-AddComment', [AddCommentController::class, 'updateAddComment']);
    // http://192.168.1.130:8000/api/update-AddComment?product_id=1&comments=[{"author":"abdullaziz.","content":"abc","timestamp":"","authorImage":"","authorGender":"","likedBy":[],"replies":[]}]
    Route::put('/update-Comment', [CommentController::class, 'updateComment']);
    // http://192.168.1.130:8000/api/update-AddComment?product_id=1&comments=[{"author":"abdullaziz.","content":"rti","timestamp":"2025-05-20T22:07:50.800874","authorImage":"","authorGender":"","likedBy":[],"replies":[]},{"author":"abdullaziz.","content":"abc","timestamp":"2025-05-20T22:02:20.570850","authorImage":"","authorGender":"","likedBy":[],"replies":[{"author":"abdullaziz.","content":"hgg"}]}]
    Route::put('/update-Rating', [AddRatingController::class, 'updateRating']);
    // http: //192.168.1.130:8000/api/update-Rating?evaluatedId=1&evaluatesId=2&evaluationValue=3.0
    Route::post('/update-Profile', [ProfileController::class, 'updateProfile']);

    // Delete
    Route::delete('/delete-Product', [DeleteProductController::class, 'deleteProduct']);
    
    

    // Services
    Route::post('/service-UploadImages', [UploadImagesController::class, 'uploadImages']);
    // http://192.168.1.130:8000/api/service-UploadImages
    Route::get('/app-version', [AppVersionController::class, 'getVersion']);
    Route::put('/update-FcmToken', [UpdateFcmTokenController::class, 'updateFcmToken']);
  
    // Notifications
    Route::post('/update-statusReadNotification', [UpdateNotificationStatus::class, 'markAsRead']);
     Route::get('/fetch-notifications', [FetchAllNotification::class, 'fetch']);


    // Admin
    Route::get('/admin-FetchUnapprovedProducts', [FetchProductApprovmentController::class, 'fetchUnapprovedProducts']);
    Route::put('/admin-ApproveProduct', [ProductApprovalController::class, 'approve']);
    Route::delete('/admin-deleteProduct', [AdminDeleteProductController::class, 'delete']);
 });






