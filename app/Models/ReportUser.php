<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    use HasFactory;

    const USER_REPORT_TYPE = 1;
    const CONTENT_REPORT_TYPE = 2;

    const REPORT_TYPE = [
        self::USER_REPORT_TYPE => 'User',
        self::CONTENT_REPORT_TYPE => 'Content',
    ];

    protected $fillable = [
        'user_id',
        'to_user_id',
        'file',
        'type',
        'reason',
        'channel_url',
        'objectional_type',
        'file_type'
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id')->withTrashed();
    }
}
