<?php

namespace Modules\Product\Http\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;



interface ProductControllerInterface
{
    /**
     * Lists all products.
     *
     * @return JsonResponse Returns a JSON response containing the list of products.
     * @throws \Exception If an error occurs while fetching the products.
     */
    public function list(): JsonResponse;

    /**
     * Creates a new product.
     *
     * @param Request $request Validated data for creating the product.
     * @return JsonResponse Returns a JSON response with the created product.
     * @throws ValidationException If there is a validation error with the data.
     */
    public function create(Request $request): JsonResponse;

    /**
     * Updates an existing product.
     *
     * @param Request $request Validated data for updating the product.
     * @param int $id ID of the product to be updated.
     * @return JsonResponse Returns a JSON response with the updated product.
     * @throws ModelNotFoundException If the product is not found.
     * @throws ValidationException If there is a validation error with the data.
     */
    public function update(Request $request, int $id): JsonResponse;

    /**
     * Deletes a product by its ID.
     *
     * @param Request $request Request data (not directly used).
     * @param int $id ID of the product to be deleted.
     * @return JsonResponse Returns a JSON response with a success message.
     * @throws ModelNotFoundException If the product is not found.
     */
    public function delete(Request $request, int $id): JsonResponse;
}

