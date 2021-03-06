<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use App\Logic\User\UserRepository;
use App\Logic\User\CaptureIp;
use App\Models\User;
use App\Models\Social;
use App\Models\Role;
use App\Models\Profile;
use App\Traits\CaptchaTrait;
use Laravel\Socialite\Facades\Socialite;
use Validator;
use Gravatar;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers
    {
        getLogout as authLogout;
    }
	use CaptchaTrait;
	use ThrottlesLogins;
    protected $auth;
    protected $userRepository;
	protected $redirectPath = '/dashboard';

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, UserRepository $userRepository)
	{
		$this->middleware('guest',
			['except' =>
				['getLogout', 'resendEmail', 'activateAccount']]);

        $this->auth = $auth;
        $this->userRepository = $userRepository;
	}

    /**
     * Overwrite getLogout method of trait AuthenticatesUsers;
     * @return Response
     */
    public function getLogout()
    {
	    \Auth::logout();
	    return redirect('auth/login')->with('status',  \Lang::get('auth.loggedOut'));
    }

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'name' 			=> 'required|max:255|unique:users',
			'first_name' 	=> 'required|max:255',
			'last_name' 	=> 'required|max:255',
			'email' 		=> 'required|email|max:255|unique:users',
			'password' 		=> 'required|confirmed|min:6',
		]);
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postRegister(Request $request)
	{
	    if($this->captchaCheck() == false)
	    {
	        return redirect()->back()->withErrors(['Sorry, Wrong Captcha'])->withInput();
	    }

		$validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

		$activation_code 		= str_random(60) . $request->input('email');
		$user 					= new User;
		$user->name 			= $request->input('name');
		$user->first_name 		= $request->input('first_name');
		$user->last_name 		= $request->input('last_name');
		$user->email 			= $request->input('email');
		$user->password 		= bcrypt($request->input('password'));

		// GET GRAVATAR
		$user->gravatar 		= Gravatar::get($request->input('email'));

		// GET ACTIVATION CODE
		$user->activation_code 	= $activation_code;

		// GET IP ADDRESS
		$userIpAddress 				= new CaptureIp;
		$user->signup_ip_address	= $userIpAddress->getClientIp();

		// SAVE THE USER
		if ($user->save()) {

			$this->sendEmail($user);
	        $user_role = Role::whereName('user')->first();
	        $user->assignRole($user_role);

            $profile = new Profile;
            $user->profile()->save($profile);

			$attemptsAllowed 		= 50;

			return view('auth.activateAccount')
				->with('id', $user->id)
			    ->with('email', $user->email)
			    ->with('username', $user->name)
			    ->with('attempts', $user->resent)
			    ->with('remaining', ($attemptsAllowed - ($user->resent)));

		} else {

			\Session::flash('message', \Lang::get('notCreated') );
			return redirect()->back()->withInput();
		}

	}
	//*/

	public function sendEmail(User $user)
	{
		$data = array(
			'name' => $user->name,
			'code' => $user->activation_code,
		);

		\Mail::queue('emails.activateAccount', $data, function($message) use ($user) {
			$message->subject( \Lang::get('auth.pleaseActivate') );
			$message->to($user->email);
		});
	}

	public function resendEmail($id)
	{
		$user 					= User::find($id);
		$username				= $user->name;
		$userEmail				= $user->email;
		$attemptsAllowed 		= 50;
		$attemptsUsed			= $user->resent;
		$attemptsRemaining		= $attemptsAllowed - $attemptsUsed;
		if( $attemptsUsed >= $attemptsAllowed )
		{
			return view('auth.tooManyEmails')

			    ->with('email', $userEmail)
			    ->with('username', $username)
			    ->with('attempts', $attemptsUsed)
			    ->with('remaining', $attemptsRemaining);
			;
		} else {
			$user->resent = $user->resent + 1;
			$user->save();
			$this->sendEmail($user);

			return view('auth.activateAccount')
				->with('id', $id)
			    ->with('email', $userEmail)
			    ->with('username', $username)
			    ->with('attempts', $user->resent)
			    ->with('remaining', $attemptsRemaining);
		}
	}

	public function activateAccount($code, User $user)
	{

		if($user->accountIsActive($code)) {
			\Session::flash('message', \Lang::get('auth.successActivated') );
			return redirect('home');
		}

		\Session::flash('message', \Lang::get('auth.unsuccessful') );
		return redirect('home');

	}

    public function getSocialRedirect( $provider )
    {
        $providerKey = \Config::get('services.' . $provider);
        if(empty($providerKey))
            return view('pages.status')->with('error','No such provider');

        return Socialite::driver( $provider )->redirect();

    }

    public function getSocialHandle( $provider )
    {

        $user = Socialite::driver( $provider )->user();

        $social_user = null;

        //CHECK IF USERS EMAIL ADDRESS IS ALREADY IN DATABASE
        $user_check = User::where('email', '=', $user->email)->first();
        if(!empty($user_check))
        {
            $social_user = $user_check;
        }
        else // USER IS NOT IN DATABASE BASED ON EMAIL ADDRESS
        {

            $same_social_id = Social::where('social_id', '=', $user->id)->where('provider', '=', $provider )->first();
            // CHECK IF NEW SOCIAL MEDIA USER
            if(empty($same_social_id))
            {

                $new_social_user 					= new User;
                $new_social_user->email            	= $user->email;
                $name 								= explode(' ', $user->name);
				if ($user->email) {
					$new_social_user->name         	= $user->email;
				} else {
					$new_social_user->name			= $name[0];
				}
                $new_social_user->first_name      	= $name[0];

                // CHECK FOR LAST NAME
                if (isset($name[1])) {
                	$new_social_user->last_name     = $name[1];
                }

                $new_social_user->active           	= '1';
				$the_activation_code 				= str_random(60) . $user->email;
				$new_social_user->activation_code 	= $the_activation_code;

				// GET IP ADDRESS
				$userIpAddress 								= new CaptureIp;
				$new_social_user->signup_sm_ip_address		= $userIpAddress->getClientIp();

				// SAVE THE USER
                $new_social_user->save();

                // GET SOCIAL MEDIA LOGIN DATA
                $social_data 						= new Social;
                $social_data->social_id 			= $user->id;
                $social_data->provider 				= $provider;
                $new_social_user->social()->save($social_data);

				// GET GRAVATAR
				$new_social_user->gravatar          = Gravatar::get($user->email);

                // ADD ROLE
                $role = Role::whereName('user')->first();
                $new_social_user->assignRole($role);
                $social_user = $new_social_user;

                // LINK TO PROFILE TABLE
	            $profile = new Profile;
	            $social_user->profile()->save($profile);

            }
            else
            {
                //Load this existing social user
                $social_user = $same_social_id->user;
            }

        }

        $this->auth->login($social_user, true);

        if( $this->auth->user()->hasRole('user'))
        {
            //return redirect()->route('user.home');
        	return redirect('dashboard');
        }

        if( $this->auth->user()->hasRole('administrator'))
        {
        	return redirect('dashboard');
            //return redirect()->route('admin.home');
        }

        return \App::abort(500);
    }

}
