<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Notification;
use Illuminate\Http\Request;

use App\Http\Requests;

class AnnouncementController extends Controller
{
    public function store(Request $request){
        $announcement=new Announcement();
        $announcement->body=$request['body'];
        $announcement->owner_id=$request['owner_id'];
        $announcement->save();
        $announcement->groups()->sync($request['group_list']);

        foreach($request['group_list'] as $group){
            $notification=new Notification();
            $notification->text='New announcement';
            $notification->receiver_id=$group;
            $notification->receiver_table='groups';
            $notification->source_id=$announcement->id;
            $notification->source_table='announcements';
            $notification->save();
        }
    }

    public function delete(Request $request){
        $announcement=Announcement::find($request['id']);
        $announcement->delete();
        $notification=Notification::where([['source_table','announcements'],['source_id',$request['id']]]);
        $notification->delete();
    }
}
