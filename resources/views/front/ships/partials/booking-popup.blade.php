@php
$isEn = app()->getLocale() === 'en';
$title = $isEn ? ($package->title_en ?: $package->title) : $package->title;

$i18n = [
'title' => $isEn ? 'Ship Rental Booking' : 'Booking Sewa Kapal',
'subtitle' => $isEn ? 'Fill the details to continue payment.' : 'Lengkapi data untuk lanjut pembayaran.',
'close' => $isEn ? 'Close' : 'Tutup',

'choice' => $isEn ? 'Selected' : 'Pilihan',
'full_name' => $isEn ? 'Full Name' : 'Nama Lengkap',
'rental_date' => $isEn ? 'Rental Date' : 'Tanggal Sewa',
'email' => 'Email',
'whatsapp' => 'WhatsApp',

'qty' => $isEn ? 'Quantity (Qty)' : 'Jumlah (Qty)',
'total' => $isEn ? 'Total' : 'Total',

'promo_code' => $isEn ? 'Promo Code' : 'Kode Promo',
'promo_placeholder' => $isEn ? 'Example: LIBURAN50' : 'Contoh: LIBURAN50',
'apply' => $isEn ? 'Apply' : 'Gunakan',
'processing' => $isEn ? 'Processing...' : 'Memproses...',
'used' => $isEn ? 'Applied' : 'Dipakai',

'total_pay' => $isEn ? 'Total Payment' : 'Total Bayar',
'pay_now' => $isEn ? 'Pay Now' : 'Bayar Sekarang',


'whatsapp' => $isEn ? 'WhatsApp' : 'WhatsApp',
'booking_failed' => $isEn ? 'Booking failed. Check your input.' : 'Gagal booking. Cek input.',
'booking_failed_retry' => $isEn ? 'Booking failed. Please try again.' : 'Gagal booking. Coba lagi.',
'promo_failed' => $isEn ? 'Failed to apply promo.' : 'Gagal memproses promo.',

'promo_already_applied' => $isEn ? 'Promo already applied for this booking.' : 'Promo sudah digunakan untuk booking ini.',
'enter_promo' => $isEn ? 'Enter promo code.' : 'Masukkan kode promo.',
'invalid_promo' => $isEn ? 'Invalid promo code.' : 'Promo tidak valid.',
'discount_applied' => $isEn ? 'Discount applied.' : 'Diskon diterapkan.',
'required_fields' => $isEn ? 'Name, email, WhatsApp, and rental date are required.' : 'Nama, email, WhatsApp, dan tanggal sewa wajib diisi.',
'server_unreachable' => $isEn ? 'Failed to reach server.' : 'Gagal menghubungi server.',
];
@endphp


<div x-data="shipBooking(
  @js(preg_replace('/\D+/', '', $siteSettings['footer_whatsapp'] ?? '6281234567890')),
  @js($title),
  @js($package->slug),
  @js(route('ship.show', $package->slug)),
  @js($i18n)
)"
  x-on:open-ship-booking.window="open($event)"
  x-cloak>

  <div x-show="isOpen"
    class="fixed inset-0 flex items-start justify-center bg-black/50 px-3 pb-4 pt-28 sm:pt-32"
    style="z-index: 9999;"
    x-transition.opacity>

    <div x-show="isOpen"
      class="w-full max-w-md sm:max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden"
      style="max-height: calc(100vh - 160px);"
      x-transition.scale>

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

      <div class="px-4 py-3 space-y-3 overflow-y-auto"
        style="max-height: calc(100vh - 160px - 56px);">

        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-xs font-extrabold text-slate-600">{{ $i18n['choice'] }}</div>
          <div class="mt-1 font-extrabold text-slate-900" x-text="tier ? tier.label_text : '-'"></div>
          <div class="mt-1 text-sm">
            Rp <span class="font-extrabold" x-text="tier ? Number(tier.price||0).toLocaleString('id-ID') : '0'"></span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['full_name'] }}</label>
            <input type="text" x-model="form.name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['rental_date'] }}</label>
            <input type="date" x-model="form.departure_date"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['email'] }}</label>
            <input type="email" x-model="form.email"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['whatsapp'] }}</label>
            <input type="text" x-model="form.phone"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['qty'] }}</div>
          <div class="mt-2 flex items-center gap-2 text-sm">
            <input type="number"
              class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-center text-sm"
              x-model.number="qty"
              min="1"
              @input="recalc()">
            <div class="text-slate-700">
              {{ $i18n['total'] }}: <span class="font-extrabold">Rp <span x-text="totalFormatted"></span></span>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['promo_code'] }}</div>

          <div class="mt-2 flex flex-col sm:flex-row gap-2">
            <input type="text" x-model="promo.code"
              class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
              placeholder="{{ $i18n['promo_placeholder'] }}">

            <button type="button"
              class="rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
              style="background:#0194F3"
              @click="applyPromo()"
              :disabled="promoLocked || promoLoading || loading">
              <span x-show="!promoLocked && !promoLoading">Gunakan</span>
              <span x-show="promoLoading">Memproses...</span>
              <span x-show="promoLocked">Dipakai</span>
            </button>
          </div>

          <div class="mt-2 text-sm"
            :class="promo.ok ? 'text-emerald-700' : 'text-rose-600'"
            x-text="promo.message"></div>
        </div>

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
            :disabled="loading"
            @click="submitBooking()">
            <span x-show="!loading">{{ $i18n['pay_now'] }}</span>
            <span x-show="loading">Memproses...</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function shipBooking(waAdmin, packageTitle, packageSlug, packageUrl, i18n) {

    return {
      isOpen: false,
      loading: false,

      tier: null,
      qty: 1,
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
        this.qty = 1;
        this.promo = {
          code: '',
          id: null,
          ok: false,
          message: ''
        };
        this.promoLocked = false;
        this.promoLoading = false;
        this.recalc();
        const params = new URLSearchParams(window.location.search);
        const promoQ = (params.get('promo') || '').trim();
        if (promoQ) {
          this.promo.code = promoQ;
          this.applyPromo();
        }

        this.isOpen = true;
      },

      close() {
        this.isOpen = false;
      },

      recalc() {
        if (!this.tier) return;
        if (this.qty < 1) this.qty = 1;
        if (this.promoLocked) return;
        this.total = (Number(this.tier.price || 0) * Number(this.qty || 1));
      },

      get totalFormatted() {
        return (this.total || 0).toLocaleString('id-ID');
      },

      async applyPromo() {
        if (this.promoLocked) {
          this.promo = {
            ...this.promo,
            message: i18n.promo_already_applied
          };
          return;
        }
        if (this.promoLoading) return;

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

          const res = await r.json();
          this.promoLoading = false;

          if (!res.valid) {
            this.promo = {
              code,
              ok: false,
              id: null,
              message: res.message || i18n.invalid_promo
            };
            return;
          }

          this.promo = {
            code,
            ok: true,
            id: res.promo_id,
            message: res.message || i18n.used
          };
          this.promoLocked = true;

          // total baru dari server
          this.total = Number(res.final_price || this.total);

        } catch (e) {
          this.promoLoading = false;
          this.promo = {
            ...this.promo,
            ok: false,
            id: null,
            message: i18n.server_unreachable
          };
        }
      },

      async submitBooking() {
        if (!this.tier) return;

        const payload = {
          name: this.form.name,
          email: this.form.email,
          phone: this.form.phone,
          departure_date: this.form.departure_date,
          tier_id: this.tier.id,
          qty: this.qty,
          promo_id: this.promo.id
        };

        this.loading = true;

        try {
          const r = await fetch(`/sewa-kapal/${packageSlug}/draft-booking`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
          });

          const res = await r.json();
          this.loading = false;

          if (!r.ok) {
            alert(res.error || i18n.booking_failed);
            return;
          }

          window.location.href = res.redirect;

        } catch (e) {
          this.loading = false;
          alert(i18n.booking_failed_retry);
        }
      }
    }
  }
</script>