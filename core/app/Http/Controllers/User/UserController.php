<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\Advertisement;
use App\Models\CommissionLog;
use App\Models\CryptoCurrency;
use App\Models\Form;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function home()
    {
        $user = auth()->user();
        if($user->kv != 1){
            $notify[] = ['error', 'Your KYC is not completed'];
            return to_route('user.kyc.form')->withNotify($notify);
        }


        $this->insertNewCryptoWallets();

        $user           = auth()->user();
        $pageTitle      = 'Dashboard';
        $wallets        = Wallet::where('user_id', $user->id)->with('crypto')->latest()->get();
        $est_balance        = Wallet::select(\DB::raw('SUM(balance) AS est_balance'))->where('user_id', $user->id)->get();

        $wallets_cards  = getTopUserWallet($user->id);

        $totalAdd       = Advertisement::where('user_id', $user->id)->count();
        $advertisements = Advertisement::where('user_id', auth()->user()->id)->latest()->with(['crypto', 'fiatGateway', 'fiat', 'user.wallets'])->latest()->limit(10)->get();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'user', 'wallets','est_balance','wallets_cards', 'totalAdd', 'advertisements'));
    }

    public function insertNewCryptoWallets()
    {
        $walletId  = Wallet::where('user_id', auth()->id())->pluck('crypto_currency_id');
        $cryptos   = CryptoCurrency::latest()->whereNotIn('id', $walletId)->pluck('id');
        $data      = [];

        foreach ($cryptos as $id) {
            $wallet['crypto_currency_id'] = $id;
            $wallet['user_id']            = auth()->id();
            $wallet['balance']            = 0;
            $data[]                       = $wallet;
        }

        if (!empty($data)) {
            Wallet::insert($data);
        }
    }

    public function depositHistory()
    {
        $pageTitle = 'Deposit History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->where('user_id', auth()->id());

        if (request()->crypto) {
            $deposits = $deposits->where('crypto_currency_id', request()->crypto);
        }

        $deposits = $deposits->with(['crypto'])->orderBy('id', 'desc')->paginate(getPaginate());
        $cryptos = CryptoCurrency::orderBy('code')->get();

        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits', 'cryptos'));
    }


    public function referralCommissions()
    {
        $pageTitle = 'Referral Commissions';
        $logs = CommissionLog::where('to_id', auth()->user()->id)->with('bywho', 'crypto')->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'user.referral.commission', compact('pageTitle', 'logs'));
    }

    public function myRef()
    {
        $pageTitle = 'My Referred Users';
        $maxLevel = Referral::max('level');
        $user = auth()->user();

        return view($this->activeTemplate . 'user.referral.users', compact('pageTitle', 'maxLevel', 'user'));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }


    public function transactionIndex(Request $request)
    {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::distinct('remark')->whereNotNull('remark')->get('remark');
        $transactions = Transaction::where('user_id', Auth::id())->where('crypto_currency_id', '!=', null);

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type', $request->type);
        }

        if ($request->crypto) {
            $transactions = $transactions->where('crypto_currency_id', $request->crypto);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->with(['crypto'])->orderBy('id', 'desc')->paginate(getPaginate());
        //dd($transactions);
        $cryptos = CryptoCurrency::latest()->get();

        return view($this->activeTemplate . 'user.transaction', compact('pageTitle', 'transactions', 'cryptos', 'remarks'));
    }


    public function kycForm()
    {

        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }

        $submittedRecords=[];
        $unverifiedRecords=[];
        if(auth()->user()->kv == 3){
            $submittedRecords =['upload_cnic/passport/driving_lisence'];
        }
        if(auth()->user()->kv == 4){
            $submittedRecords =['upload_cnic/passport/driving_lisence','proof_of_address'];
        }
        if(auth()->user()->kv == 5){
            $submittedRecords =['upload_cnic/passport/driving_lisence','proof_of_address','upload_selfie'];
        }
        
        if(auth()->user()->kv == 2){
            $unverifiedRecords =['upload_cnic/passport/driving_lisence'];
        }
        if(auth()->user()->kv == 2){
            $unverifiedRecords =['upload_cnic/passport/driving_lisence','proof_of_address'];
        }
        if(auth()->user()->kv == 2){
            $unverifiedRecords =['upload_cnic/passport/driving_lisence','proof_of_address','upload_selfie'];
        }

        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        $formData=[];

        foreach ($form->form_data as $item) {
            $item->status = '';
            if(!empty($submittedRecords)) {
                if (in_array($item->label, $submittedRecords)) {
                    $item->status = 'Uploaded';
                } else {
                    $item->status = '';
                }
            }
            if(!empty($unverifiedRecords)) {
                if (in_array($item->label, $unverifiedRecords)) {
                    $item->status = 'Pending';
                } else {
                    $item->status = '';
                }
            }
            $formData[]=$item;
        }
        $user = Auth::user();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form','formData','user'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        // dd($user->kyc_data);
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
//        $form = Form::where('act', 'kyc')->first();
//        $formData = $form->form_data;
//        $formProcessor = new FormProcessor();
//        $validationRule = $formProcessor->valueValidation($formData);
//        $request->validate($validationRule);
//        $userData = $formProcessor->processFormData($request, $formData);
        $user = auth()->user();
        //$user->kyc_data = $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function kycSubmitByType(Request $request)
    {
        // dd($request);
        $form = Form::where('act', 'kyc')->first();
        $formData = $form->form_data;
        $data = null;
        foreach($formData as $index=>$item){
            if($index == $request->type){
                $data = $item;
            }
        }
        if(!$data){
            return ['status'=>409,'message'=>'Error in Uploading'];
        }
        if ($request->type == 'upload_cnic/passport/driving_lisence') {
            $value = $request->file;
            // $value = File::get($value);
            $value=$request->file('file');
            $directory = date("Y") . "/" . date("m") . "/" . date("d");
            $path = getFilePath('verify') . '/' . $directory;
            $value = $directory . '/' . fileUploader($value, $path);

            $requestForm[] = [
                'name' => $data->name,
                'type' => $data->type,
                'value' => $value,
            ];
            $user = auth()->user();
            $user->kyc_data = $requestForm;
            $user->kv = 3;
            $user->save();
            // $fileContents = file_get_contents($request->file);
            // dd($fileContents);
            return ['status'=>200,'message'=>$data->name.' uploaded successfully'];

        } elseif ($request->type == 'proof_of_address') {
            $user = auth()->user();

            if($user->kv != 3){
                return ['status'=>409,'message'=>'Error in Uploading'];
            }
            $old_kyc = $user->kyc_data;

            $value = $request->file;
            // $value = File::get($value);
            $value=$request->file('file');
            $directory = date("Y") . "/" . date("m") . "/" . date("d");
            $path = getFilePath('verify') . '/' . $directory;
            $value = $directory . '/' . fileUploader($value, $path);

            $old_kyc[] = [
                'name' => $data->name,
                'type' => $data->type,
                'value' => $value,
            ];
            $user->kyc_data = $old_kyc;
            $user->kv = 4;
            $user->save();
            // $fileContents = file_get_contents($request->file);
            // dd($fileContents);
            return ['status'=>200,'message'=>$data->name.' uploaded successfully'];
        }elseif($request->type == 'upload_selfie'){
            $user = auth()->user();

            $old_kyc = $user->kyc_data;

            if($user->kv != 4){
                return ['status'=>409,'message'=>'Error in Uploading'];
            }

            $value = $request->file;
            // $value = File::get($value);
            $value=$request->file('file');
            $directory = date("Y") . "/" . date("m") . "/" . date("d");
            $path = getFilePath('verify') . '/' . $directory;
            $value = $directory . '/' . fileUploader($value, $path);

            $old_kyc[] = [
                'name' => $data->name,
                'type' => $data->type,
                'value' => $value,
            ];
            $user->kyc_data = $old_kyc;
            $user->kv = 5;
            $user->save();
            return ['status'=>200,'message'=>$data->name.' uploaded successfully'];

        }else{
            dd($request);
        }
        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
        ];
        $user->reg_step = 1;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }
}
