<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'column_name_of_change',
        'reason_for_change',
        'old_value',
        'new_value',
        'user_id',
    ];
    public function belongsToUser()
    {
        return $this->belongsTo(User::class);
    }

    public function belongsToProduct()
    {
        return $this->belongsTo(Product::class);
    }

}
