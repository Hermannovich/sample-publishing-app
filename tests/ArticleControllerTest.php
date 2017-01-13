<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Repositories\ArticleRepo;
use App\Article;
use Illuminate\Database\Eloquent\Collection;

class ArticleControllerTest extends TestCase
{
    use DatabaseMigrations;
   
    public function setUp() {
        parent::setUp();
        $this->articleRepo = $this->mock(ArticleRepo::class);
        $this->articles = factory(Article::class, 100)->create();
        $this->disableExceptionHandling();
    }
    
    protected function mock($class){
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);
        return $mock;
    }
    
    /**
     * @test
     */
    public function it_should_display_10_highlighted_articles() {
       $this->articleRepo
               ->shouldReceive('highlight')
               ->with(10)
               ->once()
               ->andReturn($this->articles);
       
       $response = $this->call('GET', '/');
       
       $this->assertViewHas('articles');
       
       $articles = $response->original->getData()['articles'];
       
       $this->assertInstanceOf(Collection::class, $articles);
       
    }    
    
    /**
     * @test
     */
    public function it_should_display_no_article_when_initial_state(){
        $this->articleRepo
               ->shouldReceive('highlight')
               ->with(10)
               ->once()
               ->andReturn(collect());
       
       $this->visit('/')->see('No Article yet.');  
    }
    
    /**
     * @test
     */
    public function it_should_display_article_detail(){
        $slug = 'i-need-this-sport-to-be-able-to-test';
        
        $this->articleRepo
                ->shouldReceive('findBySlug')
                ->with($slug)
                ->andReturn($this->articles->get(0));
        
        $response = $this->call('GET', '/articles/' . $slug);
       
       $this->assertViewHas('article');
    }
    
    /**
     * @test
     * 
     * @expectedException App\Exceptions\ArticleNotFoundException
     */
    public function it_shoud_throw_exception_with_slug_of_no_article(){
        $slug = 'aaaa-i-need-this-sport-to-be-able-to-test-pppp';
        
        $this->articleRepo
                ->shouldReceive('findBySlug')
                ->with($slug)
                ->andReturn(null);
        
        $this->call('GET', '/articles/' . $slug);
    }
}
