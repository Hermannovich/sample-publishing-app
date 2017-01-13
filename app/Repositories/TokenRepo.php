<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Repositories\Interfaces\TokenRepoInterface;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use App\Exceptions\InValidRegistrationToken;

/**
 * Description of TokenRepo
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
class TokenRepo implements TokenRepoInterface{
    
    protected function getKey() {
        $key = config('app.key');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        return $key;
    }
    public function create($user) {
        
        $url = url('/');
        $token = [
            "iss"   => $url,
            "aud"   => $url,
            "iat"   => time(),
            "nbf"   => time(),
            "jti"   => $user->id,
            "typ"   => url('/register'),
            "email" => $user->email,
        ];
        
        return JWT::encode($token, $this->getKey());
    }

    public function fetch($jwt) {
        //HS256 is the default encryption method of JWT.
        $decoded = JWT::decode($jwt, $this->getKey(), array('HS256'));
        $decoded = (array) $decoded;
        
        if(!$this->isValidRegistrationToken($decoded))
            throw new InValidRegistrationToken("Invalid registration token.");
        
        return $decoded;
    }
    
    protected function isValidRegistrationToken($token) {
        $url = url('/');
        return 
            $token['iss'] == $url &&
            $token['aud'] == $url && 
            $token['typ'] == url('/register'); 
    }
}
