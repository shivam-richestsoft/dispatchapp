<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedEvent extends Model
{
    use HasFactory;

    protected $table = 'assigned_events';

    const STATUS_ASSIGNED = 1;
}
