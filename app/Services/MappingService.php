<?php

namespace App\Services;

use App\DTO\BlogDTO;
use App\DTO\CommentDTO;
use App\DTO\UserDTO;
use App\Models\Blog;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class MappingService {
    private UserRepository $userRepository;
    private BlogRepository $blogRepository;

    public function __construct(
        UserRepository $userRepository,
        BlogRepository $blogRepository
    ) {
        $this->userRepository = $userRepository;
        $this->blogRepository = $blogRepository;   
    }

    public function mappingUser(User $user) {
        return new UserDTO($user['email'], $user['name']);
    }

    public function mappingBlog($blog, User $user = null) {
        if($user == null) {
            $user = $this->userRepository->getUserById($blog['user_id']);
        }
        return new BlogDTO(
            $blog['id'],
            $blog['title'],
            $blog['resource_link'],
            $blog['type'],
            $blog['content'],
            $this->mappingUser($user)
        );
    }

    public function mappingComment($comment, User $user = null) {
        if($user == null) {
            $user = $this->userRepository->getUserById($comment['user_id']);
        }
        return new CommentDTO(
            $comment['id'],
            $this->mappingUser($user),
            $comment['blog_id'],
            $comment['content']
        );
    }
}