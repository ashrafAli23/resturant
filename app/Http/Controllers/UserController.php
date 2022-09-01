<?php

namespace App\Http\Controllers;

use App\Http\Interface\RepositoryInterface;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repository\Repository;
use App\Traits\GeneralResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use GeneralResponse;
    protected RepositoryInterface $user;

    public function __construct()
    {
        $this->user = new Repository(new User());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user->index();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        if (auth()->user()->role === 'super-admin') {
            try {
                DB::beginTransaction();
                $this->user->create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
            }
            return $this->successResponse(__('Created success'), Response::HTTP_CREATED);
        }

        return $this->errorResponse(__('Invalid permissions'), Response::HTTP_CONFLICT);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->user->show($id);
        return $this->dataResponse(['data' => $data], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id)
    {
        if (auth()->user()->role === "super-admin" || intval($id) === intval(auth()->user()->id)) {
            $request->validate([
                'password' => 'required|min:6'
            ]);

            $data = $this->user->show($id);
            if (!$data) {
                return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
            }

            $data->update([
                'password' => Hash::make($request->password)
            ]);
            return $this->successResponse(__('Updated success'), Response::HTTP_OK);
        }

        return $this->errorResponse(__('Invalid permissions'), Response::HTTP_CONFLICT);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role === "super-admin" || intval($id) === intval(auth()->user()->id)) {
            $request->validate([
                'name' => 'required|min:6|string'
            ]);

            $data = $this->user->show($id);
            if (!$data) {
                return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
            }

            $data->update([
                'name' => $request->name
            ]);
            return $this->successResponse(__('Updated success'), Response::HTTP_OK);
        }
        return $this->errorResponse(__('Invalid permissions'), Response::HTTP_CONFLICT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        if (auth()->user()->role === "super-admin" || intval($id) === intval(auth()->user()->id)) {
            $data = $this->user->show($id);
            if (!$data) {
                return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
            }

            if ($data->role === "super-admin") {
                return $this->errorResponse(__('You cant delete super-admin'), Response::HTTP_CONFLICT);
            }
            $this->user->destroy($id);
            return $this->successResponse(__('Deleted success'), Response::HTTP_OK);
        }

        return $this->errorResponse(__('Invalid permissions'), Response::HTTP_CONFLICT);
    }
}