<?php

namespace App\Http\Controllers\Api;

use App\Repositories\BlogRepository;
use App\Repositories\UserRepository;
use App\Services\MappingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Controller {

    private UserRepository $userRepository;
    private MappingService $mappingService;
    private BlogRepository $blogRepository;
    
    public function __construct(UserRepository $userRepository, MappingService $mappingService, BlogRepository $blogRepository)
    {
        $this->userRepository = $userRepository;
        $this->mappingService = $mappingService;
        $this->blogRepository = $blogRepository;
    }

    public function userRegister(Request $request) {
        try {
            $rules = [
                'email' => 'required',
                'password' => 'required',
                'name' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => json_encode($validator->errors()),
                ], 422);
            }
            $email = $request['email'];
            $password = $request['password'];
            $name = $request['name'];

            $user = $this->userRepository->getUserByEmail($email);
            if($user != null) {
                return response()->json([
                    'message' => 'User existed'
                ], 400);
            }

            $user = $this->userRepository->createUser($email, $password, $name);
            return response()->json([
                'message' => 'User registered successfully',
                'data' => $this->mappingService->mappingUser($user)
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function userLogin(Request $request) {
        try {
            $rules = [
                'email' => 'required',
                'password' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => json_encode($validator->errors()),
                ], 422);
            }
            $email = $request['email'];
            $password = $request['password'];

            $user = $this->userRepository->getUserByEmail($email);
            if($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if($user['password'] != $password) {
                return response()->json([
                    'message' => 'Password incorrect'
                ], 400);
            } else {
                return response()->json([
                    'message' => 'User login successfully',
                    'data' => $this->mappingService->mappingUser($user)
                ], 200);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function createBlog(Request $request) {
        try {
            $rules = [
                'user_email' => 'required',
                'title' => 'required',
                'resource' => 'required',
                'type' => 'required|in:image,video',
                'content' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => json_encode($validator->errors()),
                ], 422);
            }

            $userEmail = $request['user_email'];
            $user = $this->userRepository->getUserByEmail($userEmail);
            if($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }
            
            $resourceLink = '';

            if($request->hasFile('resource')) {
                $file = $request->file('resource');
                $fileName = $file->getClientOriginalName();
                $file->storeAs('images', $fileName);
                $path = $request->file('resource')->store('images');
                $baseUrl = env('APP_URL');
                $resourceLink = $baseUrl.'/'.$path;
            }

            $title = $request['title'];
            $type = $request['type'];
            $content = $request['content'];
            $blog = $this->blogRepository->createBlog(
                $user['id'],
                $title,
                $resourceLink,
                $type,
                $content
            );

            return response()->json([
                'message' => 'Blog created successfully',
                'data' => $this->mappingService->mappingBlog($blog, $user)
            ], 200);
            
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function getAllBlog(Request $request) {
        try {
            $blogs = $this->blogRepository->getBlog();
            $blogsDTO = [];
            foreach($blogs as $blog) {
                array_push($blogsDTO, $this->mappingService->mappingBlog($blog));
            }
            
            return response()->json([
                'message' => 'Successfully',
                'data' => $blogsDTO
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function addCommentToBlog(Request $request) {
        try {
            $rules = [
                'user_email' => 'required',
                'blog_id' => 'required',
                'content' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => json_encode($validator->errors()),
                ], 422);
            }

            $userEmail = $request['user_email'];
            $blogId = $request['blog_id'];
            $content = $request['content'];

            $user = $this->userRepository->getUserByEmail($userEmail);
            if($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $blog = $this->blogRepository->getBlogById($blogId);
            if($blog == null) {
                return response()->json([
                    'message' => 'Blog not found'
                ], 404);
            }

            $comment = $this->blogRepository->createComment($user['id'], $blog['id'], $content);

            return response()->json([
                'message' => 'Add comment successfully',
                'data' => $this->mappingService->mappingComment($comment, $user)
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function getCommentByBlogId(Request $request) {
        try {
            $rules = [
                'blog_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => json_encode($validator->errors()),
                ], 422);
            }

            $blogId = $request['blog_id'];

            $blog = $this->blogRepository->getBlogById($blogId);
            if($blog == null) {
                return response()->json([
                    'message' => 'Blog not found'
                ], 404);
            }

            $comments = $this->blogRepository->getCommentsByBlogId($blogId);
            $commentDTOs = [];

            foreach($comments as $comment) {
                array_push($commentDTOs, $this->mappingService->mappingComment($comment));
            }

            return response()->json([
                'message' => 'Successfully',
                'data' => $commentDTOs
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function deleteComments(Request $request) {
        try {
            $rules = [
                'comment_ids' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => json_encode($validator->errors()),
                ], 422);
            }

            $commentIds = json_decode($request['comment_ids'], true);

            foreach($commentIds as $commentId) {
                $this->blogRepository->deleteCommentById($commentId);
            }

            return response()->json([
                'message' => 'Successfully'
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}