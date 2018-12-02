<?php

namespace App\Listeners\Log;

use App\Events\Log\ModelChange as Event;
use App\Models\User\UserActionLog;

class ModelChange
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ModelChange  $event
     * @return void
     */
    public function handle(Event $event)
    {
//        dd($event->model);
        $data = new UserActionLog();
        $data->after_content = json_encode($event->after);
        $data->title = $event->title;
        $data->user_id=$event->user;
        $data->before_content = json_encode($event->before);
        $data->ip = $event->ip;
        $data->site=$event->site;
        $data->user_type = 0;
        $data->model = $event->model;
        $data->key = $event->key;
        $data->save();
    }
}
