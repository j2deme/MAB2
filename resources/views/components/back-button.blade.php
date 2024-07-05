@props(['url' => null])

<a type="button" href="{{ ($url) ? $url : route('dashboard') }}"
  class="block px-3 py-2 text-sm font-semibold text-center text-white rounded-md shadow-sm bg-secondary-600 hover:bg-secondary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary-600">
  <x-phosphor.icons::regular.caret-left class="inline-block w-5 h-5 mr-2 -mt-1" />
  {{ __('Back') }}
</a>