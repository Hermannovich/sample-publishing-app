<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepoInterface;
use App\User;
use Illuminate\Support\Str;

/**
 * Description of UserRepo
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
class UserRepo implements UserRepoInterface {
    
    protected $user;
    
    public function __construct(User $user) {
        $this->user = $user;
    }
    
    public function findByEmail($email) {
        
        $result = $this->user->where('email', $email)->get();
        
        if($result->count() == 0)
            throw new UserEmailNotFoundException("User with the current email $email doesn't exist.");
       
        return $result->get(0); 
    }
    
    public function updatePassword($credentials){
        
        try{
            
            $user = $this->findByEmail($credentials['email']);
            
            $user->forceFill([
                'password' => bcrypt($credentials['password']),
                'registration_completed' => true,
                'remember_token' => Str::random(60),
            ])->save();
            
        }catch(\App\Exceptions\UserEmailNotFoundException $e){
            $user = $this->user->newInstance(array('registration_completed' => false));
            $user->setErrors([
                'email' => 'Invalid Email'
            ]);
        }
         
         return $user;
    }
}
