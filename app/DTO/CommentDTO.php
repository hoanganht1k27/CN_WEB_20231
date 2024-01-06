<?php

namespace App\DTO;

class CommentDTO {
    public $id;
    public UserDTO $author;
    public $blog_id;
    public $content;

    public function __construct($id, UserDTO $author, $blog_id, $content)
    {   
        $this->id = $id;
        $this->author = $author;
        $this->blog_id = $blog_id;
        $this->content = $content;
    }
}