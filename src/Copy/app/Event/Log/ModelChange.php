<?php

namespace App\Events\Log;

use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Larfree\Models\Api;

class ModelChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $before;
    public $after;
    public $user;
    public $site;
    public $ip;
    public $model;
    public $title;
    public $key;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(?Api $before,?Api $after,$user_id=0)
    {
        if($user_id){
            $this->user=$user_id;
        }else {
            $this->user = getLoginUserID() ?? 0;
        }
        if(function_exists('getCurrentSiteId')){
            $this->site = getCurrentSiteId() ?? 0;
        };
        $this->ip = Request()->getClientIp();


        if(is_null($before)){
            $this->key = 0;
            $this->type=1;
            $this->after = $after->toArray();
            $this->title='添加操作';
            $this->before='';
            $this->model=$after->getModelName();
        }elseif(is_null($after)){
            $this->key = $before->id;
            $this->type=3;
            $this->title='删除操作';
            $this->model=$before->getModelName();
            $this->after = '';
            $this->before=$before;
        }else{
            $this->key = $after->id;
            $this->before=$before;
            $this->model=$after->getModelName();
            $this->type=2;
            $this->title='修改操作';
            $this->after = array_diff($after->toArray(),$before->toArray());
        }
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
//        return new PrivateChannel('channel-name');
    }
}
