<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PartnerSubscription;
use App\Models\PartnerPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CheckPartnerSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:check-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check partner subscriptions, send reminders (H-5), and downgrade expired ones';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        // 1. Send reminders H-5 before expiry
        $reminderDateStart = Carbon::now()->addDays(5)->startOfDay();
        $reminderDateEnd = Carbon::now()->addDays(5)->endOfDay();
        
        $subscriptionsToRemind = PartnerSubscription::where('status', 'active')
            ->whereBetween('ends_at', [$reminderDateStart, $reminderDateEnd])
            ->with(['user', 'package'])
            ->get();

        foreach ($subscriptionsToRemind as $sub) {
            $user = $sub->user;
            if ($user && $user->email) {
                try {
                    Mail::raw("Halo {$user->name},\n\nPaket Langganan Partner Anda ({$sub->package->name}) akan berakhir pada {$sub->ends_at->format('d M Y')}.\nSilakan lakukan perpanjangan atau upgrade melalui dashboard partner agar listing iklan Anda tetap aktif.\nJika tidak diperpanjang, paket Anda akan otomatis kembali ke paket Gratis.\n\nTerima kasih,\nTim Admin", function ($msg) use ($user) {
                        $msg->to($user->email)->subject('Pengingat Perpanjangan Paket Partner H-5');
                    });
                    $this->info("Reminder email sent to {$user->email} for subscription #{$sub->id}");
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder email to {$user->email}: " . $e->getMessage());
                }
            }
        }

        // 2. Downgrade expired subscriptions to "Free"
        $expiredSubscriptions = PartnerSubscription::where('status', 'active')
            ->where('ends_at', '<', $now)
            ->with(['user'])
            ->get();

        $freePackage = PartnerPackage::where('is_free', true)->first();

        if (!$freePackage) {
            $this->error("No Free Package found in database!");
        } else {
            foreach ($expiredSubscriptions as $sub) {
                // Update status to expired
                $sub->update(['status' => 'expired']);

                $user = $sub->user;
                if ($user) {
                    // Create new free subscription
                    PartnerSubscription::create([
                        'user_id' => $user->id,
                        'partner_package_id' => $freePackage->id,
                        'amount' => 0,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => null, // Free package typically has no end date, or based on freePackage duration
                    ]);

                    // Downgrade quota
                    $userQuota = \App\Models\UserQuota::firstOrCreate(['user_id' => $user->id]);
                    if ($freePackage->listing_quota != -1) {
                        $userQuota->listing_quota = $freePackage->listing_quota;
                    } else {
                        $userQuota->listing_quota = -1;
                    }
                    $userQuota->save();
                    
                    if ($user->email) {
                        try {
                            Mail::raw("Halo {$user->name},\n\nPaket Langganan Partner Anda telah berakhir. Paket Anda saat ini telah diturunkan menjadi paket {$freePackage->name}.\nSilakan upgrade kembali melalui dashboard partner.\n\nTerima kasih,\nTim Admin", function ($msg) use ($user) {
                                $msg->to($user->email)->subject('Paket Langganan Partner Berakhir');
                            });
                        } catch (\Exception $e) {}
                    }

                    $this->info("Downgraded user {$user->id} to free package");
                }
            }
        }

        $this->info('Partner subscriptions checked successfully.');
        return 0;
    }
}
