<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use PHPMailer;
use Closure;
use Illuminate\Support\Arr;
use App\Mail\Message;
use InvalidArgumentException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\Container;

/**
 * Description of Mailer
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
class Mailer {
    
    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $views;

    /**
     * The PHPMailer instance.
     *
     * @var \PHPMailer
     */
    protected $phpmailer;

    /**
     * The global from address and name.
     *
     * @var array
     */
    protected $from;

    /**
     * The global to address and name.
     *
     * @var array
     */
    protected $to;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;
    
    /**
     * Create a new Mailer instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $views
     * @param  \PHPMailer  $phpmailer
     * @return void
     */
    public function __construct(Factory $views, PHPMailer $phpmailer)
    {
        $this->views     = $views;
        $this->phpmailer = $phpmailer;
    }
    
    /**
     * Set the global from address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysFrom($address, $name = null)
    {
        $this->from = compact('address', 'name');
    }

    /**
     * 
     * @param type $view
     * @param array $data
     * @param type $callback
     * @return type
     */
    public function send($view, array $data = array(), $callback = null) {
        
        $sended = false;
                
        // First we need to parse the view, which could either be a string or an array
        // containing both an HTML and plain text versions of the view which should
        // be used when sending an e-mail. We will extract both of them out here.
        list($view, $plain, $raw) = $this->parseView($view);
        
        // Once we have retrieved the view content for the e-mail we will set the body
        // of this message using the HTML type, which will provide a simple wrapper
        // to creating view based emails that are able to receive arrays of data.
        $this->addContent($view, $plain, $raw, $data);
        
        $data['message'] = $message = $this->createMessage();
               
        $this->callMessageBuilder($callback, $message);
                
        try{
            $sended = $message->getPHPMailerMessage()->send();
        }finally{
            $this->resetPHPMailer();
        }
        return $sended;
    }
    
    /**
     * Create a new message instance.
     *
     * @return \App\Mail\Message
     */
    protected function createMessage()
    {
        $message = new Message($this->phpmailer);

        // If a global from address has been specified we will set it on every message
        // instances so the developer does not have to repeat themselves every time
        // they create a new message. We will just go ahead and push the address.
        if (! empty($this->from['address'])) {
            $message->from($this->from['address'], $this->from['name']);
        }

        return $message;
    }
    
    protected function resetPHPMailer()
    {
        //call phpmailer destructor;
        $this->phpmailer = null;
    }
    
    
    /**
     * Call the provided message builder.
     *
     * @param  \Closure|string  $callback
     * @param  \App\Mail\Message  $message
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function callMessageBuilder($callback, $message)
    {
        if ($callback instanceof Closure) {
            return call_user_func($callback, $message);
        }

        throw new InvalidArgumentException('Callback is not valid.');
    }
    
     /**
     * Add the content to a given message.
     *
     * @param  string  $view
     * @param  string  $plain
     * @param  string  $raw
     * @param  array  $data
     * @return void
     */
    protected function addContent($view, $plain, $raw, $data)
    {
        if (isset($view)) {
            $this->phpmailer->msgHTML($this->getView($view, $data));
        }

        if (isset($plain)) {
            $this->phpmailer->AltBody = $this->getView($plain, $data);
        }

        if (isset($raw)) {
            $this->phpmailer->AltBody = $raw;
        }
    }
    
    /**
     * Render the given view.
     *
     * @param  string  $view
     * @param  array  $data
     * @return string
     */
    protected function getView($view, $data)
    {
        return $this->views->make($view, $data)->render();
    }
    
    /**
     * Get the view factory instance.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public function getViewFactory()
    {
        return $this->views;
    }
    
    /**
     * Get the PHPMailer instance.
     *
     * @return \PHPMailer
     */
    public function getPHPMailer()
    {
        return $this->phpmailer;
    }
    
    /**
     * Set the PHPMailer instance.
     *
     * @param  \PHPMailer  $phpmailer
     * @return void
     */
    public function setPHPMailer($phpmailer)
    {
        $this->phpmailer = $phpmailer;
    }
    
    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Parse the given view name or array.
     *
     * @param  string|array  $view
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseView($view)
    {
        if (is_string($view)) {
            return [$view, null, null];
        }

        // If the given view is an array with numeric keys, we will just assume that
        // both a "pretty" and "plain" view were provided, so we will return this
        // array as is, since must should contain both views with numeric keys.
        if (is_array($view) && isset($view[0])) {
            return [$view[0], $view[1], null];
        }

        // If the view is an array but doesn't contain numeric keys, we will assume
        // the views are being explicitly specified and will extract them via
        // named keys instead, allowing the devs to use one or the other.
        if (is_array($view)) {
            return [
                Arr::get($view, 'html'),
                Arr::get($view, 'text'),
                Arr::get($view, 'raw'),
            ];
        }

        throw new InvalidArgumentException('Invalid view.');
    }
}
