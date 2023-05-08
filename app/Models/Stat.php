<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Stat extends Model
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
        if (!is_null(request('account_uuid')) && !empty(request('account_uuid'))) {
            $account_ids = Account::where('uuid', request('account_uuid'))->pluck('id');
            $q->whereIn('account_id', $account_ids);
        }
        if (!is_null(request('date')) && !empty(request('date'))) {
            $q->where('date', request('date'));
        }
        if (!is_null(request('amount')) && !empty(request('amount'))) {
            $q->where('amount', request('amount'));
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

    // load account
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
