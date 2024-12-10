<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function store(PostRequest $request)
    {
        $data= $request->validated();
        $data['client_id']=auth()->guard('client')->id();

        $post=Post::create($data);

        return ApiResponse::sendResponse(200,'post created successfully', new PostResource($post));
    }
    public function update(PostRequest $request,$id)
    {
        $post=Post::findOrFail($id);
        if ($post->client_id !=$request->user()->id){
            return ApiResponse::sendResponse(403,'you aren t allowed to take this action',[]);
        }
        $data= $request->validated();
        $updating = $post->update($data);
        if ($updating)return ApiResponse::sendResponse(200,'post updated successfully',[]);
    }
    public function delete($id)
    {
        $post=Post::findOrFail($id);
        if ($post->client_id !=auth()->user()->id){
            return ApiResponse::sendResponse(403,'you aren t allowed to take this action',[]);
        }
        $delete = $post->delete();
        if ($delete)return ApiResponse::sendResponse(200,'post deleted successfully',[]);
    }
    public function approve($id)
    {
        $post=Post::findOrFail($id);
        if ($post->client_id !=auth()->guard('client')->user()->id){
            return ApiResponse::sendResponse(403,'you aren t allowed to take this action',[]);
        }
        $updating = $post->update([
            'is_published'=>1,
            'publish_date'=>date('Y-m-d H:i:s')
        ]);
        if ($updating)return ApiResponse::sendResponse(200,'post approved successfully',[]);
    }

    public function getApproved()
    {
        $posts=Post::where('is_published',1)->orderBy('publish_date', 'desc')->get();
        if(count($posts) >0){
            return ApiResponse::sendResponse(200,'posts retrieved successfully',PostResource::collection($posts));
        }
        return ApiResponse::sendResponse(200,'posts not retrieved successfully',[]);
    }
    public function search(Request $request)
    {
        $word=$request->has('search') ? $request->input('search') : null;
        $posts=Post::when($word!=null,function ($q) use ($word){
            $q->where('title','like','%'.$word.'%');
        })->latest()->get();
        if (count($posts)>0){
            return ApiResponse::sendResponse(200,'Search Completed',PostResource::collection($posts));
        }
        return ApiResponse::sendResponse(200,'No Matching',null);
    }
}
