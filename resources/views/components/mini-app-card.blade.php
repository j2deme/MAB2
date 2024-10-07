@props(['title', 'description', 'route', 'icon' => 'barricade','color' => 'yellow'])

<a href="{{ route($route) }}"
  class="flex items-center p-4 bg-white rounded-lg shadow-md group dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
  <div
    class="p-3 mr-4 text-{{ $color }}-500 bg-{{ $color }}-100 rounded-full dark:text-{{ $color }}-100 dark:bg-{{ $color }}-500 group-hover:bg-{{ $color }}-500">
    <x-icon name="{{ $icon }}" class="w-5 h-5 mr-1" />
  </div>
  <div>
    <p class="mb-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $title }}</p>
    <p class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $description }}
    </p>
  </div>
</a>