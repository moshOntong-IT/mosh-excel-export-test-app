<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mosh\ExcelExportStreamer\Contracts\ExportableInterface;

class Order extends Model implements ExportableInterface
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_address',
        'billing_address',
        'notes',
        'shipped_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getExportColumns(): array
    {
        return ['id', 'order_number', 'user_id', 'status', 'total_amount', 'created_at', 'shipped_at'];
    }

    public function getExportHeaders(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Customer ID', 
            'Status',
            'Total Amount ($)',
            'Order Date',
            'Shipped Date'
        ];
    }

    public function transformForExport(): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'status' => ucfirst($this->status),
            'total_amount' => number_format($this->total_amount, 2),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'shipped_at' => $this->shipped_at ? $this->shipped_at->format('Y-m-d H:i:s') : 'Not Shipped',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}