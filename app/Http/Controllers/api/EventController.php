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
                'event_json' => 'required|json',
                'assigned_to' => 'required',
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            $loginUser = auth()->user();

            $eventJson = json_decode($r->event_json);

            $assignedTo = $r->assigned_to;

            DB::beginTransaction();

            foreach ($eventJson as $event) {

                //json data validation
                $dataArray = [
                    'event_title' => $event->title,
                    'event_timestamp' => $event->event_timestamp
                ];
                $v = Validator::make(
                    $dataArray,
                    [
                        'event_title' => 'required|string|max:50',
                        'event_timestamp' => 'required|date'
                    ],

                );
                if ($v->fails()) {
                    return $this->validation($v);
                }

                $eventModel = Event::where(["event_timestamp" => $event->event_timestamp])->first();
                if (empty($eventModel)) {
                    $eventModel = new Event();
                }
                $eventModel->title = $event->title;
                $eventModel->event_timestamp = $event->event_timestamp;
                $eventModel->status = $eventModel::STATUS_NEW;
                $eventModel->created_by_id = $loginUser->id;
                $eventModel->save();

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

            DB::commit();

            return $this->success('Event Assigned Successfull');
        } catch (\Exception $e) {

            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    //get events list
    public function getEvents(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'event_timestamp' => 'required|array',
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }

        try {
            $paginator = Event::where(['event_timestamp' => $r->event_timestamp])->paginate(20);
            return $this->customPaginator($paginator);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    //get driver events events list
    public function getDriverEvents(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'event_type' => 'required', //assigned or unassigned 
                'event_type' => 'required', //assigned or unassigned
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            $loginUser = auth()->user();
            $query = Event::select("events.id", "events.title", "events.event_timestamp")->Join('assigned_events', 'assigned_events.event_id', '=', 'events.id')->where(["assigned_events.assigned_to" => $loginUser]);
            $paginator = $query->paginate(20);

            return $this->customPaginator($paginator);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
