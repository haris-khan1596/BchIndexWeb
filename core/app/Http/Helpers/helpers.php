<?php

use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\CommissionLog;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\CryptoCurrency;
use App\Notify\Notify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
function systemDetails()
{
    $system['name'] = 'localcoins';
    $system['version'] = '2.0';
    $system['build_version'] = '4.2.9';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $analytics = Extension::where('act', $key)->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = null, $avatar = false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    if ($avatar) {
        return asset('assets/images/avatar.png');
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $clickValue = null)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
#$notify->userColumn = $user;
    $notify->clickValue = $clickValue;
    $notify->send();

}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            if (request()->route(@$param[0]) == @$param[1]) return $class;
            else return;
        }

        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}

function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}

function getRate($data)
{
    $type       = $data->type;
    $cryptoRate = $data->crypto->rate;
    $fiatRate   = $data->fiat->rate;
    $margin     = $data->margin;
    $fixed      = $data->fixed_price ?? 0;
    $amount     = $cryptoRate * $fiatRate;

    if ($fixed > 0) {
        $rate = getAmount($fixed,2);
    } else {
        $percentValue = $amount * $margin / 100;
        $rate = $type == 1 ? $amount - $percentValue : $amount + $percentValue;
        $rate = round($rate, 2);
    }
    
    return $rate;
}
function getTopUserWallet($user_id){
    return Wallet::where('user_id', $user_id)
        ->whereIN('crypto_currency_id', [1,2,3,4,5,6,7])
        ->with('crypto')
        ->orderBy('crypto_currency_id','ASC')
        ->get();
}
function getTopWallet(){
    return CryptoCurrency::active()->whereIN('id', [1,2,3,4,5,6,7])->orderBy('id','ASC')->get();
}
function getRateAttributeForApp($data)
{
    return ($data->fiat->code . '/' . $data->crypto->code);
}

function levelCommission($user, $amount, $cryptoId, $trx, $commissionType = '')
{
    $tempUser = $user;
    $i = 1;
    $level = Referral::where('commission_type', $commissionType)->count();

    while ($i <= $level) {
        $referer = $tempUser->refBy;

        if (!$referer) {
            break;
        }

        $userWallet = Wallet::where('user_id', $referer->id)->where('crypto_currency_id', $cryptoId)->first();
        $commission = Referral::where('commission_type', $commissionType)->where('level', $i)->first();

        if (!$userWallet || !$commission) {
            break;
        }

        $commissionAmount = ($amount * $commission->percent) / 100;

        $userWallet->balance += $commissionAmount;
        $userWallet->save();

        $transactions[] = [
            'user_id'            => $referer->id,
            'crypto_currency_id' => $cryptoId,
            'amount'             => getAmount($commissionAmount, 8),
            'post_balance'       => $userWallet->balance,
            'charge'             => 0,
            'trx_type'           => '+',
            'details'            => 'Level ' . $i . ' referral commission From ' . $user->username,
            'remark'             => 'referral',
            'trx'                => $trx,
            'created_at'         => now()
        ];

        $commissionLog[] = [
            'to_id'             => $referer->id,
            'from_id'           => $user->id,
            'crypto_currency_id'         => $cryptoId,
            'level'             => $i,
            'post_balance'      => $userWallet->balance,
            'commission_amount' => $commissionAmount,
            'trx_amo'           => $amount,
            'title'             => 'Level ' . $i . ' referral commission from ' . $user->username . ' for ' . $userWallet->crypto->code . ' Wallet',
            'type'              => $commissionType,
            'percent'           => $commission->percent,
            'trx'               => $trx,
            'created_at'        => now()
        ];

        $tempUser = $referer;
        $i++;
    }

    if (isset($transactions)) {
        Transaction::insert($transactions);
    }

    if (isset($commissionLog)) {
        CommissionLog::insert($commissionLog);
    }
}

function getPublishStatus($ad, $maxLimit)
{
    if (!$ad->crypto->status || !$ad->fiatGateway->status || !$ad->fiat->status || !$ad->status) {
        return 0;
    }

    if ($ad->type == 1) {
        return 1;
    }

    if ($maxLimit >= $ad->min) {
        return 1;
    }

    return 0;
}

function getAdUnpublishReason($ad, $maxLimit, $admin = false)
{
    $message = [];

    if (!$ad->status) {
        $message['status'] = "This ad status is currently disabled";
    }

    if (!$ad->crypto->status) {
        $message['crypto'] = $ad->crypto->code . " crypto currency is currently disabled";
    }

    if (!$ad->fiat->status) {
        $message['fiat'] = $ad->fiat->code . " currency is currently disabled";
    }

    if (!$ad->fiatGateway->status) {
        $message['fiat_gateway'] = $ad->fiatGateway->name . " currency is currently disabled";
    }

    if ($maxLimit < $ad->min) {
        $message['limit'] = "You do not have the exact amount of maximum limit on your wallet";

        if ($admin) {
            $message['limit'] = "Advertiser does not have the exact amount of maximum limit on his wallet";
        }
    }

    return $message;
}


function getMaxLimit($wallets, $ad)
{
    $maxLimit = $ad->max;

    if ($ad->type == 2) {
        $userWallet = $wallets->where('crypto_currency_id', $ad->crypto_currency_id)->first();
        $rate       = getRate($ad);
        $balance = isset($userWallet->balance)?$userWallet->balance:0;
        $userMax    = (float)$balance * (float)$rate;
        $maxLimit   = $ad->max < $userMax ? $ad->max : $userMax;
    }

    return $maxLimit;
}

function avgTradeSpeed($ad)
{
    if ($ad->completed_trade) {
        return round($ad->total_min / $ad->completed_trade) . ' ' . trans('Minutes');
    }
    return trans('No trades yet');
}

function getReferees($user, $maxLevel, $data = [], $depth = 1,  $layer = 0)
{
    if ($user->allReferrals->count() > 0 && $maxLevel > 0) {
        foreach ($user->allReferrals as $under) {
            $i = 0;
            if ($i == 0) {
                $layer++;
            }
            $i++;

            $userData['id'] = $under->id;
            $userData['username'] = $under->username;
            $userData['image'] = getImage(getFilePath('userProfile') . '/' . @$under->image, null, true);
            $userData['level'] = $depth;
            $data[] = $userData;
            if ($under->allReferrals->count() > 0 && $layer < $maxLevel) {
                $data = getReferees($under, $maxLevel, $data, $depth + 1, $layer);
            }
        }
    }
    return $data;
}
function chargeCalculator($amount,$percent,$fixed){
    $percentCharge = $amount * $percent/100;
    return $fixed + $percentCharge;
}
