<?php
/**
 * Created by PhpStorm.
 * User: GitzJoey
 * Date: 9/22/2016
 * Time: 3:16 AM
 */

namespace App;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;

/**
 * App\VendorTrucking
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $name
 * @property string $address
 * @property string $tax_id
 * @property string $status
 * @property string $remarks
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereStoreId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereTaxId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereRemarks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereDeletedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\VendorTrucking whereDeletedAt($value)
 * @mixin \Eloquent
 */
class VendorTrucking extends Model
{
    protected $table = 'vendor_trucking';

    protected $fillable = [
        'store_id', 'name', 'address', 'tax_id', 'status', 'remarks'
    ];

    public function hId() {
        return HashIds::encode($this->attributes['id']);
    }

    public function getBankAccount()
    {
        $this->hasMany('App\BankAccount');
    }
}