<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mosh\ExcelExportStreamer\Contracts\ExportableInterface;

class Product extends Model implements ExportableInterface
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'sku',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getExportColumns(): array
    {
        return ['id', 'name', 'sku', 'price', 'category', 'stock_quantity', 'is_active', 'created_at'];
    }

    public function getExportHeaders(): array
    {
        return [
            'Product ID',
            'Product Name', 
            'SKU',
            'Price ($)',
            'Category',
            'Stock Quantity',
            'Status',
            'Created Date'
        ];
    }

    public function transformForExport(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => number_format($this->price, 2),
            'category' => $this->category,
            'stock_quantity' => $this->stock_quantity,
            'is_active' => $this->is_active ? 'Active' : 'Inactive',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}