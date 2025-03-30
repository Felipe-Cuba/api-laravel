<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Product\Contracts\ProductRepositoryInterface;
use Modules\Product\Http\Contracts\ProductControllerInterface;
use Modules\Product\Models\Product;

class ProductController extends Controller implements ProductControllerInterface
{
    protected $productRepository;

    private $reverseStatusMap = [
        'em_estoque' => 1,
        'em_reposicao' => 2,
        'em_falta' => 3,
    ];

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
        $messages = $this->getRulesMessages();

        try {
            $productData = $request->validate($rules, $messages);

            $productData = $this->applyExtraValidations($productData, 'create');

            $product = $this->productRepository->create($productData);

            return response()->json($product->toArray(), 201);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'error' => 'Erro de validação.',
                    'details' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Um erro ocorreu enquanto o produto era criado.',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $rules = $this->getUpdateRules();
        $messages = $this->getRulesMessages();

        try {
            $productData = $request->validate($rules, $messages);

            $currentProduct = $this->productRepository->find($productId);

            $productData = $this->applyExtraValidations($productData, 'update', $currentProduct);

            $product = $this->productRepository->update($productId, $productData);

            return response()->json($product->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'error' => 'Produto não encontrado.',
                    'message' => $e->getMessage(),
                ],
                404,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'error' => 'Erro de validação.',
                    'details' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Um erro ocorreu enquanto o produto era atualizado.',
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

            return response()->json(['message' => 'Produto deletado com sucesso.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'error' => 'Produto não encontrado.',
                    'message' => $e->getMessage(),
                ],
                404,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Um erro ocorreu enquanto o produto era deletado.',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function applyExtraValidations(array $data, string $action, ?Product $product = null)
    {
        if ($action === 'create') {
            return $this->applyCreateExtraValidations($data);
        } elseif ($action === 'update' && $product) {
            return $this->applyUpdateExtraValidations($data, $product);
        }

        return $data;
    }

    private function applyCreateExtraValidations(array $data)
    {
        if ($data['status'] !== $this->reverseStatusMap['em_falta'] && $data['stock_quantity'] === 0) {
            $data['status'] = $this->reverseStatusMap['em_falta'];
        }

        return $data;
    }

    private function applyUpdateExtraValidations(array $data, Product $product)
    {
        $productStatus = array_key_exists('status', $data) ? $data['status'] : $product->status;
        $productStockQuantity = array_key_exists('stock_quantity', $data) ? $data['stock_quantity'] : $product->stock_quantity;

        if ($productStockQuantity === 0 && $productStatus !== $this->reverseStatusMap['em_falta']) {
            $data['status'] = $this->reverseStatusMap['em_falta'];
        }

        return $data;
    }

    private function getCreateRules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'required|integer|in:1,2,3',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }

    private function getUpdateRules(): array
    {
        $productId = request()->route('productId');

        return [
            'name' => "nullable|string|max:255|unique:products,name,{$productId}",
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'nullable|integer|in:1,2,3',
            'stock_quantity' => 'nullable|integer|min:0',
        ];
    }

    private function getRulesMessages(): array
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
            'status.in' => 'O campo status deve ser um dos seguintes valores: 1 (Em estoque), 2 (Em reposição), 3 (Em falta).',
            'status.integer' => 'O campo status deve ser um número inteiro.',
            'stock_quantity.required' => 'O campo quantidade em estoque é obrigatório.',
            'stock_quantity.integer' => 'O campo quantidade em estoque deve ser um número inteiro.',
            'stock_quantity.min' => 'O campo quantidade em estoque deve ser maior ou igual a 0.',
        ];

    }
}
