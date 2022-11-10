<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserCollection;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Exception;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    /**
     * @OA\Post(
     ** path="/v1/users",
     *   tags={"users"},
     *   summary="Get list of users",
     *   operationId="index",
     *
     *    @OA\Parameter(name="page", in="query", description="the page number", required=false,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     **/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::latest()->paginate(15);
        return new UserCollection($users);
    }




    /**
     * @OA\Post(
     *      path="/v1/user/create",
     *      operationId="store",
     *      tags={"users"},
     *      summary="Store user in DB",
     *      description="Store user in DB",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name", "email", "password", "password_confirmation", "mobile_number"},
     *            @OA\Property(property="name", type="string", format="string", example="user test"),
     *            @OA\Property(property="email", type="string", format="string", example="test@gmail.com"),
     *            @OA\Property(property="password", type="string", format="string", example="12345678"),
     *            @OA\Property(property="password_confirmation", type="string", format="string", example="12345678"),
     *            @OA\Property(property="mobile_number", type="string", format="string", example="09123456789"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(property="success",type="object")
     *          )
     *       )
     *  )
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'mobile_number' => 'required|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('authToken')->accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success' => $success])->setStatusCode(Response::HTTP_CREATED);
    }




    /**
     * @OA\Get(
     *    path="/v1/user/{user}",
     *    operationId="show",
     *    tags={"users"},
     *    summary="Get user Detail",
     *    description="Get user Detail",
     *    @OA\Parameter(name="user", in="path", description="Id of user", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        try {
            $cachedUser = Redis::get('user_' . $user);
            if (isset($cachedUser)) {
                $blog = json_decode($cachedUser, FALSE);
                return response()->json(
                    [
                        'data' => $blog,
                        'Meta' => [
                            'success' => 'عملیات شما با موفقیت انجام شد',
                            'status' =>  200
                        ]
                    ],
                );
            } else {
                Redis::set('user_' . $user, $user);
                return response()->json([
                    'data' => $user,
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }



    /**
     * @OA\Put(
     *     path="/v1/user/update/{user}",
     *     operationId="update",
     *     tags={"Users"},
     *     summary="Update user in DB",
     *     description="Update user in DB",
     *     @OA\Parameter(name="user", in="path", description="Id of user", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name", "email", "password", "password_confirmation", "mobile_number"},
     *            @OA\Property(property="name", type="string", format="string", example="user test"),
     *            @OA\Property(property="email", type="string", format="string", example="test@gmail.com"),
     *            @OA\Property(property="password", type="string", format="string", example="12345678"),
     *            @OA\Property(property="mobile_number", type="string", format="string", example="09123456789"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'mobile_number' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user->update($input);
        $user->tokens()->delete();
        $success['token'] =  $user->createToken('authToken')->accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success' => $success])->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * @OA\Delete(
     *    path="/v1/user/delete/{user}",
     *    operationId="destroy",
     *    tags={"Users"},
     *    summary="Delete User",
     *    description="Delete User",
     *    @OA\Parameter(name="name", in="path", description="Id of User", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *         @OA\Property(property="status_code", type="integer", example="200"),
     *         @OA\Property(property="data",type="object")
     *          ),
     *       )
     *      )
     *  )
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $user->tokens()->delete();
            $user->delete();
            return response()->json(['status' => 200, 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
}
