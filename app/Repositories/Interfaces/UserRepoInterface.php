<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories\Interfaces;

/**
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
interface UserRepoInterface {
    public function findByEmail($email);
    public function updatePassword($credentials);
}
