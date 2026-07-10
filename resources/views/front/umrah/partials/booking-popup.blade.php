@php
$isEn = app()->getLocale() === 'en';
$title = $isEn ? (($package->title_en ?? null) ?: ($package->title ?? '')) : ($package->title ?? '');

$i18n = [
'title' => $isEn ? 'Umrah Booking Form' : 'Booking Form',
'subtitle' => $isEn ? 'Fill the details to continue payment.' : 'Lengkapi data untuk lanjut pembayaran.',
'close' => $isEn ? 'Close' : 'Tutup',

'choice' => $isEn ? 'Selected' : 'Dipakai',
'full_name' => $isEn ? 'Full Name' : 'Nama Lengkap',
'booking_date' => $isEn ? 'Booking Date' : 'Tanggal Booking',
'email' => 'Email',
'whatsapp' => 'WhatsApp',
'qty' => $isEn ? 'Participants' : 'Jumlah Peserta',

'promo' => $isEn ? 'Promo Code' : 'Kode Promo',
'use' => $isEn ? 'Apply' : 'Gunakan',
'processing' => $isEn ? 'Processing...' : 'Memproses...',

'total' => $isEn ? 'Total' : 'Total Bayar',
'pay_now' => $isEn ? 'Pay Now' : 'Bayar Sekarang',
];
@endphp


<div x-data="umrahBooking(
  @js(preg_replace('/\D+/', '', $siteSettings['footer_whatsapp'] ?? '6281234567890')),
  @js($package->title),
  @js($package->slug),
  @js(route('umrah.show', $package->slug)),
  {{ $package->id }}
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
          <h2 class="text-sm sm:text-base font-extrabold text-slate-900">{{ $i18n['title'] }}</h2>
          <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">{{ $i18n['subtitle'] }}</p>
        </div>

        <button type="button"
          class="h-8 w-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700"
          @click="close()"
          aria-label="Tutup">
          ✕
        </button>
      </div>

      <!-- Body -->
      <div class="px-4 py-3 space-y-3 overflow-y-auto"
        style="max-height: calc(100vh - 160px - 56px);">

        <!-- Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['full_name'] }}</label>
            <input type="text" x-model="form.name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              placeholder="Nama lengkap">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">{{ $i18n['booking_date'] }}</label>
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

        <!-- Participants -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">{{ $i18n['qty'] }}</div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
            <input type="number"
              class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              x-model.number="count"
              @input="recalc()">

            <div class="text-slate-700">
              x <span x-text="tier ? Number(tier.price||0).toLocaleString('id-ID') : '0'"></span>
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
              placeholder="{{ $isEn ? 'Example: HOLIDAY50' : 'Contoh: LIBURAN50' }}">

            <button type="button"
              class="rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
              style="background:#0194F3"
              onmouseover="if(!this.disabled) this.style.background='#0186DB'"
              onmouseout="this.style.background='#0194F3'"
              @click="applyPromo()"
              :disabled="promoLocked || promoLoading || loading">
              <span x-show="!promoLocked && !promoLoading">{{ $isEn ? 'Apply' : 'Gunakan' }}</span>
              <span x-show="promoLoading">{{ $isEn ? 'Processing...' : 'Memproses...' }}</span>
              <span x-show="promoLocked">{{ $isEn ? 'Selected' : 'Dipakai' }}</span>
            </button>
          </div>

          <div class="mt-2 text-sm"
            :class="promo.ok ? 'text-emerald-700' : 'text-rose-600'"
            x-text="promo.message"></div>
        </div>


        <!-- Total + Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <div class="text-xs text-slate-500">{{ $i18n['total'] }}</div>
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
  function umrahBooking(waAdmin, packageTitle, packageSlug, packageUrl, productId) {

    return {
      isOpen: false,
      loading: false,

      tier: null,
      count: 1,
      total: 0,

      waAdmin,
      packageTitle,
      packageSlug,
      packageUrl,

      form: {
        name: '',
        email: '',
        phone: '',
        booking_date: ''
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
        const params = new URLSearchParams(window.location.search);
        const promoQ = (params.get('promo') || '').trim();
        if (promoQ) {
          this.promo.code = promoQ;
          this.applyPromo();
        }

        this.isOpen = true;
        if (!this.form.booking_date) {
          const d = new Date();
          const yyyy = d.getFullYear();
          const mm = String(d.getMonth() + 1).padStart(2, '0');
          const dd = String(d.getDate()).padStart(2, '0');
          this.form.booking_date = `${yyyy}-${mm}-${dd}`;
        }

      },

      close() {
        this.isOpen = false;
      },

      recalc() {
        if (!this.tier) return;
        if (this.count < 1) this.count = 1;

        if (this.promoLocked) return;
        this.total = this.count * Number(this.tier.price || 0);
      },

      get totalFormatted() {
        return (this.total || 0).toLocaleString('id-ID');
      },

      async applyPromo() {
        const promoI18n = {
          alreadyApplied: @js($isEn ? 'Promo already applied for this booking.' : 'Promo sudah digunakan untuk booking ini.'),
          enterCode: @js($isEn ? 'Enter promo code.' : 'Masukkan kode promo.'),
          invalid: @js($isEn ? 'Invalid promo code.' : 'Promo tidak valid.'),
          applied: @js($isEn ? 'Discount applied.' : 'Diskon diterapkan.'),
          serverFail: @js($isEn ? 'Failed to reach server.' : 'Gagal menghubungi server.'),
        };

        if (this.promoLocked) {
          this.promo = {
            ...this.promo,
            message: promoI18n.alreadyApplied
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
            message: promoI18n.enterCode
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
              message: res.message || promoI18n.invalid
            };
            return;
          }

          this.total = Number(res.final_price || this.total);
          this.promo = {
            ...this.promo,
            ok: true,
            id: res.promo_id,
            message: promoI18n.applied
          };
          this.promoLocked = true;

        } catch (e) {
          this.promoLoading = false;
          this.promo = {
            ...this.promo,
            ok: false,
            id: null,
            message: promoI18n.serverFail
          };
        }
      },


      async submitBooking() {
        if (this.loading) return;
        this.loading = true;

        try {
          const payload = {
            type: "umrah",
            product_id: productId,
            name: this.form.name,
            email: this.form.email,
            phone: this.form.phone,
            booking_date: this.form.booking_date,
            participants: this.count,
            promo_id: this.promo.id ? Number(this.promo.id) : null,
            frontend_total: this.total,
            tier_id: this.tier ? this.tier.id : null,
          };

          const r = await fetch(`/umrah/${packageSlug}/draft-booking`, {
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
            alert('Booking gagal. Cek lagi data yang diisi / hubungi admin.');
            this.loading = false;
            return;
          }

          const res = await r.json();
          if (res?.redirect) {
            window.location.href = res.redirect;
            return;
          }

          this.loading = false;

        } catch (err) {
          console.error(err);
          alert('Terjadi kesalahan jaringan.');
          this.loading = false;
        }
      },

    }
  }
</script>