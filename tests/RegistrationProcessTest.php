<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Facades\Mailer;
use App\Repositories\Interfaces\TokenRepoInterface;
use App\Repositories\Interfaces\UserRepoInterface;

class RegistrationProcessTest extends TestCase
{
    const APP_KEY = "test-key";
    
    use DatabaseMigrations;
    
    public function setUp() {
        parent::setUp();
    }
    
    /**
     * 
     * @test
     */
    public function it_should_sent_an_email_when_data_are_OK() {
        Mailer::shouldReceive('send')->once()->andReturn(true);
        $this->call('POST', '/register', ['email' => 'hermann@job.io', 'name' => 'hermann']);
        $this->assertSessionHas('verificationEmailSended', true);
        $this->assertRedirectedTo('/register');
    }
    
    /**
     * @test
     */
    public function user_should_see_errors_if_sent_email_failed_and_mail_should_be_resetted() {
        Mailer::shouldReceive('send')->once()->andReturn(false);
        $this->call('POST', '/register', ['email' => 'hermann@job.io', 'name' => 'hermann']);
        $this->assertSessionHas('sendEmailError');
        $this->assertRedirectedTo('/register');
        $this->dontSeeInDatabase('users', ['email' => 'hermann@job.io']);
    }
    
    /**
     * @test
     */
    public function user_should_see_errors_if_sent_email_failed_with_exception_and_mail_should_be_resetted() {
        Mailer::shouldReceive('send')->once()->andThrow(Mockery::mock(InValidArgumentException::class));
        $this->call('POST', '/register', ['email' => 'hermann@job.io', 'name' => 'hermann']);
        $this->assertSessionHas('sendEmailError');
        $this->assertRedirectedTo('/register');
        $this->dontSeeInDatabase('users', ['email' => 'hermann@job.io']);
    }
    
    protected function mock($class){
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);
        return $mock;
    }
    
    
    /**
     * @test
     */
    public function when_user_click_on_a_right_token_he_should_see_password_form(){
        $url = url('/');
        /**
         * This token is from the current array claims
          
          $token = [
            "iss"   => $url,
            "aud"   => $url,
            "iat"   => time(),
            "nbf"   => time(),
            "jti"   => 1,
            "typ"   => url('/register'),
            "email" => 'hermann@job.io',
          ]; 
         */
        $token = [
            "iss"   => $url,
            "aud"   => $url,
            "iat"   => time(),
            "nbf"   => time(),
            "jti"   => 1,
            "typ"   => url('/register'),
            "email" => 'hermann@job.io',
          ];
        $jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0ODMxOTEzMDcsIm5iZiI6MTQ4MzE5MTMwNywianRpIjoxLCJ0eXAiOiJodHRwOlwvXC9sb2NhbGhvc3RcL3JlZ2lzdGVyIiwiZW1haWwiOiJoZXJtYW5uQGpvYi5pbyJ9.cGQdq_GaXfJ-mFx5xnV7lN0x-LXMpp_bWWzqATWp2xk";
        
        $this->tokenRepo = $this->mock(TokenRepoInterface::class);
        $this->userRepo  = $this->mock(UserRepoInterface::class);
        $user            = factory(App\User::class, 1)->make();
        $user->email     = 'hermann@job.io';
        $user->id        = 1;
        
        $this->tokenRepo->shouldReceive('fetch')->with($jwt)->andReturn($token);
        $this->userRepo->shouldReceive('findByEmail')->with($token['email'])->andReturn($user);
        
        $response = $this->call('GET','/register/email-confirmation/' . $jwt);
        $this->assertEquals('auth.passwords.register-final-step', $response->original->getName());
    }
    
    /**
     * @test
     */
    public function user_should_be_redirected_to_login_if_have_already_completed_registration(){
        $url = url('/');
        /**
         * This token is from the current array claims
          
          $token = [
            "iss"   => $url,
            "aud"   => $url,
            "iat"   => time(),
            "nbf"   => time(),
            "jti"   => 1,
            "typ"   => url('/register'),
            "email" => 'hermann@job.io',
          ]; 
         */
        $token = [
            "iss"   => $url,
            "aud"   => $url,
            "iat"   => time(),
            "nbf"   => time(),
            "jti"   => 1,
            "typ"   => url('/register'),
            "email" => 'hermann@job.io',
          ];
        $jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0ODMxOTEzMDcsIm5iZiI6MTQ4MzE5MTMwNywianRpIjoxLCJ0eXAiOiJodHRwOlwvXC9sb2NhbGhvc3RcL3JlZ2lzdGVyIiwiZW1haWwiOiJoZXJtYW5uQGpvYi5pbyJ9.cGQdq_GaXfJ-mFx5xnV7lN0x-LXMpp_bWWzqATWp2xk";
        
        $this->tokenRepo = $this->mock(TokenRepoInterface::class);
        $this->userRepo  = $this->mock(UserRepoInterface::class);
        $user            = factory(App\User::class, 1)->make();
        $user->email     = 'hermann@job.io';
        $user->id        = 1;
        $user->registration_completed = true;
        
        $this->tokenRepo->shouldReceive('fetch')->with($jwt)->andReturn($token);
        $this->userRepo->shouldReceive('findByEmail')->with($token['email'])->andReturn($user);
        
        $response = $this->call('GET','/register/email-confirmation/' . $jwt);
        $this->assertRedirectedTo('/login');
        $this->assertSessionHas('status');
    }    
}
