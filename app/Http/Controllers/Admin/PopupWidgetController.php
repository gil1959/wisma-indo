<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopupWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PopupWidgetController extends Controller
{
    public function edit()
    {
        $widget = PopupWidget::first() ?? PopupWidget::create([
            'is_enabled' => 0,
            'name' => 'Default Popup',
            'title' => 'Promo Spesial!',
            'body_format' => 'html',
'body_html' => '<p>Dapatkan promo terbaik hari ini.</p>',
'body_text' => null,
            'include_paths' => ['/'],
            'exclude_paths' => [],
            'show_on_mobile' => 1,
            'show_on_desktop' => 1,
            'delay_seconds' => 2,
            'frequency' => 'once_per_day',
        ]);

        return view('admin.settings.popup-widget', compact('widget'));
    }

    public function update(Request $request)
    {
        $widget = PopupWidget::firstOrFail();

        $data = $request->validate([
    'is_enabled' => ['nullable'],
    'name' => ['required', 'string', 'max:120'],
    'title' => ['nullable', 'string', 'max:160'],

    // NEW
    'body_format' => ['required', 'in:html,text'],
    'body_html' => ['nullable', 'string'],
    'body_text' => ['nullable', 'string'],

    'image' => ['nullable', 'image', 'max:2048'],
    'primary_button_text' => ['nullable', 'string', 'max:60'],
    'primary_button_link' => ['nullable', 'string', 'max:255'],
    'secondary_button_text' => ['nullable', 'string', 'max:60'],
    'secondary_button_link' => ['nullable', 'string', 'max:255'],
    'include_paths_text' => ['nullable', 'string'],
    'exclude_paths_text' => ['nullable', 'string'],
    'show_on_mobile' => ['nullable'],
    'show_on_desktop' => ['nullable'],
    'delay_seconds' => ['nullable', 'integer', 'min:0', 'max:120'],
    'frequency' => ['required', 'in:always,once_per_session,once_per_day'],
    'start_at' => ['nullable', 'date'],
    'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
]);


        $include = $this->linesToArray($data['include_paths_text'] ?? '');
        $exclude = $this->linesToArray($data['exclude_paths_text'] ?? '');

        $payload = [
            'is_enabled' => $request->boolean('is_enabled'),
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'body_format' => $data['body_format'],
'body_html' => $data['body_format'] === 'html' ? ($data['body_html'] ?? null) : null,
'body_text' => $data['body_format'] === 'text' ? ($data['body_text'] ?? null) : null,
            'primary_button_text' => $data['primary_button_text'] ?? null,
            'primary_button_link' => $data['primary_button_link'] ?? null,
            'secondary_button_text' => $data['secondary_button_text'] ?? null,
            'secondary_button_link' => $data['secondary_button_link'] ?? null,
            'include_paths' => $include,
            'exclude_paths' => $exclude,
            'show_on_mobile' => $request->boolean('show_on_mobile'),
            'show_on_desktop' => $request->boolean('show_on_desktop'),
            'delay_seconds' => (int)($data['delay_seconds'] ?? 0),
            'frequency' => $data['frequency'],
            'start_at' => $data['start_at'] ?? null,
            'end_at' => $data['end_at'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($widget->image_path) {
                $old = str_replace('/storage/', '', $widget->image_path);
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('image')->store('popup-widgets', 'public');
            $payload['image_path'] = '/storage/' . $path;
        }

        $widget->update($payload);

        return redirect()
            ->route('admin.settings.popup.edit')
            ->with('success', 'Popup widget berhasil disimpan.');
    }

   private function linesToArray(string $text): array
{
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $lines = array_map(function ($v) {
        $v = trim((string) $v);
        if ($v === '') return '';

        // Normalisasi: selalu diawali "/"
        if ($v[0] !== '/') $v = '/' . $v;

        // buang trailing slash (kecuali root)
        if (strlen($v) > 1) {
            $v = rtrim($v, '/');
        }

        return $v;
    }, $lines ?: []);

    $lines = array_values(array_filter($lines, fn($v) => $v !== ''));
    return $lines;
}

}
