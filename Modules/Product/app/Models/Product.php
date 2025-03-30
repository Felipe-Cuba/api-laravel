<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * Factory to create instances of the model Product.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Product\Database\Factories\ProductFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'description', 'price', 'status', 'stock_quantity'];

    public function getPriceAttribute($value)
    {
        return (float) $value;
    }

    public function getStockQuantityAttribute($value)
    {
        return (int) $value;
    }

    public function getStatusAttribute($value)
    {
        return (int) $value;
    }

    public function getStatusStringAttribute($value)
    {
        $statusMap = [
            1 => 'Em estoque',
            2 => 'Em reposição',
            3 => 'Em falta',
        ];

        return $statusMap[$this->status] ?? 'Em falta';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'status' => $this->status,
            'status_string' => $this->statusString,
            'stock_quantity' => $this->stock_quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
