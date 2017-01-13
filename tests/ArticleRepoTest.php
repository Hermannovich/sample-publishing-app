<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Article;
use App\Repositories\Interfaces\ArticleRepoInterface;

class ArticleRepoTest extends TestCase
{
    use DatabaseMigrations;
    
    protected $article;
    
    protected $expected;
    
    public function setUp() {
        parent::setUp();
        $articleItems       = factory(Article::class, 100)->create();
        $this->expected     = $articleItems->splice(90, 10);
        $this->articleRepo  = $this->app->make(ArticleRepoInterface::class);
    }
    
    /**
     * @test
     */
    public function highlight_should_return_with_a_limited_article(){
       $result =  $this->articleRepo->highlight(10);
       $diff   = $result->diff($this->expected);
       $this->assertTrue( ($diff->count() == 0) );
    }
}
