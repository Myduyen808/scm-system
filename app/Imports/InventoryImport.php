<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'regular_price' => $row['regular_price'],
            'stock_quantity' => $row['stock_quantity'],
            'supplier_id' => $row['supplier_id'],
            'sku' => $row['sku'] ?? 'AUTO_' . rand(1000, 9999), // Tự động nếu không có
            'description' => $row['description'] ?? null,
            'sale_price' => $row['sale_price'] ?? null,
            'is_active' => $row['is_active'] ?? true,
        ]);
    }
}
