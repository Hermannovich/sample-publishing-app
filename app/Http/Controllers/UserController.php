<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\ArticleRepoInterface;

class UserController extends Controller
{
    
    /**
     *
     * @var ArticleRepoInterface
     */
    protected $articleRepo;
    
    public function __construct(ArticleRepoInterface $articleRepo) {
        $this->middleware('auth');
        $this->articleRepo = $articleRepo;
    }
    
    public function profile() {
        $articles = $this->articleRepo->loadUserArticle($this->auth()->id());
        return view('users.profile', compact('articles'));
    }
    
}
