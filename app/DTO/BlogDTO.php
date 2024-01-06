<?php

namespace App\DTO;

class BlogDTO {
    public $id;
    public $title;
    public $resource_link;
    public $type;
    public $content;
    public UserDTO $author;

    public function __construct(
        $id,
        $title,
        $resource_link,
        $type,
        $content,
        UserDTO $author
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->resource_link = $resource_link;
        $this->type = $type;
        $this->content = $content;
        $this->author = $author;
    }
}