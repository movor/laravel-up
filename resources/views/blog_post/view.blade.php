@extends('layouts.default')

@section('content')

    <div class="container blog-post-view">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="mb-4">{{ $post->title }}</h1>
                <div class="lead">
                    <div>
                        <span class="text-muted">Author:</span>
                        <a href="/about">{{ $post->user->name }}</a>
                    </div>
                    <div>
                        <small>
                            <span class="text-muted">Posted on</span>
                            <span>{{ $post->published_at->format('d M Y') }}</span>
                        </small>
                    </div>
                    <div>

                        @foreach($post->tags as $tag)

                            <a href="{{ url("blog?tags=$tag->slug") }}" class="badge badge-primary">{{ $tag->name }}</a>

                        @endforeach

                    </div>
                </div>
                <img class="img-fluid rounded my-3" src="{{ asset($post->featured_image) }}" alt="">

                {!! $post->body_html !!}

            </div>
            <div class="col-lg-3 col-md-12">

                {{-- All Tags --}}

                <h5 class="text-muted">All Tags</h5>

                <div class="card mb-4">

                    <div class="card-body">
                        <div class="row">

                            @foreach(\App\Models\BlogTag::all() as $tag)

                                <div class="col-6 col-sm-4 col-md-3 col-lg-6 text-truncate text-primary mb-1">
                                    <a href="/blog?tags={{ $tag->slug }}">{{ $tag->name }}</a>
                                </div>

                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- /All Tags --}}

                {{-- Featured Posts --}}

                <h5 class="text-muted">Featured Posts</h5>

                <div class="row featured-posts">

                    @foreach($featuredPosts as $post)

                        <a href="{{ $post->getPageUrl() }}">
                            <div class="col-sm-6 col-md-4 col-lg-12">
                                <div class="card mb-4">
                                    <img class="card-img-top" src="{{ asset($post->featured_image) }}">
                                    <div class="card-body">
                                        <p class="card-title font-weight-bold mb-0 text-muted">{{ $post->title }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                    @endforeach

                </div>

                {{-- /Featured Posts --}}

            </div>
        </div>
    </div>

    <div class="container">
        <hr class="mt-5">
        <app-disqus></app-disqus>
    </div>

@endsection
