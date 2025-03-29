<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Product\Contracts\ProductRepositoryInterface;
use Modules\Product\Http\Contracts\ProductControllerInterface;
use Modules\Product\Http\Requests\CreateProductRequest;
use Modules\Product\Models\Product;

class ProductController extends Controller implements ProductControllerInterface
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function list(): JsonResponse
    {
        try {
            $products = $this->productRepository->list()->map(callback: fn(Product $product): array => $product->toArray());

            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'An error occurred while fetching the products.',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function create(Request $request): JsonResponse
    {
        $rules = $this->getCreateRules();
        $messages = $this->getCreateRulesMessages();

        try {
            $productData = $request->validate($rules, $messages);

            if ($productData['status'] === 'em_estoque' && $productData['stock_quantity'] === 0) {
                $productData['status'] = 'em_falta';
            }

            $product = $this->productRepository->create($productData);


            return response()->json($product->toArray(), 201);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'error' => 'Validation error.',
                    'message' => $e->getMessage(),
                    'details' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'An error occurred while creating the product.',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $rules = $this->getUpdateRules();
        $messages = $this->getUpdateRulesMessages();

        try {
            $productData = $request->validate($rules, $messages);

            $currentProduct = $this->productRepository->find($productId);

            if ($currentProduct->status === 'em_estoque' && $productData['stock_quantity'] === 0) {

                $productData['status'] = 'em_falta';

            }

            $product = $this->productRepository->update($productId, $productData);

            return response()->json($product->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'error' => 'Product not found.',
                    'message' => $e->getMessage(),
                ],
                404,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'error' => 'Validation error.',
                    'message' => $e->getMessage(),
                    'details' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'An error occurred while updating the product.',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function delete(Request $request, int $productId): JsonResponse
    {
        try {
            // Attempt to delete the product
            $this->productRepository->delete($productId);

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'error' => 'Product not found.',
                    'message' => $e->getMessage(),
                ],
                404,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'An error occurred while deleting the product.',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function getCreateRules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'required|in:em_estoque,em_reposicao,em_falta',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }

    private function getCreateRulesMessages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',
            'name.unique' => 'O nome do produto já está em uso.',
            'description.string' => 'O campo descrição deve ser uma string.',
            'price.required' => 'O campo preço é obrigatório.',
            'price.numeric' => 'O campo preço deve ser um número.',
            'price.min' => 'O campo preço deve ser maior ou igual a 0.',
            'price.regex' => 'O campo preço deve ter no máximo duas casas decimais.',
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser um dos seguintes valores: em_estoque, em_reposicao, em_falta.',
            'stock_quantity.required' => 'O campo quantidade em estoque é obrigatório.',
            'stock_quantity.integer' => 'O campo quantidade em estoque deve ser um número inteiro.',
            'stock_quantity.min' => 'O campo quantidade em estoque deve ser maior ou igual a 0.',
        ];
    }

    private function getUpdateRules(): array
    {
        $productId = request()->route('productId');

        return [
            'name' => "nullable|string|max:255|unique:products,name,{$productId}",
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'nullable|in:em_estoque,em_reposicao,em_falta',
            'stock_quantity' => 'nullable|integer|min:0',
        ];
    }

    private function getUpdateRulesMessages(): array
    {
        return [
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',
            'name.unique' => 'O nome do produto já está em uso.',
            'description.string' => 'O campo descrição deve ser uma string.',
            'price.numeric' => 'O campo preço deve ser um número.',
            'price.min' => 'O campo preço deve ser maior ou igual a 0.',
            'price.regex' => 'O campo preço deve ter no máximo duas casas decimais.',
            'status.in' => 'O campo status deve ser um dos seguintes valores: em_estoque, em_reposicao, em_falta.',
            'stock_quantity.integer' => 'O campo quantidade em estoque deve ser um número inteiro.',
            'stock_quantity.min' => 'O campo quantidade em estoque deve ser maior ou igual a 0.',
        ];
    }
}
