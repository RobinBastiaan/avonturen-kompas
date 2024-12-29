<div class="mb-6">
    @foreach($categories as $groupName => $categoryList)
        <div class="mb-4">
            <h3 class="font-semibold text-gray-700">{{ $groupName }}</h3>

            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($categoryList as $category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100">
                        {{ $category['name'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
