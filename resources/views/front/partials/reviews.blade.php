@php
$approvedReviews = $item->reviews()->approved()->latest()->get();
$avg = round((float) $approvedReviews->avg('rating'), 1);
$count = $approvedReviews->count();
$isEn = app()->getLocale() === 'en';
@endphp

<section class="mt-12" data-aos="fade-up">
    <div class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm">

        {{-- HEADER --}}
        <div class="px-6 py-5 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">
                {{ $isEn ? 'Customer Reviews' : 'Ulasan Pembeli' }}
            </h3>
            <p class="mt-1 text-sm text-slate-600">
                @if($count > 0)
                <span class="font-semibold text-slate-900">{{ $avg }}</span>/5 ·
                {{ $count }} {{ $isEn ? 'reviews' : 'ulasan' }}
                @else
                {{ $isEn ? 'No reviews to display yet.' : 'Belum ada ulasan yang ditampilkan' }}
                @endif
            </p>
        </div>

        {{-- BODY --}}
        <div class="px-6 py-6 space-y-10">

            {{-- REVIEW LIST --}}
            <div>
                <div class="swiper reviewSwiper">
                    <div class="swiper-wrapper">
                        @forelse($approvedReviews as $r)
                        <div class="swiper-slide">
                            <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $r->name }}</div>
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ $r->created_at->format('d M Y') }}
                                        </div>
                                    </div>

                                    {{-- STATIC STAR (DISPLAY) --}}
                                    <div class="flex gap-1 shrink-0">
                                        @for($i=1;$i<=5;$i++)
                                            <svg class="w-4 h-4 {{ $i <= $r->rating ? 'text-amber-400' : 'text-slate-300' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.444a1 1 0 00-.364 1.118l1.286 3.955c.3.921-.755 1.688-1.54 1.118l-3.363-2.444a1 1 0 00-1.175 0L6.98 18.007c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L3 9.382c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.286-3.955z" />
                                            </svg>
                                            @endfor
                                    </div>
                                </div>

                                <p class="mt-4 text-sm text-slate-600 leading-relaxed">
                                    “{{ $r->comment }}”
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="swiper-slide">
                            <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-5 text-slate-600">
                                {{ $isEn ? 'Be the first to leave a review.' : 'Jadi yang pertama memberikan ulasan.' }}
                                <br>
                                {{ $isEn ? 'After submission, your review will be moderated by admin.' : 'Setelah submit, ulasan akan dimoderasi admin.' }}

                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between gap-3">
                    <div class="text-xs text-slate-600">
                        <span class="font-semibold text-slate-900">
                            <span class="review-current">1</span>/<span class="review-total">{{ max(1, $count) }}</span>
                        </span>
                        <span class="ml-2 text-slate-500">
                            Total: {{ $count }} ulasan
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <button type="button"
                            class="review-prev px-4 py-2 rounded-xl text-sm ring-1 ring-slate-200 hover:bg-slate-50">
                            Prev
                        </button>
                        <button type="button"
                            class="review-next px-4 py-2 rounded-xl text-sm bg-primary text-white hover:bg-primary-600 shadow-sm">
                            Next
                        </button>
                    </div>
                </div>

            </div>

            {{-- FORM --}}
            <div>
                <h4 class="font-semibold text-slate-900 mb-1">
                    {{ $isEn ? 'Write a Review' : 'Tulis Ulasan' }}
                </h4>

                @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 ring-1 ring-green-200">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-3 ring-1 ring-red-200">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST"
                    action="{{ route('review.store') }}"
                    class="space-y-4"
                    x-data="{ rating: {{ (int) old('rating', 5) }}, hover: 0 }">


                    @csrf

                    <input type="hidden" name="reviewable_type" value="{{ $type }}">
                    <input type="hidden" name="reviewable_id" value="{{ $item->id }}">
                    <input type="hidden" name="rating" :value="rating">

                    {{-- honeypot --}}
                    <input type="text" name="website" class="hidden">
                    <input type="hidden" name="form_started_at" value="{{ time() }}">

                    <div class="grid sm:grid-cols-2 gap-4">
                        <input class="rounded-xl border border-slate-200 px-4 py-3 focus:ring-4 focus:ring-primary/20"
                            name="name" placeholder="{{ $isEn ? 'Full Name' : 'Nama Lengkap' }}" required>
                        <input class="rounded-xl border border-slate-200 px-4 py-3 focus:ring-4 focus:ring-primary/20"
                            name="email" placeholder="Email" required>
                    </div>

                    {{-- STAR RATING (PASTI MUNCUL) --}}
                    <div>
                        <div class="text-sm font-medium text-slate-700 mb-2">
                            Rating <span class="text-xs text-slate-500" x-text="rating ? rating + '/5' : '0/5'"></span>
                        </div>

                        <div class="flex gap-1 justify-center">
                            <template x-for="n in 5" :key="n">
                                <button type="button"
                                    @mouseenter="hover = n"
                                    @mouseleave="hover = 0"
                                    @click="rating = n"
                                    class="focus:outline-none">
                                    <svg viewBox="0 0 20 20"
                                        class="w-8 h-8 transition"
                                        :class="(hover || rating) >= n ? 'text-amber-400' : 'text-slate-300'"
                                        fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.444a1 1 0 00-.364 1.118l1.286 3.955c.3.921-.755 1.688-1.54 1.118l-3.363-2.444a1 1 0 00-1.175 0L6.98 18.007c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L3 9.382c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.286-3.955z" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <textarea class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-4 focus:ring-primary/20"
                        name="comment" rows="4" placeholder="{{ $isEn ? 'Your experience...' : 'Pengalaman Anda...' }}" required></textarea>
                    <div class="bg-[#0194F3] rounded-lg">
                        <button class="w-full rounded-xl bg-primary text-white py-3 font-semibold hover:bg-primary-600 shadow-sm">
                            {{ $isEn ? 'Submit Review' : 'Kirim Ulasan' }}
                        </button>
                    </div>



                </form>
            </div>
        </div>
    </div>
</section>