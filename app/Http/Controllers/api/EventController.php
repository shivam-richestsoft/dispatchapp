<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

//facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


//models
use App\Models\User;


//additional
use DB;
use Validator;
//traits
use App\Traits\ApiResponser;
use App\Traits\ImageUpload;
use App\Traits\Email;
use App\Traits\Togglestatus;

//events
use App\Events\Notify;
use App\Models\AssignedEvent;
use App\Models\Event;
use App\Models\Otp;
use Exception;

class EventController extends Controller
{
    use ApiResponser;
    use ImageUpload;
    use Email;
    use Togglestatus;

    // Assign a event    
    public function assignEvent(request $r)
    {

        $v = Validator::make(
            $r->input(),
            [
                'title' => 'required|string|max:50',
                'event_timestamp' => 'required|date',
                'assigned_to' => 'required|array',
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            $loginUser = auth()->user();

            $eventModel = Event::where(["event_timestamp" => $r->event_timestamp])->first();
            if (empty($eventModel)) {
                $eventModel = new Event();
            }
            $eventModel->title = $r->title;
            $eventModel->event_timestamp = $r->event_timestamp;
            $eventModel->status = $eventModel::STATUS_NEW;
            $eventModel->created_by_id = $loginUser->id;
            $eventModel->save();

            foreach ($r->assigned_to as $assignedTo) {
                $assignedEventModel = AssignedEvent::where(["assigned_to" => $assignedTo, "event_id" => $eventModel->id])->first();
                if (empty($assignedEventModel)) {
                    $assignedEventModel = new AssignedEvent();
                }
                $assignedEventModel->assigned_to = $assignedTo;
                $assignedEventModel->event_id = $eventModel->id;
                $assignedEventModel->status = $assignedEventModel::STATUS_ASSIGNED;
                $assignedEventModel->created_by_id = $loginUser->id;
                $assignedEventModel->save();
            }

            return $this->success('Event Assigned Successfull');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    //get driver list
    public function getEvents(request $r)
    {
        try {
            $paginator = Event::where('event_timestamp', User::ROLE_DRIVER)->paginate(20);
            return $this->customPaginator($paginator);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
