<?php

namespace Iktickets\models;

use Iktickets\models\PostType;

class Page extends PostType
{
    const TYPE = "page";

    public function toJSON()
    {
        // Get all post for this page
        $data = get_post($this->id, OBJECT, 'raw');

        // Add meta data
        $data->meta = get_post_meta($this->id);

        // Add thumbnail
        $data->thumbnail = get_the_post_thumbnail_url($this->id);

        // Add others
        $data->link = $this->link();
        $data->slug = $this->slug();

        // foreach remove post_ from key name
        foreach ($data as $key => $value) {
            if (strpos($key, 'post_') === 0) {
                $newKey = substr($key, 5);
                $data->$newKey = $value;
                unset($data->$key);
            }
        }


        return json_encode($data);
    }
}
