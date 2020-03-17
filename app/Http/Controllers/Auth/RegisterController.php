<?php

namespace App\Http\Controllers\Auth;

use App\Events\SignedUp;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUserProvider;
use Hostville\Dorcas\Sdk;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
        $this->data['page']['title'] = 'Create an Account';
    }

    /**
     * @inheritdoc
     */
    public function showRegistrationForm(Request $request)
    {
        $this->data['header']['title'] = 'Create an Account';

        $plans = $request->only(['starter', 'classic', 'premium']);
        # we decide the plan from the URL
        if (in_array('premium', $plans)) {
            $this->data['plan'] = $plan = 'premium';
        } elseif (in_array('classic', $plans)) {
            $this->data['plan'] = $plan = 'classic';
            $plan = empty($plans['classic']) ? 'classic' : 'classic_yearly';
        } else {
            $this->data['plan'] = $plan = 'starter';
        }
        $this->data['plan_type'] = !empty($plans[$plan]) && $plans[$plan] === 'yearly' ? 'yearly' : 'monthly';
        # set the plan type
        return view('auth.register-v2', $this->data);
        //return views('auth.register', $this->data);
    }
    
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showOldProfessionalRegistrationForm()
    {
        return redirect()->route('professional.register');
    }
    
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showProfessionalRegistrationForm(Request $request)
    {
        $this->data['plan'] = 'starter';
        $this->data['plan_type'] = 'monthly';
        return view('auth.professional-register', $this->data);
        
    }
    
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showVendorRegistrationForm(Request $request)
    {
        $this->data['plan'] = 'starter';
        $this->data['plan_type'] = 'monthly';
        return view('auth.vendor-register', $this->data);
        
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'email' => 'required|string|email|max:80|unique:users',
            'password' => 'required|string|min:6',
            'company' => 'nullable|max:100',
            'phone' => 'required|numeric',
        ], [
            'phone.numeric' => 'The phone number should only contain numbers'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request, $request->all())));

        $sdk = app(Sdk::class);
        $provider = new DorcasUserProvider($sdk);
        # get the provider
        $dorcasUser = $provider->retrieveByCredentials(['email' => $user->email, 'password' => $request->password]);
        # get the authenticated user

        $this->guard()->login($dorcasUser);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param Request $request
     * @param array   $data
     *
     * @return User
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function create(Request $request, array $data)
    {
        $sdk = app(Sdk::class);
        $data['company'] = empty($data['company']) ? $data['firstname']. ' ' . $data['lastname'] : $data['company'];
        # set the company name as the user's full name when not available
        $data['trigger_event'] = 0;
        # we don't want the event on the API triggered
        $domain = $request->session()->get('domain');
        # the domain the registration occurred on
        $partner = $request->session()->get('partner');
        # get the partner
        if (!empty($partner)) {
            $data['partner'] = $partner->id;
        }
        $response = create_account($sdk, $data);
        if (!$response->isSuccessful()) {
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Error while creating the Dorcas account!');
        }
        $dorcasUser = new DorcasUser($response->getData(), $sdk);
        # create the user
        $user = null;
        $company = null;
        try {
            DB::transaction(function () use ($dorcasUser, &$user) {
                $data = $dorcasUser->company(true, true);
                # get the company data
                $company = Company::create([
                    'uuid' => $data->id,
                    'reg_number' => $data->registration,
                    'name' => $data->name,
                    'phone' => $data->phone,
                    'email' => $data->email,
                    'website' => $data->website
                ]);
                # create the company
                $user = $company->users()->create([
                    'uuid' => $dorcasUser->id,
                    'firstname' => $dorcasUser->firstname,
                    'lastname' => $dorcasUser->lastname,
                    'email' => $dorcasUser->email,
                    'password' => $dorcasUser->password,
                    'gender' => $dorcasUser->gender,
                    'photo_url' => $dorcasUser->photo,
                    'is_verified' => (int) $dorcasUser->is_verified
                ]);
                # create the user
            });
            event(new SignedUp($user, $partner, $domain));
            
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
        return $user;
    }
}
