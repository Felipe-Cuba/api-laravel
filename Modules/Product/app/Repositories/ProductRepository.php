<?php

namespace Modules\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Contracts\ProductRepositoryInterface;
use Modules\Product\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function list(): Collection
    {
        $products = $this->model->all();

        return $products;
    }

    public function find(int $id): Product
    {
        $product = $this->model->findOrFail($id);

        return $product;
    }

    public function create(array $data): Product
    {
        $product = $this->model->create($data);

        return $product;
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->model->findOrFail($id);

        $product->update($data);

        return $product;
    }

    public function delete(int $id): void
    {
        $product = $this->model->findOrFail($id);

        $product->delete();
    }
}
