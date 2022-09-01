<?php


namespace App\Http\Interface;

interface RepositoryInterface
{
    public function index();
    public function create(array $data);
    public function update(array $data, $id);
    public function show($id);
    public function destroy($id);
}