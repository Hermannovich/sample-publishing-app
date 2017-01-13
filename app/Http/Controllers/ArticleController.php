<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ArticleRepoInterface;
use App\Exceptions\ArticleNotFoundException;
use File;
use Illuminate\Support\Str;
use Feed;
use Cache;
use Carbon\Carbon;

class ArticleController extends Controller
{
    protected $articleRepo;
    
    const LATEST_FEED_ID_KEY = 'FeedLatestArticleID';
    const ARTICLE_FEED_ID_KEY = 'ArticleFeedID';
    
    public function __construct(ArticleRepoInterface $articleRepo) {
        $this->articleRepo = $articleRepo;
        $this->middleware('auth', ['except' => ['index', 'details', 'download', 'feed'] ]);
    }
    
    public function index() {
        $articles = $this->articleRepo->highlight(config('articles.highlight'));
        return view('articles.index', compact('articles'));
    }
    
    public function details( $slug ){
        $article = $this->loadArticle( $slug );
        return view('articles.details', compact('article'));
    }
    
    protected function loadArticle($slug) {
        $article = $this->articleRepo->findBySlug( $slug );
        if(is_null($article))
            throw new ArticleNotFoundException("The article you are trying to read does not exist or has been deleted.");
        return $article;
    }


    public function download( $slug, \PDF $pdf ){
        $article = $this->loadArticle( $slug );
        $pdf = $pdf::loadView('articles.download', compact('article'));
	return $pdf->setPaper('a3', 'portrait')
                ->download('article-'. $article->slug . '-' . date('d-m-Y') . '.pdf');
    }
    
    public function delete($id) {
        try{
            $this->articleRepo->delete((int) $id);
            return redirect(route('user.profile'))->with('success', 'Article deleted.');
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return redirect()->back()->with('error', 'Article with identifier #' . $id . ' does not exists.');
        }catch(\Exception $e){
            die($e->getMessage());
            return redirect()->back()->with('error', 'Internal Error when deleting article. Please retry again.');
        }
    }
    
    public function publish() {
        return view('articles.publish');
    }
    
    /**
     * Handle a publish request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validator($request->all())->validate();
        $articleData = $this->articleData($request);
        
        if($this->articleRepo->save($articleData, $this->auth()->id())){
            return redirect(route("user.profile"))->with('success', 'Article published.');
        }else{
            return redirect()->back()->with('error', 'Failed to save this article. Please try again, if this error persists please contact us.');
        } 
    }
    
    /**
     * Get a validator for an incoming publish request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title'       => 'required|max:255',
            'description' => 'required',
            'summary'     => 'required|max:' . config('articles.excerpt_content') ,
            'photo'       => 'max:2048|mimes:gif,png,jpg,jpe,jpeg' //max 2048kb, image file
        ]);
    }
    
    /**
     * Get the article data from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function articleData(Request $request)
    {
        $path = $this->handlePhoto($request);
        $articleData= $request->only(  'title', 'description', 'photo', 'summary' );
        $articleData['photo'] = $path;
        return $articleData;
    }
    
    /**
     * Handle uploaded article's photo if any.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function handlePhoto(Request $request) {
        if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $images_path = public_path('articles');
            if( ! File::isDirectory($images_path)){
                File::makeDirectory($images_path, 0777, true);
            }
            
            try{
                $filename = Str::random() . '.' . $request->photo->extension();
                $request->photo->move($images_path, $filename);
                return $filename;
            }catch( Symfony\Component\HttpFoundation\File\Exception\FileException $e){
                //TODO: Hanlde file upload exception error.
            }
        }
        return '';
    }
    
    public function feed(Feed $feed, Cache $cache){
        
        $latestArticleId = $cache::get(self::LATEST_FEED_ID_KEY);
        
        // cache the feed for 60 minutes
        $feed->setCache(60, self::ARTICLE_FEED_ID_KEY);
            
        if(is_null($latestArticleId) || !$feed->isCached() || $LatestArticleId < $this->articleRepo->latestId()){
            
            $articles = $this->articleRepo->highlight(config('articles.highlight'));
            
            $feed->title = config('app.name');
            $feed->description = config('app.description');
            $feed->link = route('article.feed');
            $feed->setDateFormat('carbon');
            $feed->pubdate = Carbon::now();
            $feed->lang = 'en';
            $feed->setShortening(true);
            $feed->setTextLimit(100);
            
            if( $articles->count() ) {
                $feed->pubdate = $articles->get(0)->created_at;
                foreach ($articles as $article) {
                    // set item's title, author, url, pubdate, description, content, enclosure (optional)*
                    $feed->add(
                        $article->title, 
                        $article->author, 
                        route('article.details', $article->slug), 
                        $article->publishDate(), 
                        $article->summary(), 
                        $article->feedDescriptionWithImage()
                    );
                }
            }
        }
        
        // first param is the feed format
        // optional: second param is cache duration (value of 0 turns off caching)
        // optional: you can set custom cache key with 3rd param as string
        return $feed->render('rss');
    }
}
