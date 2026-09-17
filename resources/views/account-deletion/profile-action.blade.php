<section class="mt-8 bg-white rounded-3xl p-8 border border-slate-100 shadow-sm" x-data="{ open: false }">
    <h2 class="text-lg font-bold text-slate-900">Hapus akun</h2>
    <p class="mt-2 text-sm text-slate-500">Ajukan penghapusan akun WismaIndo dan data terkait melalui konfirmasi email.</p>
    <button type="button" @click="open = true; $nextTick(() => $refs.cancel.focus())" class="mt-4 px-5 py-3 rounded-xl border border-red-200 text-red-700 font-bold hover:bg-red-50">Hapus akun saya</button>
    <div x-show="open" x-cloak @keydown.escape.window="open = false" style="z-index:100" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60" @click.self="open = false">
        <div role="dialog" aria-modal="true" aria-labelledby="delete-account-title" class="w-full max-w-lg bg-white rounded-3xl p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <h3 id="delete-account-title" class="text-xl font-bold text-slate-900">Ajukan penghapusan akun?</h3>
            <p class="mt-3 text-sm text-slate-600">Kami akan mengirim tautan konfirmasi ke email akun Anda. Akun belum dihapus sampai Anda mengonfirmasi melalui tautan tersebut.</p>
            <a href="{{ route('account-deletion.index') }}" class="block mt-3 text-sm font-semibold text-[#0194F3]">Lihat data yang akan dihapus</a>
            <form method="POST" action="{{ route('account-deletion.request') }}" class="mt-5">
                @csrf
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="consent" value="1">
                <div class="flex flex-wrap justify-end gap-3">
                    <button x-ref="cancel" type="button" @click="open = false" class="px-5 py-3 rounded-xl border border-slate-200 font-semibold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-3 rounded-xl bg-[#0194F3] text-white font-bold">Kirim konfirmasi email</button>
                </div>
            </form>
        </div>
    </div>
    @if(session('deletion_status'))<p role="status" class="mt-4 text-sm text-slate-700">{{ session('deletion_status') }}</p>@endif
    @if($errors->any())<p role="status" class="mt-4 text-sm text-red-700">{{ $errors->first() }}</p>@endif
</section>
