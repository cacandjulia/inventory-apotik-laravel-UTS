<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'sku', 'name', 'purchase_price', 'selling_price', 'stock', 'minimum_stock'];

    public function category() { return $this->belongsTo(Category::class); }
    public function saleItems() { return $this->hasMany(SaleItem::class); }
    public function purchaseItems() { return $this->hasMany(PurchaseItem::class); }

    public function isLowStock(): bool { return $this->stock <= $this->minimum_stock; }

    public function scopeLowStock($q) { return $q->whereColumn('stock', '<=', 'minimum_stock'); }

    public function scopeSearch($q, ?string $term)
    {
        return $term ? $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%")) : $q;
    }
}
