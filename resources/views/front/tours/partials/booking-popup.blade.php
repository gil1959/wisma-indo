@php
$isEn = app()->getLocale() === 'en';
$flightInfo = $package->flight_info;
$title = $isEn ? ($package->title_en ?: $package->title) : $package->title;

$numberLocale = $isEn ? 'en-US' : 'id-ID';

$i18n = [
'participants' => $isEn ? 'Participants' : 'Jumlah Peserta',
'promo_code' => $isEn ? 'Promo Code' : 'Kode Promo',
'total_pay' => $isEn ? 'Total Payment' : 'Total Bayar',

'apply' => $isEn ? 'Apply' : 'Gunakan',
'processing' => $isEn ? 'Processing...' : 'Memproses...',
'applied' => $isEn ? 'Applied' : 'Dipakai',

'pay_now' => $isEn ? 'Pay Now' : 'Bayar Sekarang',

'promo_already_applied' => $isEn ? 'Promo already applied for this booking.' : 'Promo sudah digunakan untuk booking ini.',
'enter_promo' => $isEn ? 'Enter promo code.' : 'Masukkan kode promo.',
'invalid_promo' => $isEn ? 'Invalid promo code.' : 'Promo tidak valid.',
'discount_applied' => $isEn ? 'Discount applied.' : 'Diskon diterapkan.',
'server_unreachable' => $isEn ? 'Failed to reach server.' : 'Gagal menghubungi server.',
'booking_failed' => $isEn ? 'Booking failed. Check your input / contact admin.' : 'Booking gagal. Cek lagi data yang diisi / hubungi admin.',
'network_error' => $isEn ? 'Network error occurred.' : 'Terjadi kesalahan jaringan.',

'wa_ticket_prefix' => $isEn
? 'Hi, I would like to check the package price including flight tickets for:'
: 'Halo, saya mau cek harga paket dengan tiket pesawat untuk:',
];
@endphp

<div x-data="tourBooking(
  @js($flightInfo),
  @js(preg_replace('/\D+/', '', $siteSettings['footer_whatsapp'] ?? '6281234567890')),
  @js($title),
  @js($package->slug),
  @js(route('tour.show', $package->slug)),
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
    x-transition.opacity>

    <!-- Card -->
    <div x-show="isOpen"
      class="w-full max-w-md sm:max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden"
      style="max-height: calc(100vh - 160px);"
      x-transition.scale>

      <!-- Header -->
      <div class="flex items-start justify-between border-b border-slate-200 px-4 py-3">
        <div>
          <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Booking Form</h2>
          <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">
            {{ $isEn ? 'Fill in the details to continue payment.' : 'Lengkapi data untuk lanjut pembayaran.' }}
          </p>
        </div>

        <button type="button"
          class="h-8 w-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700"
          @click="close()"
          aria-label="{{ $isEn ? 'Close' : 'Tutup' }}">
          ✕
        </button>
      </div>

      <!-- Body (scroll internal) -->
      <div class="px-4 py-3 space-y-3 overflow-y-auto"
        style="max-height: calc(100vh - 160px - 56px);">

        <!-- Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Full Name' : 'Nama Lengkap' }}</label>
            <input type="text" x-model="form.name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="{{ $isEn ? 'Full name' : 'Nama lengkap' }}">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">
              {{ $isEn ? 'Departure Date' : 'Tanggal Keberangkatan' }}
            </label>
            <input type="date" x-model="form.departure_date"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">Email</label>
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

        <!-- Flight option -->
        <div x-show="flightInfo === 'not_included'" class="rounded-2xl border border-slate-200 p-3">
          <div class="font-extrabold text-slate-800 text-sm">
            {{ $isEn ? 'Flight Ticket Options' : 'Pilihan Tiket Pesawat' }}
          </div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input type="radio" name="flight_option" value="no_ticket" x-model="flightOption" class="h-4 w-4">
              <span>{{ $isEn ? 'Without Ticket' : 'Tanpa Tiket' }}</span>
            </label>

            <button type="button"
              class="w-full sm:w-auto rounded-xl px-3 py-2 text-xs sm:text-sm font-extrabold text-white shadow-sm"
              style="background:#0194F3"
              onmouseover="this.style.background='#0186DB'"
              onmouseout="this.style.background='#0194F3'"
              @click="openTicketWA()">
              {{ $isEn ? 'With Ticket (Ask price via WhatsApp)' : 'Dengan Tiket (Cek harga via WA)' }}
            </button>
          </div>
        </div>


        <!-- Participants -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">
            {{ $isEn ? 'Participants' : 'Jumlah Peserta' }}
          </div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
            <input type="number"
              class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              x-model.number="count"
              @input="recalc()">

            <div class="text-slate-700">
              x <span x-text="tier ? Number(tier.price || 0).toLocaleString(numberLocale) : '0'"></span>
              = <span class="font-extrabold">Rp <span x-text="totalFormatted"></span></span>
            </div>

          </div>
        </div>

        <!-- Promo -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">
            {{ $isEn ? 'Promo Code' : 'Kode Promo' }}
          </div>

          <div class="mt-2 flex flex-col sm:flex-row gap-2">
            <input type="text" x-model="promo.code"
              class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="Contoh: LIBURAN50">

            <button type="button"
              class="rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
              style="background:#0194F3"
              onmouseover="if(!this.disabled) this.style.background='#0186DB'"
              onmouseout="this.style.background='#0194F3'"
              @click="applyPromo()"
              :disabled="promoLocked || promoLoading || loading">
              <span x-show="!promoLocked && !promoLoading">{{ $isEn ? 'Apply' : 'Gunakan' }}</span>
              <span x-show="promoLoading">{{ $isEn ? 'Processing...' : 'Memproses...' }}</span>
              <span x-show="promoLocked">{{ $isEn ? 'Applied' : 'Dipakai' }}</span>
            </button>

          </div>

          <div class="mt-2 text-sm"
            :class="promo.ok ? 'text-emerald-700' : 'text-rose-600'"
            x-text="promo.message"></div>
        </div>

        <!-- Total + Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <div class="text-xs text-slate-500">
              {{ $isEn ? 'Total Payment' : 'Total Bayar' }}
            </div>
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
            <span x-show="!loading">{{ $isEn ? 'Pay Now' : 'Bayar Sekarang' }}</span>
            <span x-show="loading">{{ $isEn ? 'Processing...' : 'Memproses...' }}</span>
          </button>

        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function tourBooking(flightInfo, waAdmin, packageTitle, packageSlug, packageUrl, productId, numberLocale, i18n) {

    return {
      isOpen: false,
      loading: false,

      tier: null,
      count: 1,
      total: 0,

      flightInfo,
      flightOption: 'no_ticket',

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
        this.count = this.tier.is_custom ? 2 : this.tier.min_people;
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
        const dateQ = (params.get('date') || '').trim();
        if (dateQ) {
          this.form.departure_date = dateQ;
        }

        const promoQ = (params.get('promo') || '').trim();
        if (promoQ) {
          this.promo.code = promoQ;
          this.applyPromo();
        }
      },

      close() {
        this.isOpen = false;
      },

      recalc() {
        if (!this.tier) return;
        const min = this.tier.is_custom ? 2 : this.tier.min_people;
        const max = this.tier.max_people ?? 9999;
        if (this.count < min) this.count = min;
        if (this.count > max) this.count = max;


        this.total = this.count * this.tier.price;


        if (this.promoLocked) return;

      },

      get totalFormatted() {
        return (this.total || 0).toLocaleString(numberLocale);
      },


      openTicketWA() {
        const text =
          `${i18n.wa_ticket_prefix} ${packageTitle}\n\n${packageUrl}`;

        window.open(`https://wa.me/${waAdmin}?text=${encodeURIComponent(text)}`, '_blank');
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
              ...this.promo,
              ok: false,
              id: null,
              message: res.message || i18n.invalid_promo
            };
            return;
          }

          this.total = Number(res.final_price || this.total);
          this.promo = {
            ...this.promo,
            ok: true,
            id: res.promo_id,
            message: i18n.discount_applied
          };
          this.promoLocked = true;

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
        if (this.loading) return; // kunci: biar gak bisa submit berkali-kali
        this.loading = true;

        try {
          const payload = {
            type: "tour",
            product_id: productId,
            name: this.form.name,
            email: this.form.email,
            phone: this.form.phone,
            departure_date: this.form.departure_date,
            participants: this.count,
            promo_id: this.promo.id ? Number(this.promo.id) : null,
            frontend_total: this.total,
            flight_option: this.flightOption,
          };

          const r = await fetch(`/tours/${packageSlug}/draft-booking`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
          });

          if (!r.ok) {
            const text = await r.text();
            console.error('Draft booking failed', r.status, text);
            alert(i18n.booking_failed);
            this.loading = false;
            return;
          }

          let res = null;
          try {
            res = await r.json();
          } catch (e) {
            // server balikin HTML / non-JSON → jangan nge-freeze loading
            console.error('Invalid JSON response from draft-booking');
            alert(i18n.booking_failed);
            this.loading = false;
            return;
          }

          if (res?.redirect) {
            window.location.href = res.redirect;
            return;
          }

          // kalau gak redirect, unlock lagi
          this.loading = false;


        } catch (err) {
          console.error(err);
          alert(i18n.network_error);
          this.loading = false;
        }
      },

    }
  }
</script>