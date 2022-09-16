<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Database\Eloquent\Builder;

class EventsController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }

    public function store(Request $request){
        
        // requrest validation
        $request->validate([
            'eventname' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        $start = $request->start;
        $end = $request->end;

        $eventsCount = Event::where(function ($query) use ($start, $end) {
            $query->where('start', '<', $start)
                        ->where('end', '>=', $start)
                        ->where('user_id','=',auth()->user()->id);
            })
            ->orWhere(function ($query) use ($start, $end) {
                $query->where('start', '<', $end)
                        ->where('end', '>=', $end)
                        ->where('user_id','=',auth()->user()->id);
            })->orWhere(function ($query) use ($start, $end) {
                $query->where('start', '>=', $start)
                        ->where('end', '<=', $end)
                        ->where('user_id','=',auth()->user()->id);
            })->count();
                                   

       
        if($eventsCount>=1){
            return response()->json(['error' => 'Overlapping Event Please Pick Another Date and Time'], 400);
        }
       

        $event = Event::create([
            'eventname' => $request->eventname,
            'start' => $request->start,
            'end' => $request->end,
            'user_id'=>auth()->user()->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully',
            'event' => $event,
        ]);

    }

    public function destroy($id){


        if(!is_numeric($id)){
            return response()->json(['error' => 'Id is not an integer'], 400);
        }

        $event = Event::find($id);

        if($event == null){
            return response()->json(['error' => 'Event not found'], 404);
        }

        if(auth()->user()->id != $event->user_id ){
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully',
            'event' => $event,
        ]);

    }


    public function update(Request $request, $id){

        $request->validate([
            'eventname' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        if(!is_numeric($id)){
            return response()->json(['error' => 'Id is not an integer'], 400);
        }

        $event = Event::find($id);

        if($event == null){
            return response()->json(['error' => 'Event not found'], 404);
        }

        if(auth()->user()->id != $event->user_id ){
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }

        $requestStart =  new DateTime($request->start);
        $eventStart = new DateTime($event->start);

        $requestEnd =  new DateTime($request->end);
        $eventEnd = new DateTime($event->end);
        
         if(($eventStart ==  $requestStart) && ( $eventEnd ==  $requestEnd )){  

            $event->update(["eventname"=>$request->eventname]);
        
            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully',
                'event' => $event,
            ]);

         }

        

         $start = $request->start;
         $end = $request->end;
         $eventsCount = Event::where(function ($query) use ($start, $end, $event) {
             $query->where('start', '<', $start)
                         ->where('end', '>=', $start)
                         ->where('user_id','=',auth()->user()->id)
                         ->whereNotIn('id',$event);
                        
             })
             ->orWhere(function ($query) use ($start, $end,$event) {
                 $query->where('start', '<', $end)
                         ->where('end', '>=', $end)
                         ->where('user_id','=',auth()->user()->id)
                         ->whereNotIn('id',$event);
             })
             ->orWhere(function ($query) use ($start, $end,$event) {
                $query->where('start', '>=', $start)
                        ->where('end', '<=', $end)
                        ->where('user_id','=',auth()->user()->id)
                        ->whereNotIn('id',$event);
            })->count();
                                    
 
        
         if($eventsCount>=1){
             return response()->json(['error' => 'Overlapping Event Please Pick Another Date and Time'], 400);
         }

         $event->update($request->all());
        
         return response()->json([
             'status' => 'success',
             'message' => 'Event updated successfully',
             'event' => $event,
         ]);


    }

}
