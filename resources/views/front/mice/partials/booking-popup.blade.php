@php
$isEn = app()->getLocale() === 'en';
$title = $isEn ? ($package->title_en ?: $package->title) : $package->title;

$numberLocale = $isEn ? 'en-US' : 'id-ID';

$i18n = [
'title' => $isEn ? 'Booking Form' : 'Booking Form',
'subtitle' => $isEn ? 'Fill in your details to continue payment.' : 'Lengkapi data untuk lanjut pembayaran.',
'close' => $isEn ? 'Close' : 'Tutup',

'tier_selected' => $isEn ? 'Selected Tier' : 'Tier Dipilih',
'price' => $isEn ? 'Price' : 'Harga',

'full_name' => $isEn ? 'Full Name' : 'Nama Lengkap',
'name_ph' => $isEn ? 'Full name' : 'Nama lengkap',
'departure_date' => $isEn ? 'Departure Date' : 'Tanggal Keberangkatan',
'email' => 'Email',
'wa' => $isEn ? 'WhatsApp' : 'WhatsApp',
'wa_ph' => $isEn ? 'WhatsApp number' : '08xxxxxxxxxx',

'participants' => $isEn ? 'Participants' : 'Jumlah Peserta',

'promo_code' => $isEn ? 'Promo Code' : 'Kode Promo',
'promo_ph' => $isEn ? 'Example: LIBURAN50' : 'Contoh: LIBURAN50',
'apply' => $isEn ? 'Apply' : 'Gunakan',
'processing' => $isEn ? 'Processing...' : 'Memproses...',
'applied' => $isEn ? 'Applied' : 'Dipakai',

'total_pay' => $isEn ? 'Total Payment' : 'Total Bayar',
'pay_now' => $isEn ? 'Pay Now' : 'Bayar Sekarang',

// JS messages (fallback kalau backend gak kirim message)
'enter_promo' => $isEn ? 'Enter promo code.' : 'Masukkan kode promo.',
'invalid_promo' => $isEn ? 'Invalid promo code.' : 'Kode promo tidak valid.',
'promo_applied' => $isEn ? 'Promo applied.' : 'Promo berhasil dipakai.',
'promo_failed' => $isEn ? 'Failed to validate promo.' : 'Gagal validasi promo.',
'booking_failed' => $isEn ? 'Booking failed. Check your input.' : 'Booking gagal. Cek input.',
'no_redirect' => $isEn ? 'Booking created, but redirect not found.' : 'Booking berhasil, tapi redirect tidak ditemukan.',
'network_error' => $isEn ? 'Network error occurred.' : 'Terjadi kesalahan jaringan.',
];
@endphp

<div x-data="miceBooking(
  @js($title),
  @js($package->slug),
  {{ $package->id }},
  @js($numberLocale),
  @js($i18n)
)"
  x-on:open-booking.window="open($event)"
  x-cloak>


  <!-- Overlay -->
  <div x-show="isOpen"
    class="fixed inset-0 flex items-start justify-center bg-black/50 px-3 pb-4 pt-28 sm:pt-32"
    style="z-index: 9999;"
    x-transition.opacity
    @click.self="close()">

    <!-- Card (SAMA TOUR) -->
    <div x-show="isOpen"
      class="w-full max-w-md sm:max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden"
      style="max-height: calc(100vh - 160px);"
      x-transition.scale
      @keydown.escape.window="close()">

      <!-- Header (SAMA TOUR) -->
      <div class="flex items-start justify-between border-b border-slate-200 px-4 py-3">
        <div>
          <h2 class="text-sm sm:text-base font-extrabold text-slate-900">{{ $i18n['title'] }}</h2>
          <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">{{ $i18n['subtitle'] }}</p>

        </div>

        <button type="button"
          class="h-8 w-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700"
          @click="close()"
          aria-label="{{ $i18n['close'] }}">

          ✕
        </button>
      </div>

      <!-- Body (scroll internal + ADA ruang bawah biar gak mepet) -->
      <div class="px-4 py-3 pb-6 space-y-3 overflow-y-auto"
        style="max-height: calc(100vh - 160px - 56px);">

        <!-- Tier info ringkas -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['tier_selected'] }}</div>
              <div class="mt-1 font-extrabold text-slate-900" x-text="tier ? tier.label : '-'"></div>
            </div>
            <div class="text-right">
              <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['price'] }}</div>
              <div class="mt-1 font-extrabold" style="color:#0194F3">
                Rp <span x-text="tier ? tier.price.toLocaleString('id-ID') : '0'"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Inputs (SAMA TOUR) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['full_name'] }}</label>
            <input type="text" x-model="form.name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="{{ $i18n['name_ph'] }}">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['departure_date'] }}</label>
            <input type="date" x-model="form.departure_date"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['email'] }}</label>

            <input type="email" x-model="form.email"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="nama@email.com">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">WhatsApp</label>
            <input type="text" x-model="form.phone"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="08xxxxxxxxxx">
          </div>
        </div>

        <!-- Participants (SAMA TOUR style) -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['participants'] }}</div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
            <input type="number"
              class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              min="1"
              x-model.number="count"
              @input="recalc()">

            <div class="text-slate-700">
              x <span x-text="tier ? tier.price.toLocaleString(numberLocale) : '0'"></span>
              = <span class="font-extrabold">Rp <span x-text="totalFormatted"></span></span>
            </div>
          </div>
        </div>

        <!-- Promo (SAMA TOUR) -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['promo_code'] }}</div>

          <div class="mt-2 flex flex-col sm:flex-row gap-2">
            <input type="text" x-model="promo.code"
              class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="{{ $i18n['promo_ph'] }}">

            <button type="button"
              class="rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
              style="background:#0194F3"
              onmouseover="if(!this.disabled) this.style.background='#0186DB'"
              onmouseout="this.style.background='#0194F3'"
              @click="applyPromo()"
              :disabled="promoLocked || promoLoading || loading">
              <span x-show="!promoLocked && !promoLoading">{{ $i18n['apply'] }}</span>
              <span x-show="promoLoading">{{ $i18n['processing'] }}</span>
              <span x-show="promoLocked">{{ $i18n['applied'] }}</span>
            </button>
          </div>

          <div class="mt-2 text-sm"
            :class="promo.ok ? 'text-emerald-700' : 'text-rose-600'"
            x-text="promo.message"></div>
        </div>

        <!-- Total Bayar + Action (SAMA TOUR, TANPA TOMBOL BATAL) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <div class="text-xs text-slate-500">{{ $i18n['total_pay'] }}</div>
            <div class="text-lg sm:text-xl font-extrabold" style="color:#0194F3">
              Rp <span x-text="totalFormatted"></span>
            </div>
          </div>

          <button type="button"
            class="w-full sm:w-auto rounded-xl px-5 py-2.5 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
            style="background:#0194F3"
            onmouseover="if(!this.disabled) this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'"
            :disabled="loading"
            @click="submitBooking()">
            <span x-show="!loading">{{ $i18n['pay_now'] }}</span>
            <span x-show="loading">{{ $i18n['processing'] }}</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function miceBooking(packageTitle, packageSlug, productId, numberLocale, i18n) {
    return {

      isOpen: false,
      loading: false,

      tier: null,
      count: 1,
      total: 0,

      form: {
        name: '',
        email: '',
        phone: '',
        departure_date: ''
      },

      promo: {
        code: '',
        id: null,
        ok: false,
        message: ''
      },
      promoLocked: false,
      promoLoading: false,

      open(e) {
        this.tier = e.detail.tier;
        this.count = 1;
        this.total = 0;
        this.promo = {
          code: '',
          id: null,
          ok: false,
          message: ''
        };
        this.promoLocked = false;
        this.promoLoading = false;
        this.recalc();
        this.isOpen = true;

        const params = new URLSearchParams(window.location.search);
        const promoQ = (params.get('promo') || '').trim();
        if (promoQ) {
          this.promo.code = promoQ;
          this.applyPromo();
        }
      },

      close() {
        this.isOpen = false;
      },

      get totalFormatted() {
        return (this.total || 0).toLocaleString(numberLocale);
      },

      recalc() {
        if (!this.tier) return;
        if (this.count < 1) this.count = 1;

        // kalau promo sudah dipakai, jangan overwrite total diskon
        if (this.promoLocked) return;

        this.total = this.count * this.tier.price;
      },

      async applyPromo() {
        if (this.promoLocked || this.promoLoading || this.loading) return;

        const code = (this.promo.code || '').trim();
        if (!code) {
          this.promo = {
            ...this.promo,
            ok: false,
            id: null,
            message: i18n.enter_promo
          };
          return;
        }

        this.promoLoading = true;

        try {
          const r = await fetch("/promo/validate", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
              code,
              price: this.total,
              email: (this.form.email || '').trim()
            })
          });

          const data = await r.json();

          if (!r.ok || !data.valid) {
            this.promo = {
              code,
              id: null,
              ok: false,
              message: data.message || i18n.invalid_promo
            };
            this.promoLoading = false;
            return;
          }

          // kalau backend kirim final_price / discount, ikuti (biar konsisten)
          if (typeof data.final_price !== 'undefined') {
            this.total = parseInt(data.final_price || 0, 10);
          }

          this.promo = {
            code,
            id: data.promo_id || data.id || null,
            ok: true,
            message: data.message || i18n.promo_applied
          };

          this.promoLocked = true;
        } catch (err) {
          console.error(err);
          this.promo = {
            ...this.promo,
            ok: false,
            id: null,
            message: i18n.promo_failed
          };
        } finally {
          this.promoLoading = false;
        }
      },

      async submitBooking() {
        if (this.loading) return;
        if (!this.tier) return;

        this.loading = true;

        try {
          const payload = {
            name: this.form.name,
            email: this.form.email,
            phone: this.form.phone,
            departure_date: this.form.departure_date,
            tier_id: this.tier.id,
            participants: this.count,
            promo_id: this.promo.id ? Number(this.promo.id) : null,
          };

          const r = await fetch(`/mice/${packageSlug}/draft-booking`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
          });

          if (!r.ok) {
            let msg = i18n.booking_failed;
            try {
              const err = await r.json();
              if (err?.error) msg = err.error;
              else if (err?.message) msg = err.message;
              else if (err?.errors) {
                const k = Object.keys(err.errors)[0];
                if (k && err.errors[k]?.[0]) msg = err.errors[k][0];
              }
            } catch (e) {}
            alert(msg);
            this.loading = false;
            return;
          }

          const res = await r.json();
          if (res?.redirect) {
            window.location.href = res.redirect;
            return;
          }

          alert(i18n.no_redirect);
        } catch (err) {
          console.error(err);
          alert(i18n.network_error);
        } finally {
          this.loading = false;
        }
      }
    }
  }
</script>