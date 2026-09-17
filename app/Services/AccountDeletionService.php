<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\{DB, Schema, Storage};

class AccountDeletionService
{
    public function eligible(User $user): bool
    {
        return !$user->hasAnyRole(['admin', 'site_moderator']);
    }

    public function fingerprint(User $user): string
    {
        return hash_hmac('sha256', $user->id.'|'.$user->email.'|'.$user->password, config('app.key'));
    }

    public function delete(int $id, string $fingerprint): void
    {
        DB::transaction(function () use ($id, $fingerprint) {
            $user = User::lockForUpdate()->findOrFail($id);
            abort_unless($this->eligible($user) && hash_equals($this->fingerprint($user), $fingerprint), 403);
            $listings = DB::table('listings')->where('user_id', $id)->lockForUpdate()->get();
            $listingIds = $listings->pluck('id')->all();
            $paths = [$user->avatar];
            foreach ($listings as $listing) $paths[] = $listing->cover_image ?? null;
            $queries = [];
            $add = function ($table, $query) use (&$queries) { $queries[$table] = $query; };
            if (Schema::hasTable('buyer_leads')) {
                $leads = DB::table('buyer_leads')->where(function ($q) use ($id, $listingIds, $user) {
                    $q->where('partner_id', $id)->orWhereIn('listing_id', $listingIds)->orWhere('email', $user->email);
                });
                if (Schema::hasTable('lead_activities')) $add('lead_activities', DB::table('lead_activities')->whereIn('buyer_lead_id', (clone $leads)->pluck('id')));
                $add('buyer_leads', $leads);
            }
            foreach (['listing_images', 'favorite_listings', 'listing_transactions', 'survey_schedules', 'scheduled_autoposts'] as $table) {
                if (!Schema::hasTable($table)) continue;
                $owner = Schema::hasColumn($table, 'user_id') ? 'user_id' : (Schema::hasColumn($table, 'partner_id') ? 'partner_id' : null);
                $query = DB::table($table)->where(function ($q) use ($owner, $id, $listingIds) {
                    $q->whereIn('listing_id', $listingIds);
                    if ($owner) $q->orWhere($owner, $id);
                });
                $add($table, $query);
            }
            foreach (['topup_transactions', 'partner_subscriptions', 'partner_registrations', 'bulk_uploads', 'social_accounts', 'user_quotas', 'sessions'] as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) $add($table, DB::table($table)->where('user_id', $id));
            }
            foreach ($queries as $table => $query) {
                $columns = array_intersect(['image_path','payment_proof','proof_of_payment','ktp_file','nib_file','foto_file','npwp_file','lisensi_file','file_path'], Schema::getColumnListing($table));
                if ($columns) foreach ((clone $query)->get(array_values($columns)) as $row) foreach ((array) $row as $path) $paths[] = $path;
            }
            // Delete only owned uploads. A failure leaves the account available for retry.
            foreach (array_unique(array_filter($paths)) as $path) $this->deleteUpload($path);
            foreach ($queries as $query) $query->delete();
            if (Schema::hasTable('google_indexing_logs')) {
                foreach ($listings as $listing) {
                    if (!isset($listing->slug)) continue;
                    foreach (['properti','barang','jasa'] as $prefix) DB::table('google_indexing_logs')->where('url', url($prefix.'/'.$listing->slug))->delete();
                }
            }
            DB::table('listings')->whereIn('id', $listingIds)->delete();
            foreach (['password_resets', 'password_reset_tokens'] as $table) if (Schema::hasTable($table)) DB::table($table)->where('email', $user->email)->delete();
            $user->tokens()->delete();
            if (Schema::hasTable('notifications')) DB::table('notifications')->where('notifiable_type', $user->getMorphClass())->where('notifiable_id', $id)->delete();
            $user->delete();
        }, 3);
    }

    private function deleteUpload(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            if (parse_url($value, PHP_URL_HOST) !== parse_url(config('app.url'), PHP_URL_HOST)) return;
            $value = parse_url($value, PHP_URL_PATH) ?? '';
        }
        $path = ltrim(str_replace('\\', '/', $value), '/');
        foreach (['storage/', 'public/'] as $prefix) if (str_starts_with($path, $prefix)) $path = substr($path, strlen($prefix));
        if (str_contains($path, '..') || str_contains($path, "\0")) throw new \RuntimeException('Lokasi berkas tidak valid.');
        if (!preg_match('#^(avatars|listings|partners|payments|bulk_uploads|partner_registrations|payment_proofs)/#', $path)) return;
        $disk = Storage::disk('public');
        if ($disk->exists($path) && !$disk->delete($path)) throw new \RuntimeException('Berkas belum dapat dihapus. Silakan coba kembali.');
    }
}
