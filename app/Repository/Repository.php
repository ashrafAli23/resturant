<?php


namespace App\Repository;

use App\Http\Interface\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class Repository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->query()->paginate(10);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $find = $this->model->find($id);
        return $find->update($data);
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function destroy($id)
    {
        return $this->model->destroy($id);
    }
}