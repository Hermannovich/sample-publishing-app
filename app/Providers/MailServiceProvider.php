<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Providers;

use PHPMailer;
use App\Services\Mailer;
use Illuminate\Support\ServiceProvider;

/**
 * Description of MailServiceprovider
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
class MailServiceProvider extends ServiceProvider {
    
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
        $this->registerPHPMailer();
        
        $this->app->singleton('appmailer', function ($app) {
            // Once we have create the mailer instance, we will set a container instance
            // on the mailer. This allows us to resolve mailer classes via containers
            // for maximum testability on said classes instead of passing Closures.
            $mailer = new Mailer(
                $app['view'], $app['phpmailer']
            );

            $this->setMailerDependencies($mailer, $app);

            // If a "from" address is set, we will set it on the mailer so that all mail
            // messages sent by the applications will utilize the same "from" address
            // on each one, which makes the developer's life a lot more convenient.
            $from = $app['config']['mail.from'];

            if (is_array($from) && isset($from['address'])) {
                $mailer->alwaysFrom($from['address'], $from['name']);
            }

            return $mailer;
        });
    }
    
    /**
     * Set a few dependencies on the mailer instance.
     *
     * @param  \App\Mail\Mailer  $mailer
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function setMailerDependencies($mailer, $app)
    {
        $mailer->setContainer($app);
    }
    
    /**
     * Register the PHPMailer instance.
     *
     * @return void
     */
    protected function registerPHPMailer()
    {
        // Register the actual PHPMailer instance, which allows us to
        // override this transporter instances during app start-up if necessary.
        $this->app['phpmailer'] = $this->app->share(function ($app) {
            $mailer    = $this->app['config']['phpmailer.mailer'];
            switch($mailer){
                case 'smtp':
                    return $this->buildStmpPHPMailer();
                case 'qmail':
                    return $this->buildQmailPHPMailer();
                case 'sendmail':
                    return $this->buidSendmailPHPMailer();
                case 'mail':
                default: //Because mail is the default mailer used by PHPMailer.
                    return $this->buildMailPHPMailer();    
            }
        });
    }
    
    
    protected function buidSendmailPHPMailer() {
        $phpMailer = $this->buildMailPHPMailer();
        $phpMailer->isSendmail();
        return $phpMailer;
    }
    
    protected function buildQmailPHPMailer() {
        $phpMailer = $this->buildMailPHPMailer();
        $phpMailer->isQmail();
        return $phpMailer;
    }
    
    protected function buildMailPHPMailer() {
        return new PHPMailer(false);
    }
    
    protected function buildStmpPHPMailer() {
        $phpMailer = $this->buildMailPHPMailer();
        $phpMailer->isSMTP();
        $phpMailer->Host = $this->app['config']['phpmailer.host']; 
        $phpMailer->Port = $this->app['config']['phpmailer.port']; 
        $smtp_auth       = $this->app['config']['phpmailer.smtp_auth']; 
        if(is_bool($smtp_auth) && $smtp_auth){
            $phpMailer->SMTPAuth = true;
            $phpMailer->Username = $this->app['config']['phpmailer.username']; 
            $phpMailer->Password = $this->app['config']['phpmailer.password'];
        }
        return $phpMailer;
    }
    
    public function provides() {
        return ['appmailer', 'phpmailer'];
    }

}
