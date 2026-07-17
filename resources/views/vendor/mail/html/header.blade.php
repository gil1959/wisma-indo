<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (isset($siteSettings['site_logo']) && $siteSettings['site_logo'] != '')
<img src="{{ asset($siteSettings['site_logo']) }}" class="logo" alt="{{ $slot }}">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
