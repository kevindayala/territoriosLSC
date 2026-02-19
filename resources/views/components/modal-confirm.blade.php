@props(['name', 'title', 'content', 'footer', 'maxWidth' => '2xl'])

<x-modal :name="$name" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4">
        <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            {{ $content }}
        </div>
    </div>

    <div
        class="flex flex-row justify-end px-6 py-4 bg-gray-50 dark:bg-gray-800 text-right border-t border-gray-100 dark:border-gray-700">
        {{ $footer }}
    </div>
</x-modal>