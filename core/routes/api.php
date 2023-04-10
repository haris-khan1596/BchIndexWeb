<?php

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
// Route::middleware('auth:sanctum')->group(function () {
//     Route::namespace('Spot\Api')->prefix('spot')->name('spot.')->group(function () {
//         Route::get('pairs', 'SpotOrdersController@pairs')->name('pairs');
//         Route::get('create/{pair}', 'SpotOrdersController@create')->name('create');
//         Route::post('limitorder', 'SpotOrdersController@spotLimitOrder')->name('limitorder');
//         Route::post('stoplimitorder', 'SpotOrdersController@SpotStopLimitOrder');
//         Route::post('marketorder', 'SpotOrdersController@SpotMarketOrder');
//         Route::get('testfunction', function(){
//             return response()->json(['success' => 'testfunction']);
//             });
//         });

//     Route::namespace('Spot\Api')->prefix('transactions')->name('transactions.')->group(function () {
//         Route::get('p2p_to_spot', 'TrasactionController@transfer_p2p')->name('p2p_to_spot');
//         Route::post('p2p_to_spot_post', 'TrasactionController@transfer_p2p_post')->name('p2p_to_spot_post');
//         Route::get('spot_to_p2p', 'TrasactionController@transfer_spot')->name('spot_to_p2p');
//         Route::post('spot_to_p2p_post', 'TrasactionController@transfer_spot_post')->name('spot_to_p2p_post');
//     });

// });
Route::get('p2p_to_spot', 'Spot\Api\TrasactionController@transfer_spot')->name('p2p_to_spot');
Route::middleware('auth:sanctum')->group(function () {
    Route::namespace('Spot\Api')->prefix('spot')->name('spot.')->group(function () {
        Route::get('pairs', 'SpotOrdersController@pairs')->name('pairs');
        Route::get('create/{pair}', 'SpotOrdersController@create')->name('create');
        Route::post('limitorder', 'SpotOrdersController@spotLimitOrder')->name('limitorder');
        Route::post('stoplimitorder', 'SpotOrdersController@SpotStopLimitOrder');
        Route::post('marketorder', 'SpotOrdersController@SpotMarketOrder');
        Route::get('cancel/order/{orderId}/{type}','SpotOrdersController@cancel');
        
    });
    
    Route::namespace('Spot\Api')->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('p2p_to_spot', 'TrasactionController@transfer_p2p')->name('p2p_to_spot');
        Route::post('p2p_to_spot_post', 'TrasactionController@transfer_p2p_post')->name('p2p_to_spot_post');
        Route::get('spot_to_p2p', 'TrasactionController@transfer_spot')->name('spot_to_p2p');
        Route::post('spot_to_p2p_post', 'TrasactionController@transfer_spot_post')->name('spot_to_p2p_post');
    });
    Route::namespace('Spot\Api')->prefix('wallets')->group(function(){
        Route::get('get/{id}','Wallet@index');
    });
});
Route::namespace('Spot\Api')->prefix('wallets')->group(function(){
        Route::get('get/{id}','Wallet@index');
    });
Route::get('testfunction', function(){
    return response()->json(['success' => 'testfunction']);
    });



// Route::post('limitorder', 'SpotOrdersController@spotLimitOrder');

Route::namespace('Api')->name('api.')->group(function () {
    Route::get('general-setting', function () {
        $general = gs();
        $notify[] = 'General setting data';
        return response()->json([
            'remark' => 'general_setting',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'general_setting' => $general,
            ],
        ]);
    });
    
    // BasicController
    Route::controller('BasicController')->group(function () {
        Route::get('cryptos', 'cryptos');
        Route::get('fiat-gateways', 'fiatGateways');
        Route::get('countries', 'countries');
        Route::get('ad-filter', 'adFilter');
        Route::get('polices', 'polices');
    });


    Route::namespace('Auth')->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('register', 'RegisterController@register');

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
            Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
            Route::post('password/reset', 'reset')->name('password.update');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        //authorization
        Route::controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization')->name('authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
            Route::post('verify-email', 'emailVerification')->name('verify.email');
            Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
            Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
        });

        Route::middleware(['check.status'])->group(function () {
            Route::post('user-data-submit', 'UserController@userDataSubmit')->name('data.submit');

            Route::middleware('registration.complete')->group(function () {

                Route::post('device-token', 'PushNotificationController@saveDeviceToken');

                Route::controller('UserController')->group(function () {

                    Route::get('user-info', 'userData');

                    //User App Dashboard
                    Route::get('dashboard', 'UserController@dashboard');

                    //User App Notification
                    Route::get('notifications', 'UserController@notifications');
                    Route::get('notification-mark-read/{id}', 'UserController@notificationMarkRead');

                    //KYC
                    Route::get('kyc-form', 'kycForm')->name('kyc.form');
                    Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                    //Report
                    Route::get('deposit-history', 'depositHistory')->name('deposit.history');
                    Route::get('transactions', 'transactions')->name('transactions');

                    //Wallets
                    Route::get('wallets', 'wallets');
                    Route::get('single/wallet/{id}', 'singleWallet');

                    //Profile setting
                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    //Public Profile
                    Route::get('public-profile/{username}', 'publicProfile');

                    //Referral
                    Route::get('referral/commissions', 'referralCommissions');
                    Route::get('refereed/users', 'myRef');
                });

                Route::controller('AdvertisementController')->prefix('advertisement')->group(function () {
                    //Ad search
                    Route::get('search', 'search');

                    Route::middleware('kyc')->group(function () {
                        Route::get('index', 'index');
                        Route::get('new', 'new');
                        Route::get('edit/{id}', 'edit');
                        Route::post('store/{id?}', 'store');
                        Route::post('status-update/{id}', 'statusUpdate');
                        Route::get('reviews/{id}', 'reviews')->name('reviews');
                    });
                });

                Route::controller('TradeController')->middleware('kyc')->prefix('trade')->group(function () {
                    Route::get('index', 'index');
                    Route::get('details/{uid}', 'details')->name('user.trade.details');
                    Route::get('new/{id}', 'create');
                    Route::post('store/{id}', 'store');

                    // Trade Operation
                    Route::post('cancel', 'cancel');
                    Route::post('paid', 'paid');
                    Route::post('dispute', 'dispute');
                    Route::post('release', 'release');
                });

                // Trade Chat
                Route::controller('ChatController')->middleware('kyc')->prefix('trade-chat')->group(function () {
                    Route::post('store/{id}', 'store');
                    Route::get('download/{tradeId}/{id}', 'download');
                });

                // Trade Review
                Route::controller('ReviewController')->middleware('kyc')->prefix('trade-review')->group(function () {
                    Route::get('check/{uid}', 'check');
                    Route::post('store/{uid?}', 'store');
                });

                // Withdraw
                Route::controller('WithdrawController')->group(function () {
                    Route::middleware('kyc')->group(function () {
                        Route::get('withdraw-request/{id}', 'withdrawMoney');
                        Route::get('past-withdrawals/{id}', 'previousWithdrawals');
                        Route::post('withdraw-request/confirm', 'store');
                    });

                    Route::get('withdraw/history', 'log')->name('withdraw.history');
                });

                // Payment
                Route::controller('PaymentController')->group(function () {
                    Route::get('wallet-generate/{id}', 'walletGenerate');
                });
            });
        });
        
        Route::get('logout', 'Auth\LoginController@logout');
    });
});
