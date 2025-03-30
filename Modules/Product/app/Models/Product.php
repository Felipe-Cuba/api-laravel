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

    /**
     * Convert the price attribute to a float when accessed.
     */
    public function getPriceAttribute($value)
    {
        return (float) $value;
    }

    /**
     * Convert the stock_quantity attribute to an integer when accessed.
     */
    public function getStockQuantityAttribute($value)
    {
        return (int) $value;
    }

    /**
     * Convert the status attribute to an integer when accessed.
     */
    public function getStatusAttribute($value)
    {
        return (int) $value;
    }

    /**
     * Convert the status string attribute to a readable string when accessed.
     */
    public function getStatusStringAttribute($value)
    {
        $statusMap = [
            1 => 'Em estoque',
            2 => 'Em reposição',
            3 => 'Em falta',
        ];

        return $statusMap[$this->status] ?? 'Em falta';
    }

    /**
     * Transform the model into an array.
     */
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
