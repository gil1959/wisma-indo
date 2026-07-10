@props(['errors'])

@if ($errors->any())
<div {{ $attributes }}>
    @php $isEn = app()->getLocale() === 'en'; @endphp

    <div class="font-medium text-red-600">
        {{ $isEn ? 'Whoops! Something went wrong.' : 'Ups! Ada yang salah.' }}
    </div>


    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif