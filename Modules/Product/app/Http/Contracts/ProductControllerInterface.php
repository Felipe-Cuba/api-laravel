<?php

namespace Modules\Product\Http\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;



interface ProductControllerInterface
{
    /**
     * Lista todos os produtos.
     *
     * @return JsonResponse Retorna uma resposta JSON contendo a lista de produtos.
     * @throws \Exception Caso ocorra um erro ao buscar os produtos.
     */
    public function list(): JsonResponse;
    /**
     * Cria um novo produto.
     *
     * @param Request $request Dados validados para criação do produto.
     * @return JsonResponse Retorna uma resposta JSON com o produto criado.
     * @throws ValidationException Caso ocorra um erro de validação nos dados.
     */
    public function create(Request $request): JsonResponse;
    /**
     * Atualiza um produto existente.
     *
     * @param Request $request Dados validados para atualização do produto.
     * @param int $productId ID do produto que será atualizado.
     * @return JsonResponse Retorna uma resposta JSON com o produto atualizado.
     * @throws ModelNotFoundException Caso o produto não seja encontrado.
     * @throws ValidationException Caso ocorra um erro de validação nos dados.
     */
    public function update(Request $request, int $id): JsonResponse;
    /**
     * Exclui um produto pelo ID.
     *
     * @param Request $request Dados da requisição (não utilizados diretamente).
     * @param int $productId ID do produto que será excluído.
     * @return JsonResponse Retorna uma resposta JSON com uma mensagem de sucesso.
     * @throws ModelNotFoundException Caso o produto não seja encontrado.
     */
    public function delete(Request $request, int $id): JsonResponse;
}
