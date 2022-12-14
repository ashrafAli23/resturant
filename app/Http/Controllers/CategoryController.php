<?php

namespace App\Http\Controllers;

use App\Http\Interface\RepositoryInterface;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repository\Repository;
use App\Traits\GeneralResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use GeneralResponse;
    protected RepositoryInterface $category;

    public function __construct()
    {
        $this->category = new Repository(new Category());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'required|numeric'
        ]);

        $perPage = $request->perPage ?? 10;

        $data = $this->category->index();
        if ($request->food) {
            $query_data = $data->with('food')->paginate($perPage);
        } else {
            $query_data = $data->query()->paginate($perPage);
        }

        return $this->dataResponse(['data', $query_data], Response::HTTP_OK);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->category->create([
                'en_title' => $request->en_title,
                'ar_title' => $request->ar_title,
                'is_active' => $request->is_active,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->successResponse(__('Created success'), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->category->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }



        return  $this->dataResponse(['data', $data], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $data = $this->category->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $data->update([
                'en_title' => $request->en_title,
                'ar_title' => $request->ar_title,
                'is_active' => $request->is_active,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
        }
        return $this->successResponse(__('Updated success'), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->category->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $this->category->destroy($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
        }
        return $this->successResponse(__('Deleted success'), Response::HTTP_OK);
    }
}