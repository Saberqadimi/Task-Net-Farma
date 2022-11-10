<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ArticleCollection;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Models\Article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $Articles = Article::latest()->paginate(15);
        return new  ArticleCollection($Articles);
    }



    public function store(Request $request)
    {

        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error'
            ], 422);
        }
        $inputs = $request->all();
        $Article = auth()->user()->articles()->create($inputs);
        return response()->json([
            'data' => $Article,
            'success' => 'مقاله شما با موفقیت ساخته شد.'
        ], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {

        try {
            $cachedBlog = Redis::get('blog_' . $article);

            if (isset($cachedBlog)) {
                $blog = json_decode($cachedBlog, FALSE);
                return response()->json(
                    [
                        'data' => $blog,
                        'Meta' => [
                            'success' => 'عملیات شما با موفقیت انجام شد',
                            'status' =>  200
                        ]
                    ],
                );
                // return new  ArticleCollection($blog);
            } else {
                Redis::set('blog_' . $article, $article);
                return response()->json([
                    'data' => $article,
                ]);
                // return new  ArticleCollection($article);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }


    public function update(Request $request, Article $article)
    {
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error'
            ], 422);
        }
        $inputs = $request->all();
        $item = $article->update($inputs);
        return response()->json([
            'data' => $item,
            'success' => 'مقاله شما با موفقیت آپدیت شد.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $Article)
    {
        try {
            $Article->delete();
            return response()->json(['status' => 200, 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    public function validateRequest($request)
    {
        return  Validator::make($request->all(), [
            'title' => 'required|unique:articles|max:255',
            'description' => 'required',
            'image' => 'required',
        ]);
    }
}
