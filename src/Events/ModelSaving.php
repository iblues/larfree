<?php

namespace Larfree\Events;

use App\Models\User\UserActionLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Larfree\Models\Api;

class ModelSaving
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Api $data)
    {
        //过滤掉数据库没有的列,避免报错
        $columns = $data->getColumns();
        //日志记录
        if($data->is_log()){
            $this->logAction($data);
        }

        $data->beforeSave($data);
        foreach ($data->toArray() as $key =>$val) {
            if(!in_array($key,$columns)){
                $data->setTmpSave($key,$data->$key);
                unset($data->$key);
            }
        }
    }

    /**
     * 获取修改前和之后的数据
     * @param Api $data
     */
    public function logAction(Api $data){
        if(class_exists('App\Events\Log\ModelChange') && $data->id ){
            $oldData = (new $data)->find($data->id);
            event(new \App\Events\Log\ModelChange($oldData,$data));
//            $diff = array_diff($oldData->toArray(),$data->toArray());
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
    }
}
