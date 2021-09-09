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

    public function getAssignedEvent()
    {
        return $this->belongsTo(AssignedEvent::class, 'id', 'event_id');
    }

    public function jsonData()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['title'] = $this->title;
        $json['event_timestamp'] = $this->event_timestamp;
        $json['assigned_to'] = $this->getAssignedEvent->getAssignedTo->jsonData()??(object)[];

        return $json;
    }
}
