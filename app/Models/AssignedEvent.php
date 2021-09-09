<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedEvent extends Model
{
    use HasFactory;

    protected $table = 'assigned_events';

    const STATUS_ASSIGNED = 1;


    public function getAssignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }


    // public function jsonData()
    // {
    //     $json = [];
    //     $json['user_id'] = $this->assigned_to;
    //     $json['title'] = $this->title;
    //     $json['event_timestamp'] = $this->event_timestamp;
    //     $json['assigned_to'] = $this->getAssignedEvent;

    //     return $json;
    // }
}
