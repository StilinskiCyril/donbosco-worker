<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['id'];

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
        if (!is_null(request('project_uuid')) && !empty(request('project_uuid'))) {
            $project_ids = Project::where('uuid', request('project_uuid'))->pluck('id');
            $q->whereIn('project_id', $project_ids);
        }
        if (!is_null(request('name')) && !empty(request('name'))) {
            $q->where('name', request('name'));
        }
        if (!is_null(request('account_number')) && !empty(request('account_number'))) {
            $q->where('account_number', request('account_number'));
        }
        if (!is_null(request('description')) && !empty(request('description'))) {
            $q->where('description', 'like', '%'.request('description').'%');
        }
        if (!is_null(request('target_amount')) && !empty(request('target_amount'))) {
            $q->where('target_amount', request('target_amount'));
        }
        if (!is_null(request('target_date')) && !empty(request('target_date'))) {
            $start = Carbon::parse(request('target_date'))->startOfDay();
            $end = Carbon::parse(request('target_date'))->endOfDay();
            $q->whereBetween('target_date', [$start, $end]);
        }
        if (!is_null(request('message_to_donor')) && !empty(request('message_to_donor'))) {
            $q->where('message_to_donor', 'like', '%'.request('message_to_donor').'%');
        }
        if (!is_null(request('message_to_treasurer')) && !empty(request('message_to_treasurer'))) {
            $q->where('message_to_treasurer', 'like', '%'.request('message_to_treasurer').'%');
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

    // load account project
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // load account treasurers
    public function treasurers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Treasurer::class);
    }

    // load account pledges
    public function pledges(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pledge::class, 'account_no', 'account_no');
    }
}
