@foreach($comments as $comment)
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <p class="text-gray-800 dark:text-gray-200 mb-2">{{ $comment->text }}</p>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                @if ($comment->createdBy)
                    Door <span class="font-medium">{{ $comment->createdBy->name }}</span>
                @endif
                @if ($comment->created_at)
                    op {{ $comment->created_at->toFormattedDateString() }}
                @endif
            </span>
        </div>

        {{-- Replies --}}
        @foreach($comment->replies as $reply)
            <div class="ml-8 mt-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow">
                    <p class="text-gray-800 dark:text-gray-200 mb-2">{{ $reply->text }}</p>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        @if ($reply->createdBy)
                            Door <span class="font-medium">{{ $reply->createdBy->name }}</span>
                        @endif
                        @if ($reply->created_at)
                            op {{ $reply->created_at->toFormattedDateString() }}
                        @endif
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
