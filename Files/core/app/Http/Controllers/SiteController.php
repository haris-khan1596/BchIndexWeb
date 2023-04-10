<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Advertisement;
use App\Models\CryptoCurrency;
use App\Models\FiatCurrency;
use App\Models\FiatGateway;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller
{
    public function index()
    {
        if (request()->reference) {
            session()->put('reference', request()->reference);
        }

        $pageTitle    = 'Home';
        $sections     = Page::where('tempname', $this->activeTemplate)->where('slug', '/')->first();
        $cryptos      = CryptoCurrency::active()->orderBy('name')->get();
        $fiatGateways = FiatGateway::getGateways();
        $info         = json_decode(json_encode(getIpInfo()), true);
        $mobileCode   = @implode(',', $info['code']);
        $countries    = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections', 'cryptos', 'fiatGateways', 'countries', 'mobileCode'));
    }

    public function publicProfile($username)
    {
        $request        = request();
        $user           = User::where('username', $username)->firstOrFail();
        $pageTitle      = "Profile of $user->username";

        $advertisements = $user->advertisements()->active()
            ->whereHas('fiat', function ($q) {
                $q->active();
            })->whereHas('crypto', function ($q) {
                $q->active();
            })
            ->whereHas('fiatGateway',  function ($q) {
                $q->active();
            });

        $buyAds         = clone $advertisements;
        $sellAds        = clone $advertisements;

        $latestBuyAds   = $buyAds->where('type', 2);
        $latestSellAds  = $sellAds->where('type', 1);

        if ($request->crypto) {
            $latestBuyAds  = $latestBuyAds->where('crypto_currency_id', $request->crypto);
            $latestSellAds = $latestSellAds->where('crypto_currency_id', $request->crypto);
        }

        if ($request->fiat_gateway) {
            $latestBuyAds  = $latestBuyAds->where('fiat_gateway_id', $request->fiat_gateway);
            $latestSellAds = $latestSellAds->where('fiat_gateway_id', $request->fiat_gateway);
        }

        if ($request->amount) {
            $latestBuyAds  = $latestBuyAds->where('min', '<=', $request->amount)->where('max', '>=', $request->amount);
            $latestSellAds = $latestSellAds->where('min', '<=', $request->amount)->where('max', '>=', $request->amount);
        }

        $latestBuyAds      = $latestBuyAds->latest()->with(['crypto', 'user.wallets', 'fiatGateway', 'fiat'])->get();
        $latestSellAds     = $latestSellAds->latest()->with(['crypto', 'user.wallets', 'fiatGateway', 'fiat'])->get();
        $cryptos           = CryptoCurrency::active()->orderBy('name')->get();
        $fiatGateways      = FiatGateway::active()->orderBy('name')->get();
        $positiveFeedbacks = $user->positiveFeedBacks->count();
        $negativeFeedBacks = $user->negativeFeedBacks->count();

        return view($this->activeTemplate . 'public_profile', compact('user', 'pageTitle', 'latestBuyAds', 'latestSellAds', 'cryptos', 'fiatGateways', 'positiveFeedbacks', 'negativeFeedBacks'));
    }

    public function searchAdvertisements(Request $request)
    {
        return redirect()->route('buy.sell', [$request->type, $request->crypto_code, $request->country_code, $request->fiat_gateway_slug, $request->fiat_code, $request->amount]);
    }

    public function buySell($type, $crypto, $countryCode, $fiatGateway = null, $fiat = null, $amount = null)
    {
        if (!in_array($type, ['sell', 'buy'])) {
            abort(404);
        }

        $cryptoCheck    = CryptoCurrency::where('code', $crypto)->active()->firstOrFail();
        $typeValue      = ($type == 'sell') ? 1 : 2;

        $advertisements = Advertisement::active()->where('type', $typeValue)->where('crypto_currency_id', $cryptoCheck->id);

        if ($fiatGateway) {
            $fiatGatewayCheck = FiatGateway::where('slug', $fiatGateway)->active()->firstOrFail();
            $advertisements   = $advertisements->where('fiat_gateway_id', $fiatGatewayCheck->id);
        }

        if ($fiat) {
            $fiatCheck        = FiatCurrency::where('code', $fiat)->active()->firstOrFail();
            $advertisements   = $advertisements->where('fiat_currency_id', $fiatCheck->id);
        }

        if ($countryCode != 'all') {
            $advertisements = $advertisements->whereHas('user', function ($q) use ($countryCode) {
                $q->active()->where('country_code', $countryCode);
            });
        } else {
            $advertisements = $advertisements->whereHas('user', function ($q) {
                $q->active();
            });
        }

        if ($amount) {
            $advertisements = $advertisements->where('min', '<=', $amount)->where('max', '>=', $amount);
        }

        $advertisements = $advertisements->with(['user', 'fiatGateway', 'crypto', 'fiat', 'user.wallets'])->paginate(getPaginate());

        $cryptos        = CryptoCurrency::active()->orderBy('name')->get();
        $countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $fiatGateways   = FiatGateway::getGateways();
        $pageTitle      = ucfirst($type) . ' ' . $cryptoCheck->name;

        return view($this->activeTemplate . 'advertisement.all', compact('pageTitle', 'advertisements', 'type', 'crypto', 'cryptos', 'fiatGateways', 'fiat', 'amount', 'fiatGateway', 'countries', 'countryCode'));
    }

    public function currencyWiseAd($id)
    {
        $type = request()->type;
        $scope = $type == 'buy' ? 'sell' : 'buy';

        $ads = Advertisement::active()->$scope()->whereHas('crypto', function ($q) use ($id) {
            $q->where('id', $id)->active();
        })->whereHas('fiatGateway', function ($q) {
            $q->active();
        })->whereHas('fiat', function ($q) {
            $q->active();
        })->whereHas('user', function ($q) {
            $q->active();
        })->latest()->with(['crypto', 'user', 'fiatGateway', 'fiat'])->limit(6)->get();
        
        
        

        $view = view($this->activeTemplate . "advertisement.$type", compact('ads'))->render();

        return response()->json([
            'html' => $view
        ]);
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $sections = Page::where('tempname', $this->activeTemplate)->where('slug', 'contact')->first();
        return view($this->activeTemplate . 'contact', compact('pageTitle', 'sections'));
    }

    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();
        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;

        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug, $id)
    {
        $policyDetails = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policyDetails->data_values->title;
        return view($this->activeTemplate . 'policy', compact('policyDetails', 'pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blogDetails($slug, $id)
    {
        $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle = $blog->data_values->title;
        return view($this->activeTemplate . 'blog_details', compact('blog', 'pageTitle'));
    }

    public function cookieAccept()
    {
        $general = gs();
        Cookie::queue('gdpr_cookie', $general->site_name, 43200);
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        $general = gs();
        if ($general->maintenance_mode == 0) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }
}
