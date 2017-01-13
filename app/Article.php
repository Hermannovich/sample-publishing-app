<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'photo', 'description', 'summary', 'user_id', 'slug'
    ];
    
    /**
     * Get the user that owns the article.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function publishDate() {
        return $this->{self::CREATED_AT};
    }
    
    public function setTitleAttribute($title){
        $this->attributes['title'] = $title;
        $title .= ' ' . $this->max('id');
        $this->attributes['slug']  = Str::slug($title);
    }
    
    public function embedFeedImage() {
        if(!empty($this->photo))
            return "<img src='" . asset('/articles/' . $this->photo) . "' alt='" . $this->title ."' >";
        else return '';
    }
    
    public function summary() {
        return $this->summary;
    }
    
    public function feedDescriptionWithImage() {
        return  $this->description . $this->embedFeedImage();
    }
}
