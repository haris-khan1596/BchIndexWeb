<?php


use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});
Route::get('/exchange/{pair}', 'Spot\OrderBookController@guestcreate');
Route::post('/exchange','Spot\OrderBookController@changeCoinPair')->name('coinPairForm');
Route::get('/exchange', 'Spot\OrderBookController@exchange')->name('user.exchange');
//Crons
//Route::get('cron/fiat-currency', 'CronController@fiatRate');
//Route::get('cron/crypto-currency', 'CronController@cryptoRate');
Route::get('user/exchange/{pair}/GetData/{time}', 'Spot\OrderBookController@GetData')->name('GetData');//ajax
Route::get('cron/fiat-currency', 'CronController@fiatRate')->name('cron.fiat.rate');
Route::get('cron/crypto-currency', 'CronController@cryptoRate')->name('cron.crypto.rate');
Route::get('cron/FakeChart', 'CronController@fakeChart')->name('cron.fake.chart');
Route::get('cron/delFakeChart', 'CronController@DelFakeChart')->name('cron.delete.fake.chart');

// Coin Payments
Route::controller('Gateway\PaymentController')->prefix('ipn')->name('ipn.')->group(function () {
    Route::post('crypto', 'cryptoIpn')->name('crypto');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->group(function () {
    Route::get('/', 'supportTicket')->name('ticket');
    Route::get('/new', 'openSupportTicket')->name('ticket.open');
    Route::post('/create', 'storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'replyTicket')->name('ticket.reply');
    Route::post('/close/{ticket}', 'closeTicket')->name('ticket.close');
    Route::get('/download/{ticket}', 'ticketDownload')->name('ticket.download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    // Public profile
    Route::get('/profile/{username}', 'SiteController@publicProfile')->name('public.profile');

    // Search Advertisement
    Route::get('/search-ad', 'searchAdvertisements')->name('advertisement.search');

    Route::get('/currency-wise-ads/{id}', 'currencyWiseAd')->name('advertisement.currency');

    //Route::get('/contact', 'contact')->name('contact');
    //Route::post('/contact', 'contactSubmit');

    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');



    // Buy Sell Operation
    Route::get('/{buysell}/{crypto}/{countryCode}/{fiatgt?}/{fiat?}/{amount?}', 'buySell')->name('buy.sell');
});
