<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegistered;
use App\User;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Routing\UrlGenerator;

class AuthController extends Controller
{
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

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Landing page for a registered or logged in user.
     *
     * @var string
     */
    protected $redirectTo = null;

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Laravel Config
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Mailer Service
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * Events dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new authentication controller instance.
     *
     * @param Guard $auth
     * @param ConfigRepository $config
     * @param Mailer $mailer
     * @param Dispatcher $dispatcher
     * @param UrlGenerator $generator
     */
    public function __construct(Guard $auth, ConfigRepository $config, Mailer $mailer, Dispatcher $dispatcher, UrlGenerator $generator)
    {
        $this->auth = $auth;
        $this->config = $config;
        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;

        // set the authenticated users landing page url.
        $this->redirectTo = $generator->route('user.home');

        // all methods but getLogout can only be used by a not logged user
        $this->middleware('guest', ['except' => 'getLogout']);

        // getLogout can only be used by a logged user
        $this->middleware('auth', ['only' => 'getLogout']);
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->is_active = 1;

        // if an email confirmation needs to be done, the user is initially not active
        if ($this->config->get('auth.email_confirmation') === true) {
            $user->is_active = 0;
            $user->mail_confirm_token = str_random(60);
        }

        return $user->save() ? $user : null;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        // overrides RegistersUsers::postRegister to send an email confirmation link instead of directly
        // logging in the user.

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $user = $this->create($request->all());

        $this->dispatcher->fire(new UserRegistered($user));

        // if email confirmation is active, he can't log in yet. Show a 'registration was successful'
        // message and send him an email to confirm his email address.
        if ($this->config->get('auth.email_confirmation') === true) {
            $vars = ['user' => $user];
            $this->mailer->send('emails.confirm_registration', $vars, function($mail) use ($user) {
                $mail->to($user->email, $user->name)->subject('Account Confirmation');
            });

            return redirect()->route('auth.login.show')->with('status',
                'Thanks for registering. We have sent you a confirmation email '
                . 'to <strong>'.$user->email.'</strong> to activate your account.'
            );
        }

        // there is no email confirmation; login the user and redirect him to the landing page.

        $this->auth->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * Confirm a registered account's email based on the given token.
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function confirmRegisterEmail($token)
    {
        $user = User::where('mail_confirm_token', $token)->first();

        if ($user === null) {
            throw new NotFoundHttpException;
        }

        // the token matches with a user. Activate it, log him in and
        // redirect him to his landing page.

        $user->mail_confirm_token = null;
        $user->is_active = 1;
        $user->save();

        $this->auth->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        // overrides AuthenticatesUsers::postLogin to make a custom validation of the user
        // trying to log in.

        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only($this->loginUsername(), 'password');

        if ($this->auth->attempt($credentials, $request->has('remember'))) {
            // the authentication was successful. We do not check here if the user is active;
            // \App\Http\Middleware\Authenticate checks it for every request and bounces the
            // user to the login page with a custom error if he isn't.
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }
}
