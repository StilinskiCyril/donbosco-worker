<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PendingMpesaTransaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function ($uuid){
            $uuid->uuid = Str::uuid()->toString();
        });
    }
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function scopeFilter($q){
        if (!is_null(request('uuid')) && !empty(request('uuid'))) {
            $q->where('uuid', request('uuid'));
        }
        if (!is_null(request('trans_id')) && !empty(request('trans_id'))) {
            $q->where('trans_id', request('trans_id'));
        }
        if (!is_null(request('start')) && !empty(request('start'))) {
            $q->where('trans_time', '>=', request('start'));
        }
        if (!is_null(request('end')) && !empty(request('end'))) {
            $q->where('trans_time', '<=', request('end'));
        }
        if (!is_null(request('amount')) && !empty(request('amount'))) {
            $q->where('amount', request('amount'));
        }
        if (!is_null(request('account_no')) && !empty(request('account_no'))) {
            $q->where('account_no', request('account_no'));
        }
        if (!is_null(request('business_short_code')) && !empty(request('business_short_code'))) {
            $q->where('business_short_code', request('business_short_code'));
        }
        if (!is_null(request('third_party_trans_id')) && !empty(request('third_party_trans_id'))) {
            $q->where('third_party_trans_id', request('third_party_trans_id'));
        }
        if (!is_null(request('ip')) && !empty(request('ip'))) {
            $q->where('ip', request('ip'));
        }
        if (!is_null(request('status')) && !empty(request('status'))) {
            $q->where('status', request('status'));
        }
        if (!is_null(request('sort_by')) && !empty(request('sort_by'))) {
            if (request('sort_by') == 'random') {
                $q->inRandomOrder();
            }
            if (request('sort_by') == 'latest') {
                $q->latest();
            }
            if (request('sort_by') == 'oldest') {
                $q->oldest();
            }
        }
        return $q;
    }

    public function unknownDonations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UnknownDonation::class);
    }
}
