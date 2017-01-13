<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'registration_completed' => false,
    ];
});

$factory->define(App\Article::class, function (Faker\Generator $faker) {
    static $static_title;
     
    $title = empty($static_title) ? ($static_title ?: $static_title = 'I need this sport to be able to test') 
            : $faker->sentence(5, true);
    
    return [
        'title' => $title,
        'photo' => $faker->imageUrl(720, 540, 'sports'),
        'description' => $faker->paragraphs(5, true),
        'summary' => $faker->text(100),
        'user_id' => 1,
        'slug' => str_slug( $title )
    ];
});
