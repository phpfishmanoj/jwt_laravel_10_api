<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'contact' => 'required|numeric|digits_between:10,11',
            'dob' => 'required|date|date_format:Y-m-d|before:' . \Carbon\Carbon::now()->subYears(18)->format('Y-m-d'),
        ], [
            'dob.before' => 'Age must be 18+.',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'status' => 200,
            'message' => 'User successfully created',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['status' => 200, 'message' => 'User successfully signed out']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }
    public function profile()
    {
        try {
            return response()->json(auth()->user());
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update_profile(User $user)
    {
        //dd($user);
        request()->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'password' => 'sometimes|min:8',
            'contact' => 'required|numeric|digits_between:10,11',
            'dob' => 'required|date|date_format:Y-m-d|before:' . \Carbon\Carbon::now()->subYears(18)->format('Y-m-d'),
        ], [
            'dob.before' => 'Age must be 18+.',

        ]);
        try {

            $user = Auth::user();
            $user->name = request('name');
            $user->email = request('email');
            $user->dob = request('dob');
            $user->password = request('password') ? bcrypt(request('password')) : auth()->user()->password;
            $user->updated_at = now();
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'User successfully updated',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
