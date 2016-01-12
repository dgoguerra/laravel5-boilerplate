<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

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
     * Mailer Service
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * Create a new password controller instance.
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param \Illuminate\Contracts\Mail\Mailer $mailer
     * @param \Illuminate\Routing\UrlGenerator $generator
     */
    public function __construct(Guard $auth, Mailer $mailer, UrlGenerator $generator)
    {
        $this->auth = $auth;
        $this->mailer = $mailer;

        // set the authenticated users landing page url.
        $this->redirectTo = $generator->route('user.home');

        $this->middleware('guest', [
            'except' => ['getReset', 'getChangePassword', 'postChangePassword', 'getChangeEmail', 'postChangeEmail', 'confirmChangeEmail']
        ]);

        $this->middleware('auth', [
            'only' => ['getChangePassword', 'postChangePassword', 'getChangeEmail', 'postChangeEmail']
        ]);

        // getReset() and confirmChangeEmail() can be called from both logged and not logged users!
    }

    /**
     * Display the change password form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getChangePassword()
    {
        return view('auth.change_password');
    }

    /**
     * Request the current user's email change.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChangePassword(Request $request)
    {
        $this->validate($request, ['password' => 'required|confirmed|min:6']);

        $user = $this->auth->user();
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return redirect()->route('user.home')->with('status', 'Your password has been successfully updated.');
    }

    /**
     * Display the change email form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getChangeEmail()
    {
        return view('auth.change_email');
    }

    /**
     * Get a validator for an incoming email change request.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function emailChangeValidator(Authenticatable $user, array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|confirmed'
                . '|unique:users,email,'.$user->id
                . '|unique:users,email_to_change,'.$user->id
        ]);
    }

    /**
     * Request the current user's email change.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChangeEmail(Request $request)
    {
        $user = $this->auth->user();
        $validator = $this->emailChangeValidator($user, $request->all());

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        $user->email_to_change = $request->get('email');
        $user->email_change_token = str_random(60);
        $user->save();

        $vars = ['user' => $user];

        $this->mailer->send('emails.confirm_email_change', $vars, function($mail) use ($user) {
            $mail->to($user->email, $user->name)->subject('Email Change Confirmation');
        });

        return redirect()->back()->with('status', 'A confirmation email has been sent to your new email address.');
    }

    /**
     * Confirm en email change request.
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function confirmChangeEmail($token)
    {
        $user = User::where('email_change_token', $token)->first();

        if (! $user) {
            throw new NotFoundHttpException;
        }

        $user->email_change_token = null;
        $user->email = $user->email_to_change;
        $user->email_to_change = null;
        $user->save();

        return redirect()->route('user.home')->with('status', 'Your email address has been successfully changed.');
    }
}
