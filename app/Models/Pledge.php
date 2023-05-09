<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pledge extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    protected $hidden = ['id', 'updated_at', 'deleted_at'];

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
        if (!is_null(request('name')) && !empty(request('name'))) {
            $q->where('name', request('name'));
        }
        if (!is_null(request('msisdn')) && !empty(request('msisdn'))) {
            $q->where('msisdn', request('msisdn'));
        }
        if (!is_null(request('email')) && !empty(request('email'))) {
            $q->where('email', request('email'));
        }
        if (!is_null(request('target_amount')) && !empty(request('target_amount'))) {
            $q->where('target_amount', request('target_amount'));
        }
        if (!is_null(request('target_date')) && !empty(request('target_date'))) {
            $start = Carbon::parse(request('target_date'))->startOfDay();
            $end = Carbon::parse(request('target_date'))->endOfDay();
            $q->whereBetween('target_date', [$start, $end]);
        }
        if (!is_null(request('account_no')) && !empty(request('account_no'))) {
            $q->where('account_no', request('account_no'));
        }
        if (!is_null(request('frequency')) && !empty(request('frequency'))) {
            $q->where('frequency', request('frequency'));
        }
        if (!is_null(request('frequency_amount')) && !empty(request('frequency_amount'))) {
            $q->where('frequency_amount', request('frequency_amount'));
        }
        if (!is_null(request('once_and_monthly_frequency_date')) && !empty(request('once_and_monthly_frequency_date'))) {
            $q->where('once_and_monthly_frequency_date', request('once_and_monthly_frequency_date'));
        }
        if (!is_null(request('day_of_the_week')) && !empty(request('day_of_the_week'))) {
            $q->where('day_of_the_week', request('day_of_the_week'));
        }
        if (!is_null(request('opt_out')) && !empty(request('opt_out'))) {
            $q->where('opt_out', request('opt_out'));
        }
        if (!is_null(request('last_alert_time')) && !empty(request('last_alert_time'))) {
            $last_alert_time = Carbon::parse(request('last_alert_time'))->toDateTime();
            $q->where('last_alert_time', $last_alert_time);
        }
        if (!is_null(request('alerted')) && !empty(request('alerted'))) {
            $q->where('alerted', request('alerted'));
        }
        if (!is_null(request('payment_status')) && !empty(request('payment_status'))) {
            $q->where('payment_status', request('payment_status'));
        }
        if (!is_null(request('stk_count_daily')) && !empty(request('stk_count_daily'))) {
            $q->where('stk_count_daily', request('stk_count_daily'));
        }
        if (!is_null(request('start')) && !empty(request('start'))) {
            $q->where('created_at', '>=', request('start'));
        }
        if (!is_null(request('end')) && !empty(request('end'))) {
            $q->where('created_at', '<=', request('end'));
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

    // load pledge account
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_no', 'account_no');
    }

    // load donations
    public function donations()
    {
        return $this->hasMany(Donation::class, 'account_no', 'account_no');
    }
}
