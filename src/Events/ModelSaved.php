<?php

namespace Larfree\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
                //如果是配置中的
                if (isset($schemas[$key])) {
                    $schema = $schemas[$key];
                    $method = $schema['key'];
                    if (isset($schema['link'])) {
                        $method = $schema['key'];
                        if ($data->$method() instanceof BelongsToMany) {
                            $data->$method()->sync($val);
                        }
                    }
                }
            }
        }
        $data->afterSave($data);
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
