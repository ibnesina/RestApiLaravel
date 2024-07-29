<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        if($flag == 1) {
            $users = User::select('email', 'name')->where('status', 1)->get();
        }
        else if($flag == 0) {
            $users = User::select('email', 'name')->get();
        }
        else {
            return response()->json([
                'message' => 'Parameter can be either 0 or 1',
                'status' => 0
            ], 400);
        }
        

        if(count($users)>0) {
            $response = [
                'message' => count($users) . ' users found',
                'status' => 1, 
                'data' => $users
            ];
        }
        else {
            $response = [
                'message' => count($users) . ' users found',
                'status' => 0, 
            ];
        }
        return response()->json($response, 200);
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
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required']
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        // Begin transaction
        DB::beginTransaction();

        // Data for user creation
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password instead of name
        ];

        try {
            // Create user
            $user = User::create($data);
            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user // Optionally return the created user
            ], 201);
        } catch (Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Return error response
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }   


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if(is_null($user)) {
            $response = [
                'message' => 'User not found',
                'status' => 0
            ];
        }
        else {
            $response = [
                'message' => 'User found',
                'status' => 0,
                'data' => $user
            ];
        }

        return response()->json($response, 200);
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
        $user = User::find($id);
        // p($request->all());
        // die;

        if(is_null($user)) {
            $response = [
                'message' => 'User doesn\'t exist',
            ];
            $respCode = 404;
        }
        else {
            DB::beginTransaction();

            try {
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->pincode = $request['pincode'];
                $user->address = $request['address'];
                $user->save();
                DB::commit();
                $response = [
                    'message' => 'User Updated Successfully',
                    'status' => 1
                ];
                $respCode = 200;
            }
            catch(Exception $e) {
                DB::rollBack();

                $response = [
                    'message' => 'Internal Server error',
                    'status' => 0,
                    'error' => $e->getMessage(),
                ];

                $respCode = 500;
            }
        }

        return response()->json($response, $respCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(is_null($user)) {
            $response = [
                'message' => 'User doesn\'t exist',
            ];
            $respCode = 404;
        }
        else {
            DB::beginTransaction();

            try {
                $user->delete();
                DB::commit();
                $response = [
                    'message' => 'User deleted Successfully',
                    'status' => 1
                ];
                $respCode = 200;
            }
            catch(Exception $e) {
                DB::rollBack();

                $response = [
                    'message' => 'Internal Server error',
                    'status' => 0
                ];

                $respCode = 500;
            }
        }

        return response()->json($response, $respCode);
    }

    public function changePassword(Request $request, $id) {
        $user = User::find($id);
        $response = [];
        $respCode = 200;

        if (is_null($user)) {
            $response = [
                'message' => 'User doesn\'t exist',
            ];
            $respCode = 404;
        } else {
            // Check if the old password matches
            if (Hash::check($request['old_password'], $user->password)) {
                // Check if the new password and confirm password match
                if ($request['password'] == $request['confirm_password']) {
                    DB::beginTransaction();
                    try {
                        $user->password = Hash::make($request['password']);
                        $user->save();
                        DB::commit();
                        $response = [
                            'message' => 'Password updated successfully',
                            'status' => 1
                        ];
                    } catch (Exception $e) {
                        DB::rollBack();
                        $response = [
                            'message' => 'Internal Server Error',
                            'status' => 0
                        ];
                        $respCode = 500;
                    }
                } else {
                    $response = [
                        'message' => 'Password confirmation doesn\'t match',
                        'status' => 0
                    ];
                    $respCode = 400;
                }
            } else {
                $response = [
                    'message' => 'Old password doesn\'t match',
                    'status' => 0
                ];
                $respCode = 400;
            }
        }

        return response()->json($response, $respCode);
    }

}
