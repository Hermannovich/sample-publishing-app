<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Repositories\Interfaces\ArticleRepoInterface;
use App\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Description of ArticleRepo
 *
 * @author Hermannovich <donfack.hermann@gmail.com>
 */
class ArticleRepo implements ArticleRepoInterface{
    
    protected $article;
    
    public function __construct(Article $article) {
        $this->article = $article;
    }
    
    /**
     * 
     * @param integer $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function highlight($limit = 10) {
        return $this->article
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
    }

    /**
     * 
     * @param string $slug
     * @return Article|null
     */
    public function findBySlug($slug) {
        $result = $this->article->where('slug', $slug)->get();
        if($result->count())
            return $result->get(0);
        return null;
    }

    public function loadUserArticle($user_id, $limit = 10) {
        return $this->article
                ->where('user_id', $user_id)
                ->orderBy('id', 'DESC')
                ->paginate($limit);
    }
    
    /**
     * Delete the article from the database.
     *
     * @return bool|null
     *
     * @throws \Exception | ModelNotFoundException
     */
    public function delete($id) {
        $article = $this->article->findOrFail($id);
        return $article->delete();
    }

    public function save($data, $user_id) {
        $data['user_id'] = $user_id;
        $newInstance = $this->article->newInstance($data);
        return $newInstance->save();
    }

    public function latestId() {
        return $this->article->max('id');
    }

}
