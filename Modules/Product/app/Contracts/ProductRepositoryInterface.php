<?php

namespace Modules\Product\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;

interface ProductRepositoryInterface
{
    public function list(): Collection;
    public function find(int $id): Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): void;
}
