<?php

namespace Larfree\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Larfree\Libs\Schemas;

class ModelSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $data)
    {

        $schemas = $data->getSchemas();
        if ($data->getTmpSave()) {
            foreach ($data->getTmpSave() as $key => $val) {
                $this->doRelateSave($key, $schemas, $val, $data);
            }
        }

        $data->afterSave($data);
    }


    protected function doRelateSave($key, $schemas, $val, $data)
    {

        $datas=[];
        //考虑user.name 直接修改的方式.  但是有很多其他问题. 暂时不执行
//        if (stripos($key, '.') > 0) {
//            $keys = explode('.', $key, 2);
//
//            //如果直接key查不到 那就得搜索下as有没有
//            $schema = $schemas[$key] ?? null;
//            if (!$schema) {
//                $schema = Schemas::searchLinkAs($keys[0], $schemas);
//            }
//            $method = $keys[0];
//            $key=$keys[1];
//            $datas[$key]=$val;
//        } else {
            $schema = $schemas[$key] ?? null;
            if (!$schema) {
                $schema = Schemas::searchLinkAs($key, $schemas);
            }
            $datas=$val;
            $method = $key;
//        }

        if (!$schema) {
            return '';
        }
        if (isset($schema['link'])) {
            if ($data->$method() instanceof BelongsToMany) {
                $data->$method()->advSync($val);
            }
//            else if ($data->$method() instanceof BelongsTo) {
//                $model =  $data->load($method)[$method];
//                if($model){
//                    $this->setAttributes($model,$datas);
//                    $this->save();
//                }elseif(!$model){
////                    $model = $data->$method();
////                    $model= $model->getRelated();
////                    $data = $model::create($datas);
//                }
//            }
        }

    }

    protected function setAttributes($model,$data){
        foreach ($data as $k=>$val){
            $model->setAttribute($k,$val);
        }
        return $model;
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
