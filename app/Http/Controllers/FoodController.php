<?php

namespace App\Http\Controllers;

use App\Http\Interface\RepositoryInterface;
use App\Http\Requests\FoodRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FoodResource;
use App\Models\Food;
use App\Repository\Repository;
use App\Traits\GeneralResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FoodController extends Controller
{
    use GeneralResponse;
    protected RepositoryInterface $food;

    public function __construct()
    {
        $this->food = new Repository(new Food());
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
        $foods = $this->food->index();
        $foods_querey = $foods->with('category');
        // query()->with('category');

        if ($request->search) {
            if (app()->getLocale() === 'en') {
                $foods_querey->where('en_title', 'LIKE', '%' . $request->search . '%');
            } else {
                $foods_querey->where('ar_title', 'LIKE', '%' . $request->search . '%');
            }
        }

        if ($request->category) {
            $foods_querey->whereHas('category', function ($query) use ($request) {
                if (app()->getLocale() === 'en') {
                    $query->where('en_title', $request->category);
                } else {
                    $query->where('ar_title', $request->category);
                }
            });
        }

        if ($request->orderBy && in_array($request->orderBy, ['id', 'created_at'])) {
            $foods_querey->orderBY($request->orderBy, 'desc');
        }

        $foods_data = $foods_querey->paginate($perPage);

        return $this->dataResponse(['data', $foods_data], Response::HTTP_OK);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FoodRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->food->create([
                'en_title' => $request->en_title,
                'ar_title' => $request->ar_title,
                'en_description' => $request->en_description,
                'ar_description' => $request->ar_description,
                'category_id' => $request->category_id
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
        $data = Food::with('category')->where('id', $id)->first();

        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }


        return  $this->dataResponse(['data' => $data], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FoodRequest $request, $id)
    {
        $data = $this->food->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $data->update([
                'en_title' => $request->en_title,
                'ar_title' => $request->ar_title,
                'en_description' => $request->en_description,
                'ar_description' => $request->ar_description,
                'category_id' => $request->category_id
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
        $data = $this->food->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $this->food->destroy($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
        }
        return $this->successResponse(__('Deleted success'), Response::HTTP_OK);
    }
}