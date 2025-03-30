<?php

namespace Modules\Product\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * Retrieve all products.
     *
     * @return Collection|Product[]
     */
    public function list(): Collection;

    /**
     * Find a product by its ID.
     *
     * @param int $id Product ID.
     * @return Product
     */
    public function find(int $id): Product;

    /**
     * Create a new product.
     *
     * @param array $data Product data.
     * @return Product
     */
    public function create(array $data): Product;

    /**
     * Update an existing product.
     *
     * @param int $id Product ID.
     * @param array $data Updated product data.
     * @return Product
     */
    public function update(int $id, array $data): Product;

    /**
     * Delete a product by its ID.
     *
     * @param int $id Product ID.
     * @return void
     */
    public function delete(int $id): void;
}
