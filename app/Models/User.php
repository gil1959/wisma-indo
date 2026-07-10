<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'address',
    'full_address',
    'sub_district',
    'email_verified_at',
   // affiliate
'is_affiliate',
'affiliate_status',
'affiliate_requested_at',
'affiliate_reviewed_at',
'affiliate_reviewed_by',
'affiliate_review_note',
'affiliate_commission_type',
'affiliate_commission_value',
'partner_tax_percent',
'is_suspended',
'suspended_at',
 'partner_tax_percent',
  'partner_type',
  'partner_bank_name',
  'partner_bank_account_number',
  'partner_bank_account_holder',
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_affiliate' => 'boolean',
        'affiliate_requested_at' => 'datetime',
    'affiliate_reviewed_at' => 'datetime',
    'partner_tax_percent' => 'decimal:2',
'is_suspended' => 'boolean',
'suspended_at' => 'datetime',
    ];
    public function quotas()
    {
        return $this->hasOne(UserQuota::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function topupTransactions()
    {
        return $this->hasMany(TopupTransaction::class);
    }
}
