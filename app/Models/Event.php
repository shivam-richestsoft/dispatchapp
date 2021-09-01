<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;


    const STATUS_NEW = 1;


    protected $table = 'events';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'event_timestamp',
        'accepted_at',
        'status',
        'created_by_id',

    ];
}
