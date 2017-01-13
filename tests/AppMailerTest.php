<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Mail\Mailer;

class AppMailerTest extends TestCase
{
    use WithoutMiddleware;
    
    public function testMailerSendSendsMessageWithProperViewContent()
    {
    }
}
