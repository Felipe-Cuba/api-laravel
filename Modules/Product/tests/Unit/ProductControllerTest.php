<?php

namespace Modules\Product\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Models\Product;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $statusMap = [
        1 => 'Em estoque',
        2 => 'Em reposição',
        3 => 'Em falta',
    ];

    private $reverseStatusMap = [
        'em_estoque' => 1,
        'em_reposicao' => 2,
        'em_falta' => 3,
    ];

    public function test_list_products()
    {
        $productOne = Product::factory()->create();
        $productTwo = Product::factory()->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $response->assertJsonIsArray();
        $response->assertJsonCount(2);

        $jsonAssertData = [];

        foreach ([$productOne, $productTwo] as $product) {
            $jsonAssertData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'description' => $product->description,
                'status' => (int) $product->status,
                'status_string' => $this->statusMap[(int) $product->status],
                'stock_quantity' => (int) $product->stock_quantity,
                'created_at' => $product->created_at->toISOString(),
                'updated_at' => $product->updated_at->toISOString(),
            ];
        }

        $response->assertJson($jsonAssertData);

    }

    public function test_list_products_empty()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $response->assertJsonFragment([]);
    }

    public function test_create_product()
    {
        $productData = Product::factory()->make();

        $data = $productData->toArray();

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'name' => $data['name'],
            'price' => (float) $data['price'],
            'description' => $data['description'],
            'status' => (int) $data['status'],
            'status_string' => $this->statusMap[(int) $data['status']],
            'stock_quantity' => (int) $data['stock_quantity']
        ]);

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'status' => $data['status'],
            'stock_quantity' => $data['stock_quantity']
        ]);
    }

    public function test_create_product_with_invalid_data()
    {
        // Enviar dados inválidos (faltando nome)
        $data = [
            'price' => 200,
            'description' => 'Teclado de alta qualidade',
            'status' => $this->reverseStatusMap['em_estoque'],
            'stock_quantity' => 10
        ];

        // Realizando a requisição POST com dados inválidos
        $response = $this->postJson('/api/products', $data);

        // Verificando se o status de resposta é 422 (Erro de validação)
        $response->assertStatus(422);

        // Verificando se a resposta contém o erro de validação esperado
        $response->assertJsonFragment(
            [
                'error' => 'Erro de validação.',
                'details' => [
                    'name' => ['O campo nome é obrigatório.']
                ]
            ]
        );
    }

    public function test_create_product_with_stock_quantity_zero()
    {
        $productData = Product::factory()->make([
            'stock_quantity' => 0,
        ]);

        $data = $productData->toArray();

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'name' => $data['name'],
            'price' => (float) $data['price'],
            'description' => $data['description'],
            'status' => $this->reverseStatusMap['em_falta'],
            'status_string' => $this->statusMap[3],
            'stock_quantity' => 0,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'price' => $data['price'],
            'stock_quantity' => 0,
            'status' => $this->reverseStatusMap['em_falta']
        ]);
    }

    public function test_create_product_with_the_same_name_as_another_product()
    {
        $product = Product::factory()->create();

        $newProductData = Product::factory()->make([
            'name' => $product->name,
        ]);

        $data = $newProductData->toArray();

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'error' => 'Erro de validação.',
            'details' => [
                'name' => [
                    'O nome do produto já está em uso.'
                ]
            ]
        ]);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create();

        $newData = Product::factory()->make();

        $data = $newData->toArray();

        $response = $this->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => $data['name'],
            'price' => (float) $data['price'],
            'description' => $data['description'],
            'status' => (int) $data['status'],
            'status_string' => $this->statusMap[(int) $data['status']],
            'stock_quantity' => (int) $data['stock_quantity']
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $newData['name'],
            'price' => $newData['price'],
            'description' => $newData['description'],
            'status' => $newData['status'],
            'stock_quantity' => $newData['stock_quantity']
        ]);
    }

    public function test_update_product_not_found()
    {
        $response = $this->putJson('/api/products/999999', []);

        $response->assertStatus(404);

        $response->assertJsonFragment([
            'error' => 'Produto não encontrado.'
        ]);
    }

    public function test_update_product_with_only_one_field()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Novo Nome'
        ];

        $response = $this->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => 'Novo Nome',
            'price' => $product->price,
            'description' => $product->description,
            'status' => $product->status,
            'status_string' => $this->statusMap[$product->status],
            'stock_quantity' => $product->stock_quantity
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Novo Nome',
            'price' => $product->price,
            'description' => $product->description,
            'status' => $product->status,
            'stock_quantity' => $product->stock_quantity
        ]);
    }

    public function test_update_product_with_stock_quantity_zero()
    {
        $product = Product::factory()->create([
            'stock_quantity' => 10,
        ]);

        $data = [
            'price' => 500,
            'stock_quantity' => 0
        ];

        $response = $this->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => $product->name,
            'price' => 500,
            'description' => $product->description,
            'status' => $this->reverseStatusMap['em_falta'],
            'status_string' => $this->statusMap[3],
            'stock_quantity' => 0
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
            'price' => 500,
            'description' => $product->description,
            'status' => $this->reverseStatusMap['em_falta'],
            'stock_quantity' => 0
        ]);
    }

    public function test_update_product_with_invalid_data()
    {
        $product = Product::factory()->create();

        $randomString = bin2hex(random_bytes(150));

        // Enviar dados inválidos (faltando nome)
        $data = [
            'name' => $randomString,
            'price' => 200.768,
            'description' => 'Teclado de alta qualidade',
            'status' => 'inactive',
            'stock_quantity' => -1
        ];

        // Realizando a requisição PUT com dados inválidos
        $response = $this->putJson("/api/products/{$product->id}", $data);

        // Verificando se o status de resposta é 422 (Erro de validação)
        $response->assertStatus(422);

        // Verificando se a resposta contém o erro de validação esperado
        $response->assertJson([
            'error' => 'Erro de validação.',
            'details' => [
                'stock_quantity' => ['O campo quantidade em estoque deve ser maior ou igual a 0.'],
                'price' => ['O campo preço deve ter no máximo duas casas decimais.'],
                'status' => [
                    'O campo status deve ser um número inteiro.',
                    'O campo status deve ser um dos seguintes valores: 1 (Em estoque), 2 (Em reposição), 3 (Em falta).',
                ]
            ]
        ]);
    }

    public function test_update_product_with_the_same_name_as_another_product()
    {
        $productOne = Product::factory()->create();

        $productTwo = Product::factory()->create();

        $data = [
            'name' => $productTwo->name,
            'status' => $this->reverseStatusMap['em_estoque'],
            'stock_quantity' => 10
        ];

        $response = $this->putJson("/api/products/{$productOne->id}", $data);

        $response->assertStatus(422);

        $response->assertJsonFragment(
            [
                'error' => 'Erro de validação.',
                'details' => [
                    'name' => ['O nome do produto já está em uso.']
                ]
            ]
        );
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => 'Produto deletado com sucesso.'
        ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_delete_product_not_found()
    {
        $response = $this->deleteJson('/api/products/999999');

        $response->assertStatus(404);

        $response->assertJsonFragment([
            'error' => 'Produto não encontrado.'
        ]);
    }
}
