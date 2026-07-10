@php
  /** @var \App\Models\PopupWidget|null $popupWidget */
@endphp

@if(!empty($popupWidget) && $popupWidget->is_enabled)
  <div id="bw-popup-overlay" class="fixed inset-0 z-[9999] hidden items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/60" data-bw-popup-close></div>

    <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden">
      <button type="button"
              class="absolute right-3 top-3 h-9 w-9 rounded-full bg-white/90 border border-slate-200 flex items-center justify-center"
              aria-label="Close"
              data-bw-popup-close>
        ✕
      </button>

      @if($popupWidget->image_path)
        <img src="{{ $popupWidget->image_path }}" class="h-48 w-full object-cover" alt="popup image">
      @endif

      <div class="p-6">
        @if($popupWidget->title)
          <h3 class="text-xl font-extrabold text-slate-900">{{ $popupWidget->title }}</h3>
        @endif

       @if(($popupWidget->body_format ?? 'html') === 'text' && $popupWidget->body_text)
  <div class="mt-3 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
    {{ $popupWidget->body_text }}
  </div>
@elseif($popupWidget->body_html)
  <div class="mt-3 text-slate-700 text-sm leading-relaxed prose max-w-none">
    {!! $popupWidget->body_html !!}
  </div>
@endif


        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          @if($popupWidget->primary_button_text && $popupWidget->primary_button_link)
            <a href="{{ $popupWidget->primary_button_link }}"
               class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
               data-bw-popup-primary>
              {{ $popupWidget->primary_button_text }}
            </a>
          @endif

          @if($popupWidget->secondary_button_text && $popupWidget->secondary_button_link)
            <a href="{{ $popupWidget->secondary_button_link }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50"
               data-bw-popup-secondary>
              {{ $popupWidget->secondary_button_text }}
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var cfg = {
        id: {{ (int) $popupWidget->id }},
        include: @json($popupWidget->include_paths ?? []),
        exclude: @json($popupWidget->exclude_paths ?? []),
        showOnMobile: {{ $popupWidget->show_on_mobile ? 'true' : 'false' }},
        showOnDesktop: {{ $popupWidget->show_on_desktop ? 'true' : 'false' }},
        delaySeconds: {{ (int) ($popupWidget->delay_seconds ?? 0) }},
        frequency: @json($popupWidget->frequency ?? 'once_per_day'),
        startAt: @json(optional($popupWidget->start_at)->toIso8601String()),
        endAt: @json(optional($popupWidget->end_at)->toIso8601String()),
      };

      var overlay = document.getElementById('bw-popup-overlay');
      if (!overlay) return;

      function isMobile() {
        return window.matchMedia && window.matchMedia('(max-width: 767px)').matches;
      }

      function nowISO() {
        return new Date().toISOString();
      }

      function withinDateWindow() {
        var now = nowISO();
        if (cfg.startAt && now < cfg.startAt) return false;
        if (cfg.endAt && now > cfg.endAt) return false;
        return true;
      }

     function normalizePattern(p) {
  p = (p || '').trim();
  if (!p) return '';
  // pastiin selalu diawali "/"
  if (p[0] !== '/') p = '/' + p;
  // hilangin trailing spaces & normalize root
  if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
  return p;
}

function normalizePath(path) {
  path = (path || '/').trim();
  if (!path) path = '/';
  // remove trailing slash kecuali root
  if (path.length > 1 && path.endsWith('/')) path = path.slice(0, -1);
  return path;
}

/**
 * Rules:
 * 1) Kalau pattern mengandung "*" -> wildcard match (tapi "/x/*" juga match "/x")
 * 2) Kalau pattern tanpa "*" -> match exact ATAU prefix child:
 *    "/rentcar" match "/rentcar" dan "/rentcar/avanza"
 */
function pathMatches(pattern, rawPath) {
  var p = normalizePattern(pattern);
  var path = normalizePath(rawPath);

  if (!p) return false;

  // wildcard mode
  if (p.indexOf('*') !== -1) {
    // special: "/tour/*" harus match "/tour" juga
    if (p.endsWith('/*')) {
      var base = p.slice(0, -2); // buang "/*"
      if (path === base) return true;
    }

    var esc = p.replace(/[.+?^${}()|[\]\\]/g, '\\$&');
    // ubah "*" jadi ".*"
    var re = new RegExp('^' + esc.replace(/\*/g, '.*') + '$');
    return re.test(path);
  }

  // non-wildcard mode: exact or prefix children
  if (path === p) return true;
  if (p === '/') return path === '/';
  return path.startsWith(p + '/');
}

function isTargetPath() {
  var path = window.location.pathname || '/';

  var included = (cfg.include && cfg.include.length)
    ? cfg.include.some(function (pp) { return pathMatches(pp, path); })
    : true;

  var excluded = (cfg.exclude && cfg.exclude.length)
    ? cfg.exclude.some(function (pp) { return pathMatches(pp, path); })
    : false;

  return included && !excluded;
}


      function deviceAllowed() {
        var mobile = isMobile();
        if (mobile && !cfg.showOnMobile) return false;
        if (!mobile && !cfg.showOnDesktop) return false;
        return true;
      }

      function frequencyAllowed() {
        var key = 'bw_popup_' + cfg.id + '_shown';
        if (cfg.frequency === 'always') return true;

        if (cfg.frequency === 'once_per_session') {
          return !sessionStorage.getItem(key);
        }

        var stamp = localStorage.getItem(key);
        if (!stamp) return true;

        try {
          var last = new Date(stamp);
          var now = new Date();
          return !(last.getFullYear() === now.getFullYear() &&
                   last.getMonth() === now.getMonth() &&
                   last.getDate() === now.getDate());
        } catch (e) {
          return true;
        }
      }

      function markShown() {
        var key = 'bw_popup_' + cfg.id + '_shown';
        var stamp = new Date().toISOString();
        if (cfg.frequency === 'once_per_session') {
          sessionStorage.setItem(key, stamp);
          return;
        }
        if (cfg.frequency === 'once_per_day') {
          localStorage.setItem(key, stamp);
        }
      }

      function openPopup() {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        overlay.setAttribute('aria-hidden', 'false');
        markShown();
      }

      function closePopup() {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        overlay.setAttribute('aria-hidden', 'true');
      }

      overlay.querySelectorAll('[data-bw-popup-close]').forEach(function (el) {
        el.addEventListener('click', closePopup);
      });

      if (!withinDateWindow()) return;
      if (!isTargetPath()) return;
      if (!deviceAllowed()) return;
      if (!frequencyAllowed()) return;

      window.setTimeout(openPopup, Math.max(0, cfg.delaySeconds) * 1000);
    })();
  </script>
@endif
