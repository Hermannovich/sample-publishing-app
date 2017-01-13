<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\TokenRepoInterface;
use App\Exceptions\InValidRegistrationToken;
use App\Repositories\Interfaces\UserRepoInterface;
use AppMailer;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/register';
    
    /**
     *
     * @var App\Repositories\Interfaces\TokenRepoInterface 
     */
    protected $tokenRepo;
    
    /**
     * @var App\Repositories\Interfaces\UserRepoInterface
     */
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TokenRepoInterface $tokenRepo, UserRepoInterface $userRepo)
    {
        $this->middleware('guest');
        $this->tokenRepo = $tokenRepo;
        $this->userRepo  = $userRepo;
    }
    
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
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
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt("dummy-secret"),
            'registration_completed' => false
        ]);
    }
    
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        
        try{
            //Sent email to $user and display a message for him 
            //to help him to understand that it should verify his email.
            $result = AppMailer::send(
                        'emails.users.email-verification', 
                        ['verification_link' => route('register.email.verification', $this->tokenRepo->create($user))],
                        function($message) use ($user){
                            $message->to($user->email)
                                    ->subject("[" . config("app.name") . "] Confirm your email" );
                        });         
            if($result == false)
                return $this->resetUserRegistration($request, $user);
        }  catch ( \InValidArgumentException $e ){
            return $this->resetUserRegistration($request, $user);
        }
        
        $request->session()->flash('verificationEmailSended', true);
        $this->redirectTo = '/register';
        return redirect($this->redirectTo);
    }
    
    protected function resetUserRegistration($request, $user) {
        User::destroy($user->id);
        $this->redirectTo = '/register';
        $request->session()->flash('sendEmailError', 'Failed to send email. Please retry or contact us.');
        return redirect($this->redirectTo);
    }
    
    public function emailVerification($jwt){
        
        try{
           $token = $this->tokenRepo->fetch($jwt);
        }catch(InValidRegistrationToken $e){
            //TODO: Handle this exception InValidRegistrationToken.
        }
        
        try{
            $user = $this->userRepo->findByEmail($token['email']);
        }catch(\App\Exceptions\UserEmailNotFoundException $e){
            //TODO: Handle this exception UserEmailNotFoundException.
        }
        
        if($user->hasCompletedRegistration()){
            return redirect(route('login'))->with('status', 'You have already verified your email and set your password. You can login and publish some news.');
        }else{
            return view('auth.passwords.register-final-step', compact('user', 'jwt'));
        }
    }
    
    /**
     * Get the password validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'jwt'      => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }
    
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function finalizedRegistration(Request $request){
        
        $this->validate($request, $this->rules(), $this->validationErrorMessages());
        
        $user = $this->userRepo->updatePassword($this->credentials($request));
        
        if($user->hasCompletedRegistration()){
            return redirect(route('login'))->with('status', 'Registration completed. You can login and publish amazing news.');
        }else{
            return redirect()->back()->withErrors($user->getErrors());
        }
    }
    
    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'jwt'
        );
    }
    
    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }
    
    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
