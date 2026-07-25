<?php
$dashboard = file_get_contents('resources/views/front/user/dashboard.blade.php');
$profile = file_get_contents('resources/views/front/user/profile.blade.php');

// Extract sidebar (from <div class="w-full lg:w-1/3 xl:w-1/4 flex flex-col gap-6"> to the closing div before RIGHT CONTENT)
$sidebarStart = strpos($dashboard, '<div class="w-full lg:w-1/3 xl:w-1/4 flex flex-col gap-6">');
$sidebarEnd = strpos($dashboard, '{{-- RIGHT CONTENT --}}');
$sidebar = substr($dashboard, $sidebarStart, $sidebarEnd - $sidebarStart);

// Extract form
$formStart = strpos($profile, '<div class="mb-8">');
$formEnd = strpos($profile, '</form>');
$formEnd = strpos($profile, '</div>', $formEnd);
$formEnd = strpos($profile, '</div>', $formEnd + 1);
$form = '<div class="w-full lg:w-2/3 xl:w-3/4">' . "\n    " . substr($profile, $formStart, $formEnd - $formStart + 6) . "\n</div>";

$newProfile = "@extends('layouts.front')\n@section('title', 'Edit Profil')\n\n@section('content')\n<div class=\"pt-24 pb-20 min-h-screen bg-slate-50\">\n    <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8\">\n        <div class=\"flex flex-col lg:flex-row gap-8\">\n            \n            {{-- LEFT SIDEBAR --}}\n            " . trim($sidebar) . "\n\n            {{-- RIGHT CONTENT (FORM) --}}\n            " . trim($form) . "\n        </div>\n    </div>\n</div>\n@endsection";

file_put_contents('resources/views/front/user/profile.blade.php', $newProfile);
echo "Success";
