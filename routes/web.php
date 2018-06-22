<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//
// Static pages
//

Route::get('/', 'StaticPageController@index');
Route::get('/about', 'StaticPageController@about');
Route::match(['post', 'get'], '/contact', 'StaticPageController@contact');

//
// Blog
//

Route::get('blog', 'BlogPostController@index');
Route::get('blog/{slug}', 'BlogPostController@view');
Route::get('blog-post/{id}', 'BlogPostController@viewCanonical');

//
// Newsletter
//

Route::post('newsletter', 'NewsletterController@subscribe');

//
// ImagePlaceholder - Cached placeholder images (from external source, but served as internal)
//

Route::get('img/placeholders/{name}.jpg', 'ImagePlaceholderController@get');
