<?php

namespace App\Http\Controllers;

use App\Http\Interface\RepositoryInterface;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Repository\Repository;
use App\Traits\GeneralResponse;
use App\Traits\UploadFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{

    use GeneralResponse, UploadFiles;
    protected RepositoryInterface $company;

    public function __construct()
    {
        $this->company = new Repository(new Company());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->company->index()->query()->paginate(10);
        return CompanyResource::collection($data);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->company->create([
                'title' => $request->title,
                'image' => $this->uploadImage($request, 'Company'),
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
        $data = $this->company->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }
        return  new CompanyResource($data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:companies,title|min:6',
        ]);

        $data = $this->company->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $data->update([
                'title' => $request->title,
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
        $data = $this->company->show($id);
        if (!$data) {
            return $this->errorResponse(__('Not found'), Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $this->company->destroy($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), Response::HTTP_CONFLICT);
        }
        return $this->successResponse(__('Deleted success'), Response::HTTP_OK);
    }
}