<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

/**
 * App\Model\StockOpname
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $stockMovement
 * @mixin \Eloquent
 */
class StockOpname extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'stock_opnames';

    protected $fillable = [
        'stock_id',
        'previous_quantity',
        'adjusted_quantity',
        'reason'
    ];

    public function hId()
    {
        return HashIds::encode($this->attributes['id']);
    }

    public function stockIn()
    {
        return $this->hasOne('App\Model\StockIn');
    }

    public function stockOut()
    {
        return $this->hasOne('App\Model\StockOut');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });

        static::updating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->updated_by = $user->id;
            }
        });

        static::deleting(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->deleted_by = $user->id;
                $model->save();
            }
        });
    }
}