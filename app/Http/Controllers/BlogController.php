<?php

namespace App\Http\Controllers;

use App\Http\Interface\RepositoryInterface;
use App\Http\Requests\BlogRequest;
use App\Http\Resources\EventsResource;
use App\Models\blog;
use App\Repository\Repository;
use App\Traits\GeneralResponse;
use App\Traits\UploadFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    use GeneralResponse, UploadFiles;
    protected RepositoryInterface $blogs;

    public function __construct()
    {
        $this->blogs = new Repository(new blog());
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
        $data = $this->blogs->index()->query()->paginate($perPage);

        return $this->dataResponse(['data' => $data], Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->blogs->create([
                'en_title' => $request->en_title,
                'ar_title' => $request->ar_title,
                'en_description' => $request->en_description,
                'ar_description' => $request->ar_description,
                'image' => $this->uploadImage($request, 'Events'),
                'date' => $request->date
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
        $data = $this->blogs->show($id);
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
    public function update(BlogRequest $request, $id)
    {
        $data = $this->blogs->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            if ($request->file('image') && $request->file('image') !== null) {
                DB::beginTransaction();
                $data->update([
                    'image' => $this->uploadImage($request, 'Blogs'),
                ]);
                DB::commit();
            }
            DB::beginTransaction();
            $data->update([
                'en_title' => $request->en_title,
                'ar_title' => $request->ar_title,
                'en_description' => $request->en_description,
                'ar_description' => $request->ar_description,
                'date' => $request->date
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
        $data = $this->blogs->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $this->blogs->destroy($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
        }
        return $this->successResponse(__('Deleted success'), Response::HTTP_OK);
    }
}