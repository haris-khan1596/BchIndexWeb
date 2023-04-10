<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register/{reference?}', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');

                //2FA
                Route::get('two-factor', 'show2faForm')->name('twofactor');
                Route::post('two-factor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('two-factor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');
                Route::post('kyc-submit-type', 'kycSubmitByType')->name('kyc.submit.type');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');

                // Transactions
                Route::get('transactions', 'transactionIndex')->name('transaction.index');

                // Referral
                Route::get('referral/commissions', 'referralCommissions')->name('referral.commissions.trade');
                Route::get('referred/users', 'myRef')->name('referral.users');

                // Chat file download
                Route::get('attachment-download/{file_hash}', 'attachmentDownload')->name('attachment.download');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            Route::middleware('kyc')->group(function () {

                // Advertisement
                Route::controller('AdvertisementController')->prefix('advertisement')->name('advertisement.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('add-new', 'create')->name('new');
                    Route::post('store/{id?}', 'store')->name('store');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::get('delete/{id}', 'delete')->name('delete');
                    Route::post('status/{id}', 'updateStatus')->name('status');
                    Route::get('reviews/{id}', 'reviews')->name('reviews');
                });

                // Trade Request
                Route::controller('TradeController')->name('trade.request.')->group(function () {

                    Route::prefix('trades')->group(function () {
                        Route::get('running', 'running')->name('running');
                        Route::get('completed', 'completed')->name('completed');
                        Route::get('details/{id}', 'details')->name('details');
                        Route::get('ajax_chat/{id}', 'ajax_chat')->name('ajax_chat');
                        Route::get('get_ajax_actions/{id}', 'get_ajax_actions')->name('get_ajax_actions');
                        // Trade Request Operation
                        Route::post('cancel/{id}', 'cancel')->name('cancel');
                        Route::post('paid/{id}', 'paid')->name('paid');
                        Route::post('dispute/{id}', 'dispute')->name('dispute');
                        Route::post('release/{id}', 'release')->name('release');
                    });

                    Route::get('new-trade-request/{id}', 'newTrade')->name('new');
                    Route::post('send-trade-request/{id}', 'sendTradeRequest')->name('store');
                });

                // Trade Chat
                Route::controller('ChatController')->prefix('trade-chat')->name('chat.')->group(function () {
                    Route::post('store/{id}', 'store')->name('store');
                    Route::get('download/{tradeId}/{id}', 'download')->name('download');
                });

                // Trade Review
                Route::controller('ReviewController')->prefix('trade-review')->name('review.')->group(function () {
                    Route::post('store/{uid}', 'store')->name('store');
                });
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::get('history', 'log')->name('.history');
                Route::middleware('kyc')->group(function () {
                    Route::get('/{crypto}', 'withdrawMoney');
                    Route::post('/', 'store')->name('.store');
                });
            });
        });

        // Wallets
        Route::middleware(['registration.complete', 'kyc'])->controller('Gateway\PaymentController')->group(function () {
            Route::get('/wallets', 'Gateway\PaymentController@wallets')->name('wallets');
            Route::get('/single-wallet/{id}/{code}', 'Gateway\PaymentController@singleWallet')->name('wallets.single');
            Route::get('/wallets/generate/{crypto}', 'Gateway\PaymentController@walletGenerate')->name('wallets.generate');
        });

        // Spot Wallet
        Route::middleware(['registration.complete', 'kyc'])->controller('Gateway\PaymentController')->group(function () {
            Route::get('/spot-wallet', 'P2pSpotTransfer@spotWallet')->name('spot-wallet');
        });

        // Exchange Spot
        Route::middleware(['registration.complete', 'kyc'])->controller('Gateway\PaymentController')->group(function () {
            Route::get('/exchange/{pair}', 'Spot\OrderBookController@create');
            //Route::post('/exchange/{pair}','Spot\OrderBookController@store');
            Route::post('/exchange/{pair}/limit','Spot\OrderController@SpotLimitOrder');
            Route::post('/exchange/{pair}/market','Spot\OrderController@SpotMarketOrder');
            Route::post('/exchange/{pair}/stoplimit','Spot\OrderController@SpotStopLimitOrder');

            Route::post('/exchange/{pair}/cancel','Spot\OrderController@SpotCancelOrder');

            Route::get('/exchange/{pair}/orderBookUpdate/{time}', 'Spot\OrderBookController@orderBookUpdate')->name('orderBookUpdate'); //ajax

            Route::post('/exchange/orders/cancel/{orderId}/{type}', 'Spot\OrderController@cancel')->name('orders.cancel');

            // Order BookTableUpdate
            Route::get('/exchange/{pair}/orderBookUpdate', 'Spot\OrderBookController@orderBookUpdate')->name('orderBookUpdate');
        });

         // Transfer from Spot To P2P
        Route::middleware(['registration.complete', 'kyc'])->controller('Gateway\PaymentController')->group(function () {
            Route::get('/{Cid}/{Uid}/transfer_p2s', 'Gateway\PaymentController@transfer_p2s')->name('transfer_p2s');
            Route::post('/{Cid}/{Uid}/transfer_p2s', 'Gateway\PaymentController@transfer_p2s_post');

        });

        // Transfer from P2P To Spot
        Route::middleware(['registration.complete', 'kyc'])->controller('Gateway\PaymentController')->group(function () {
            Route::get('/{Wid}/transfer_p2p', 'Gateway\PaymentController@transfer_p2p')->name('transfer_p2p');
            Route::post('/transfer_p2p', 'Gateway\PaymentController@transfer_p2p_post');
        });



        // Transfer
        Route::middleware(['registration.complete', 'kyc'])->namespace('User')->controller('UserTransferMoneyController')->group(function(){
            Route::get('/transfer/money/{id?}', 'transfer')->name('transfer');
            Route::get('/user/exist', 'userExist')->name('transfer-user-check');
            Route::get('/user/receipt/{id}/{ajax?}', 'p2p_receipt')->name('transfer-user-receipt');
            Route::post('/transfer/money', 'transferMoney')->name('transfer-money-post');
        });

        //Exchange money
        Route::middleware(['registration.complete', 'kyc'])->namespace('User')->controller('MoneyExchangeController')->group(function(){
            Route::get('/exchange/money', 'exchangeForm')->name('exchange.money');
            Route::post('/exchange/money', 'exchangeConfirm');
        });

    });
});
