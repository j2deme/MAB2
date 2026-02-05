@php
/**
* Compatibility wrapper: Livewire default layout points to
* 'components.layouts.app' but this project uses 'layouts.app'.
* This view simply includes the existing layout and forwards
* the `$slot` variable so page components render correctly.
*/
@endphp

@include('layouts.app', ['slot' => $slot ?? ''])