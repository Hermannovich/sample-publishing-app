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
interface ArticleRepoInterface {
    public function highlight($limit = 10);
    
    public function findBySlug($slug);
    
    public function loadUserArticle($user_id);
    
    public function delete($id);
    
    public function save($data, $user_id);
    
    public function latestId();
}
