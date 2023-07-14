<?php

namespace App\Models;

use App\Events\PermissionStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    use HasFactory;

    protected $table = 'reserves';

    protected $fillable = ['room_id','user_id','title','name','start_time','stop_time','participant','permission_status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reserve) {
            if ($reserve->permission_status == 1) {
                event(new \App\Events\PendingMail($reserve));
            }
        });
    }
}
