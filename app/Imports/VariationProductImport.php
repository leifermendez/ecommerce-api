<?php

namespace App\Imports;

use App\User;
use App\shop;
use App\products;
use App\product_attached;
use App\variation_product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\attached;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class VariationProductImport implements ToModel, WithHeadingRow, WithCustomCsvSettings, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $product_id = $this->storeProduct($row);
        $resp = $this->validateVariation($row, $product_id);
        if (!$resp) {
            $this->getImg($row, $product_id);
            return new variation_product([
                'price_normal' => $row['price_normal'],
                'price_regular' => $row['price_regular'],
                'product_id' => $product_id,
                'quantity' => $row['quantity'],
                'label' => $row['label_variation'],
                'observation'  => $row['observation'],
                'weight' => $row['weight'],
                'width' => $row['width'],
                'height' => $row['height'],
                'length' => $row['length'],
                'delivery' => ($row['delivery'] == 'si') ? true : false ,
                'status' => $row['status'],
            ]);
        }
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    public function rules(): array
    {
        return [
            'email_corporate' => function($attribute, $value, $onFailure) {
                $user = JWTAuth::parseToken()->authenticate();

                $shop = shop::where(['email_corporate' => $value, 'users_id' => $user->id])->first();
                if ($shop == null) {
                    $onFailure('Correo no existe para una tienda');
                }
            },
            'product' => 'required|string',
            'short_description' => 'required|string',
            'featured' => Rule::in(['premium', 'regular', 'not']),
            'product_type' => Rule::in(['digital', 'physical']),
            'description' => 'required|string',
            'label_product' => 'required',
            'price_normal' => 'required|numeric',
            'price_regular' => 'required|numeric',
            'quantity' => 'required|integer',
            'label_variation' => 'required',
            'observation' => 'required',
            'weight' => 'required|integer',
            'width' => 'required|integer',
            'height' => 'required|integer',
            'length' => 'required|integer',
            'delivery' => Rule::in(['si', 'no']),
            'img' =>'url',

        ];
    }

    public function customValidationMessages(){
        return [
            'product.required' => 'El campo es obligatorio',
            'product.string' => 'El campo debe ser alfanumerico',
            'short_description.required' => 'El campo es obligatorio',
            'short_description.string' => 'El campo debe ser alfanumerico',
            'featured.in' => 'Solo se permite: premium, regular, not',
            'product_type.required' => 'El campo es obligatorio',
            'product_type.in' => 'Solo se permite: digital, physical',
            'description.required' => 'El campo es obligatorio',
            'description.string' => 'El campo debe ser alfanumerico',
            'label_product.required' => 'El campo es obligatorio',
            'price_normal.required' => 'El campo es obligatorio',
            'price_normal.numeric' => 'El campo debe ser un numero',
            'price_regular.required' => 'El campo es obligatorio',
            'price_regular.numeric' => 'El campo debe ser un numero',
            'quantity.required' => 'El campo es obligatorio',
            'quantity.integer' => 'El campo debe ser un numero entero',
            'label_variation.required' => 'El campo es obligatorio',
            'observation.required' => 'El campo es obligatorio',
            'weight.required' => 'El campo es obligatorio',
            'weight.integer' => 'El campo debe ser un numero entero',
            'width.required' => 'El campo es obligatorio',
            'width.integer' => 'El campo debe ser un numero entero',
            'height.required' => 'El campo es obligatorio',
            'height.integer' => 'El campo debe ser un numero entero',
            'length.required' => 'El campo es obligatorio',
            'length.integer' => 'El campo debe ser un numero entero',
            'delivery.required' => 'El campo es obligatorio',
            'delivery.in' => 'Solo se permite: si, no',
            'img.url' => 'No es una url de imagen',
        ];
    }

    /**
     * Obtener el id de la tienda
     * @param  [type] $email [el email cooporativo de la tienda]
     * @return [type]        [id de la tienda / false]
     */
    public function getShop($email){
        $user = JWTAuth::parseToken()->authenticate();
        $shop = shop::where(['email_corporate' => $email, 'users_id' => $user->id])->first();
        return $shop->id;
    }

    /**
     * crear el producto si no existe
     * @param  [type] $row [la linea completa]
     * @return [type]      [id del producto / false]
     */
    public function storeProduct($row){
        $shop_id = $this->getShop($row['email_corporate']);
        $procutd = products::where('name',$row['product'])->first();
        if ($procutd) {
           return $procutd->id;
        }else{
            $procutd = new products();
            $procutd->name = $row['product'];
            $procutd->short_description = $row['short_description'];
            $procutd->featured = $row['featured'];
            $procutd->product_type = $row['product_type'];
            $procutd->description = $row['description'];
            $procutd->shop_id = $shop_id;
            $procutd->label = $row['label_product'];
            $procutd->status = 'available';
            $procutd->save();
            return $procutd->id;
        }
    }

    public function validateVariation($row, $product_id){
        $variation = variation_product::where(['product_id' => $product_id , 'label' => $row['label_variation'], ])->first();
        return ($variation) ? true : false;
    }

    public function getImg($row, $product_id){
        $user = JWTAuth::parseToken()->authenticate();
        $responseSize = [];
        $formato = substr($row['img'], -3);
        if ( $formato == 'peg' || $formato == 'jpg' || $formato == 'bmp' || $formato == 'png') {
            $formato = ($formato == 'peg') ? 'jpeg' : $formato ;
            $file = file_get_contents($row['img']);//obtener imagen
            $imageName = Str::random(35);
            $sizes = array(
                'small' => Image::make($file)
                    ->encode($formato, 100)
                    ->orientate()
                    ->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream()->__toString(),
                'medium' => Image::make($file)
                    ->encode($formato, 100)
                    ->orientate()
                    ->resize(600, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream()->__toString(),
                'large' => Image::make($file)
                    ->encode($formato, 100)
                    ->orientate()
                    ->resize(1600, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream()->__toString(),
            );

            foreach ($sizes as $key => $value) {
                $name_bulk = 'public/upload/products/' . $key . '_' . $imageName . '.' . $formato;
                Storage::disk()->put($name_bulk, $value);
                $responseSize[$key] = Storage::disk()->url($name_bulk);
            }

            $attached = new attached();
            $attached->name = $imageName . '.png';
            $attached->users_id = $user->id;
            $attached->large = $responseSize['large'];
            $attached->medium = $responseSize['medium'];
            $attached->small = $responseSize['small'];
            $attached->media_type = 'image';
            $attached->save();

            $p_attached = new product_attached();
            $p_attached->attached_id = $attached->id;
            $p_attached->product_id = $product_id;
            $p_attached->save();
        }
    }
}
