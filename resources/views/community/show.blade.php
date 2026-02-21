@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Back Button -->
                <a href="{{ route('community') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Community
                </a>

                <!-- Post -->
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <!-- Post Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start gap-4">
                            <!-- Vote Buttons -->
                            <div class="flex flex-col items-center">
                                <button class="text-gray-400 hover:text-primary-600 transition" onclick="vote('up')">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                                <span class="text-xl font-bold text-gray-900 dark:text-white my-1">{{ $post->votes_count ?? 0 }}</span>
                                <button class="text-gray-400 hover:text-red-600 transition" onclick="vote('down')">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Post Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($post->category)
                                        <a href="{{ route('community.category', $post->category->slug) }}" class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs rounded">
                                            {{ $post->category->name }}
                                        </a>
                                    @endif
                                    @if($post->is_solved)
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 text-xs rounded">
                                            ✓ Solved
                                        </span>
                                    @endif
                                </div>

                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $post->title }}</h1>

                                <div class="flex items-center gap-3">
                                    <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=4F46E5&color=fff' }}" 
                                         alt="{{ $post->user->name }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <a href="{{ route('profile', $post->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                            {{ $post->user->name }}
                                        </a>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $post->user->reputation ?? 0 }} reputation • {{ $post->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="p-6">
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $post->content !!}
                        </div>

                        <!-- Tags -->
                        @if($post->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                @foreach($post->tags as $tag)
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-full">
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Post Actions -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <button class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition" onclick="bookmark()">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                    Bookmark
                                </button>
                                <button class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition" onclick="share()">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                    Share
                                </button>
                            </div>
                            @if(auth()->id() === $post->user_id || (auth()->user() && auth()->user()->hasRole('admin|moderator')))
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('post.edit', $post->slug) }}" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('post.destroy', $post->slug) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-red-600 hover:text-red-700 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </article>

                <!-- Comments Section -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ $post->comments->count() }} Comments
                    </h2>

                    <!-- Comment Form -->
                    @auth
                        <form action="{{ route('post.comments.store', $post->slug) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                            @csrf
                            <textarea name="content" rows="4" placeholder="Write your comment..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" required></textarea>
                            <div class="flex justify-end mt-4">
                                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 text-center">
                            <p class="text-gray-600 dark:text-gray-400">
                                <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline">Sign in</a> to leave a comment
                            </p>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @foreach($post->comments->whereNull('parent_id') as $comment)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                                <div class="flex gap-4">
                                    <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=4F46E5&color=fff' }}" 
                                         alt="{{ $comment->user->name }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <a href="{{ route('profile', $comment->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                                {{ $comment->user->name }}
                                            </a>
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
                                            @if($comment->is_accepted)
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 text-xs rounded">
                                                    Accepted Answer
                                                </span>
                                            @endif
                                        </div>
                                        <div class="prose dark:prose-invert max-w-none">
                                            {!! $comment->content !!}
                                        </div>
                                        <div class="flex items-center gap-4 mt-4 text-sm">
                                            <button class="flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                </svg>
                                                {{ $comment->votes_count ?? 0 }}
                                            </button>
                                            <button class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400" onclick="toggleReply({{ $comment->id }})">
                                                Reply
                                            </button>
                                            @if(auth()->id() === $post->user_id && !$post->is_solved)
                                                <form action="{{ route('comment.accept', $comment->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-700">
                                                        Accept as Answer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Reply Form -->
                                        <div id="reply-form-{{ $comment->id }}" class="hidden mt-4">
                                            <form action="{{ route('post.comments.store', $post->slug) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <textarea name="content" rows="3" placeholder="Write your reply..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500" required></textarea>
                                                <div class="flex justify-end mt-2">
                                                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                                        Reply
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Replies -->
                                        @if($comment->replies->count() > 0)
                                            <div class="mt-4 pl-4 border-l-2 border-gray-200 dark:border-gray-700 space-y-4">
                                                @foreach($comment->replies as $reply)
                                                    <div class="flex gap-3">
                                                        <img src="{{ $reply->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) . '&background=4F46E5&color=fff' }}" 
                                                             alt="{{ $reply->user->name }}" 
                                                             class="w-8 h-8 rounded-full">
                                                        <div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <a href="{{ route('profile', $reply->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 text-sm">
                                                                    {{ $reply->user->name }}
                                                                </a>
                                                                <span class="text-gray-500 dark:text-gray-400 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <div class="text-gray-700 dark:text-gray-300 text-sm">
                                                                {!! $reply->content !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Post Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Post Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Views</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $post->views_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Votes</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $post->votes_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Comments</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $post->comments->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Author Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Author</h3>
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=4F46E5&color=fff' }}" 
                             alt="{{ $post->user->name }}" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <a href="{{ route('profile', $post->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $post->user->name }}
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $post->user->reputation ?? 0 }} reputation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleReply(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    form.classList.toggle('hidden');
}

function vote(direction) {
    // API call for voting would go here
    console.log('Vote:', direction);
}

function bookmark() {
    // API call for bookmarking would go here
    console.log('Bookmark');
}

function share() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $post->title }}',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}
</script>
@endpush
@endsection
