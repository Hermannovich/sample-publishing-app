<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Mail;

use PHPMailer; 

/**
 * Description of Message
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
class Message {
    /**
     * The PHPMailer instance.
     *
     * @var \PHPMailer
     */
    protected $phpmailer;

    /**
     * Create a new message instance.
     *
     * @param  \PHPMailer  $phpmailer
     * @return void
     */
    public function __construct($phpmailer)
    {
        $this->phpmailer = $phpmailer;
    }
    
    /**
     * Add a "from" address to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        $this->phpmailer->setFrom($address, $name);

        return $this;
    }

    /**
     * Add a recipient to the message.
     *
     * @param  string|array  $address
     * @param  string|null  $name
     * @param  bool  $override
     * @return $this
     */
    public function to($address, $name = null, $override = false)
    {
        if ($override)
            $this->phpmailer->clearAddresses();
        
        return $this->addAddresses($address, $name);
    }
    
    /***
     * These function cc, bcc, replyTo are here to be confirm to Mailable interface
     */
    public function cc($address, $name = null){}
    public function bcc($address, $name = null){}
    public function replyTo($address, $name = null){}
    
    /**
     * Add a recipient to the message.
     *
     * @param  string|array  $address
     * @param  string  $name
     * @param  string  $type
     * @return $this
     */
    protected function addAddresses($address, $name)
    {
        if (is_array($address)) {
            foreach($address as $key => $addr)
                $this->phpmailer->addAddress($addr, ( isset($name[$key]) ? $name[$key] : null ) );
        } else {
            $this->phpmailer->addAddress($address, $name);
        } 

        return $this;
    }

    
    /**
     * Set the subject of the message.
     *
     * @param  string  $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->phpmailer->Subject = $subject;

        return $this;
    }
    
    /**
     * Get the underlying PHPMailer Message instance.
     *
     * @return \PHPMailer
     */
    public function getPHPMailerMessage()
    {
        return $this->phpmailer;
    }
    
    /**
     * Dynamically pass missing methods to the PHPMailer instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $callable = [$this->phpmailer, $method];

        return call_user_func_array($callable, $parameters);
    }
    
 }
