@php
$isEn = app()->getLocale() === 'en';
$i18n = [
'title' => $isEn ? 'Hotel/Vila Booking' : 'Booking Hotel/Vila',
'name' => $isEn ? 'Name' : 'Nama',
'full_name' => $isEn ? 'Full name' : 'Nama lengkap',
'email' => 'Email',
'whatsapp' => $isEn ? 'WhatsApp' : 'WhatsApp',
'wa_placeholder' => $isEn ? 'WhatsApp number' : 'Nomor WhatsApp',
'promo_code' => $isEn ? 'Promo Code' : 'Kode Promo',
'promo_placeholder' => $isEn ? 'Enter promo code' : 'Masukkan kode promo',
'use' => $isEn ? 'Apply' : 'Gunakan',
'used' => $isEn ? 'Applied' : 'Dipakai',
'total_days' => $isEn ? 'Total Night' : 'Total Malam',
'total_price' => $isEn ? 'Total Price' : 'Total Harga',
'book_now' => $isEn ? 'Book Now' : 'Booking Sekarang',
'processing' => $isEn ? 'Processing...' : 'Memproses...',
'promo_already_used' => $isEn ? 'Promo has already been applied for this booking.' : 'Promo sudah digunakan untuk booking ini.',
'promo_empty' => $isEn ? 'Promo code is empty.' : 'Kode promo belum diisi.',
'pick_date_first' => $isEn ? 'Please select dates first.' : 'Pilih tanggal terlebih dahulu.',
'server_unreachable' => $isEn ? 'Failed to reach server.' : 'Gagal menghubungi server.',
'required_fields' => $isEn ? 'Name, email, and WhatsApp are required.' : 'Nama, email, dan WhatsApp wajib diisi.',
'required_dates' => $isEn ? 'Checkin & Checkout date are required.' : 'Checkin & Checkout date wajib diisi.',
];
@endphp


<div
  x-data="hotelBookingPopup({{ (int) $package->price_per_night }}, '{{ $package->slug }}')"
  x-on:open-hotel-booking.window="open($event.detail)"
  x-cloak>
  {{-- backdrop --}}
  <div
    x-show="isOpen"
    x-transition.opacity
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-[999]"
    @click.self="close()"
    style="display:none">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg relative border border-slate-200 shadow-2xl">

      {{-- HEADER --}}
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-extrabold text-slate-900 mb-0">{{ $i18n['title'] }}</h2>

        <button
          type="button"
          class="text-slate-400 hover:text-slate-700 text-2xl leading-none"
          @click="close()"
          aria-label="Close">×</button>
      </div>

      <div class="space-y-3">

        <div class="grid sm:grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-semibold text-slate-600">{{ $i18n['name'] }}</label>
            <input type="text" x-model="name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="{{ $i18n['full_name'] }}">
          </div>

          <div>
            <label class="text-xs font-semibold text-slate-600">Email</label>
            <input type="email" x-model="email"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="Email">
          </div>

          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-slate-600">WhatsApp</label>
            <input type="text" x-model="phone"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="{{ $i18n['wa_placeholder'] }}">
          </div>
        </div>

        {{-- Pickup/Return readonly (biar user tau yang kepilih) --}}
        <div class="grid sm:grid-cols-2 gap-3">
          <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <div class="text-xs text-slate-500">Checkin</div>
            <div class="text-sm font-bold text-slate-900" x-text="checkin_date || '-'"></div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <div class="text-xs text-slate-500">Checkout</div>
            <div class="text-sm font-bold text-slate-900" x-text="checkout_date || '-'"></div>
          </div>
        </div>

        <!-- Promo -->
        <div class="mt-4">
          <label class="text-xs font-semibold text-slate-600">{{ $i18n['promo_code'] }}</label>

          <div class="mt-2 flex gap-2">
            <input
              type="text"
              x-model.trim="promoCode"
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
              placeholder="{{ $i18n['promo_placeholder'] }}" />


            <button
              type="button"
              @click="applyPromo()"
              class="shrink-0 rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800 disabled:opacity-60"
              :disabled="promoLoading || promoLocked">
              <span x-show="!promoLocked && !promoLoading">{{ $i18n['use'] }}</span>
              <span x-show="promoLocked && !promoLoading">{{ $i18n['used'] }}</span>
              <span x-show="promoLoading">{{ $i18n['processing'] }}</span>
            </button>
          </div>

          <div class="mt-2 text-xs" x-html="promoMsg"></div>
        </div>


        {{-- TOTAL --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600">{{ $i18n['total_days'] }}</span>
            <b x-text="hours"></b>
          </div>
          <div class="flex justify-between mt-1">
            <span class="text-slate-600">{{ $i18n['total_price'] }}</span>
            <b>Rp <span x-text="format(total)"></span></b>
          </div>
        </div>

        <button
          type="button"
          class="w-full rounded-xl bg-brand-500 py-3 text-white font-extrabold hover:bg-brand-600 transition disabled:opacity-60 disabled:cursor-not-allowed"
          :disabled="loading"
          @click="submitBooking()">
          <span x-show="!loading">{{ $i18n['book_now'] }}</span>
          <span x-show="loading">{{ $i18n['processing'] }}</span>
        </button>

      </div>

    </div>
  </div>
</div>

<script>
  function hotelBookingPopup(basePrice, slug) {
    const I18N = @json($i18n);

    return {
      isOpen: false,
      loading: false,

      name: '',
      email: '',
      phone: '',

      promoCode: '',
      promoId: null,
      promoMsg: '',
      promoLocked: false,
      promoLoading: false,

      checkin_date: '',
      checkout_date: '',

      base: basePrice,
      hours: 0,
      total: 0,

      // inject token langsung dari blade (ANTI 419)
      token: @json(csrf_token()),

      open(detail) {
        this.checkin_date = detail?.checkin_date || '';
        this.checkout_date = detail?.checkout_date || '';
        this.isOpen = true;

        // reset promo state setiap buka popup
        this.promoMsg = '';
        this.promoCode = '';
        this.promoId = null;
        this.promoLocked = false;
        this.promoLoading = false;

        // ===== auto-fill promo dari URL (?promo=XXXX) =====
        const params = new URLSearchParams(window.location.search);
        const promoQ = (params.get('promo') || '').trim();
        if (promoQ) {
          this.promoCode = promoQ;
          this.applyPromo();
        }


        this.calc();
      },


      close() {
        this.isOpen = false;
        this.loading = false;
      },

      calc() {
        if (!this.checkin_date || !this.checkout_date) {
          this.hours = 0;
          this.total = 0;
          return;
        }
        const start = new Date(this.checkin_date);
        const end = new Date(this.checkout_date);
        if (end <= start) {
          this.hours = 0;
          this.total = 0;
          return;
        }
        const diff = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        this.hours = diff;
        this.total = diff * this.base;
      },

      format(n) {
        try {
          return Number(n || 0).toLocaleString('id-ID');
        } catch (e) {
          return n;
        }
      },

      applyPromo() {
        this.promoMsg = '';

        if (this.promoLocked) {
          this.promoMsg = `<span class="text-slate-600">${I18N.promo_already_used}</span>`;
          return;
        }
        if (this.promoLoading) return;

        const code = (this.promoCode || '').trim();
        if (!code) {
          this.promoMsg = `<span class="text-red-600">${I18N.promo_empty}</span>`;
          return;
        }
        if (this.total <= 0) {
          this.promoMsg = `<span class="text-red-600">${I18N.pick_date_first}</span>`;
          return;
        }

        this.promoLoading = true;

        fetch('/promo/validate', {

            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              _token: this.token,
              code: code,
              price: this.total,
              email: this.email
            })

          })
          .then(r => r.json())
          .then(res => {
            this.promoLoading = false;
            if (!res.valid) {
              this.promoMsg = `<span class="text-red-600">${res.message}</span>`;
              this.promoId = null;
              return;
            }
            this.total = res.final_price;
            this.promoId = res.promo_id;
            this.promoLocked = true;
            this.promoMsg = `<span class="text-emerald-600">Diskon diterapkan!</span>`;
          })
          .catch(() => {
            this.promoLoading = false;
            this.promoMsg = `<span class="text-red-600">${I18N.server_unreachable}</span>`;
          });
      },

      submitBooking() {
        if (!this.name || !this.email || !this.phone) {
          alert(I18N.required_fields);
          return;
        }
        if (!this.checkin_date || !this.checkout_date) {
          alert(I18N.required_dates);
          return;
        }

        this.loading = true;

        fetch(`/hotel/${slug}/draft-booking`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              _token: this.token,
              name: this.name,
              email: this.email,
              phone: this.phone,
              checkin_date: this.checkin_date,
              checkout_date: this.checkout_date,
              promo_id: this.promoId ? Number(this.promoId) : null,
              final_price: this.total
            })
          })
          .then(async (r) => {
            const text = await r.text();

            if (!r.ok) {
              // biar gak “nebak-nebak” lagi, tampilkan error mentahnya
              console.error('Draft rent car failed', r.status, text);
              alert(`Booking gagal (HTTP ${r.status}). Lihat Console untuk detail.`);
              this.loading = false;
              return null;
            }

            // kalau response json
            try {
              return JSON.parse(text);
            } catch (e) {
              console.error('Response bukan JSON:', text);
              alert('Booking gagal: response server bukan JSON.');
              this.loading = false;
              return null;
            }
          })
          .then(res => {
            if (res && res.redirect) {
              window.location.href = res.redirect;
            } else if (res) {
              alert('Gagal membuat pesanan.');
              this.loading = false;
            }
          })
          .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            this.loading = false;
          });
      }
    }
  }
</script>