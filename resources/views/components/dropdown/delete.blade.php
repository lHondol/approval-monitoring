@php
    $formId = 'delete-' . $id;
@endphp

@if (!$permission || auth()->user()->hasPermissionTo($permission))
    <a href="#"
       class="item"
       onclick="event.preventDefault(); document.getElementById('{{ $formId }}').submit();">
        {{ $label }}
    </a>

    <form id="{{ $formId }}"
          action="{{ $route }}"
          method="POST"
          style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endif