<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Models\Comment;

class BlogRepository {
    public function createBlog($userId, $title, $resourceLink, $type, $content) {
        $blog = new Blog();
        $blog['user_id'] = $userId;
        $blog['title'] = $title;
        $blog['resource_link'] = $resourceLink;
        $blog['type'] = $type;
        $blog['content'] = $content;
        $blog->save();
        return $blog;
    }

    public function getBlog() {
        return Blog::query()->orderBy('created_at', 'desc')->get();
    }

    public function getBlogById($blogId) {
        return Blog::query()->where('id', '=', $blogId)->first();
    }

    public function createComment($userId, $blogId, $content) {
        $comment = new Comment();
        $comment['user_id'] = $userId;
        $comment['blog_id'] = $blogId;
        $comment['content'] = $content;
        $comment->save();
        return $comment;
    }

    public function getCommentsByBlogId($blogId) {
        return Comment::query()->where('blog_id', '=', $blogId)->orderBy('created_at', 'desc')->get();
    }

    public function getCommentById($commentId) {
        return Comment::query()->where('id', '=', $commentId)->first();
    }

    public function deleteCommentById($commentId) {
        $comment = $this->getCommentById($commentId);
        if($comment != null) {
            $comment->delete();
        }

        return true;
    }
}