<?php

namespace App\Models;

use DateTimeInterface;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\MStore;
use App\Models\MObjType;
use App\Models\MSettingDoc;
use Illuminate\Http\JsonResponse;

class PublicModel extends Model
{

    public function array_respon_200_table_tr(
        $todos,
        $count,
        $arr_pagination,
        $total_bpitem_null = 0,
        $confirm_approve = null,
        $status_save_PenerimaanBarang = 0
    ) {
        return [
            'status_save_PenerimaanBarang' => $status_save_PenerimaanBarang,
            'total_bpitem_null' => $total_bpitem_null,
            'confirm_approve' => $confirm_approve,
            'nomorBaris' => $arr_pagination['nomorBaris'],
            'count' => $count,
            'next' =>  $arr_pagination['next'],
            'previous' =>  $arr_pagination['previous'],
            'results' => $todos,
        ];
    }


    public function successResponse(
        mixed $data,
        mixed $count,
        array $pagination
    ): JsonResponse {
        return response()->json([
            'nomorBaris' => $pagination['nomorBaris'],
            'count' => $count,
            'next' =>  $pagination['next'],
            'previous' =>  $pagination['previous'],
            'results' => $data,
        ], 200);
    }

    public function paginateDataWithoutSearchQuery($URL, $limit, $offset,$depcode, $start, $end)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?offset=$offset_next&limit=$limit&depcode=$depcode&start=$start&end=$end";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?offset=$offset_previous&limit=$limit&depcode=$depcode&start=$start&end=$end";
        }

        return ['nomorBaris' => (int)$nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset];
    }


    public function paginateDataWithSearchQuery($URL, $limit, $offset, $search)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?search=$search&offset=$offset_next&limit=$limit";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?search=$search&offset=$offset_previous&limit=$limit";
        }


        return ['nomorBaris' => (int)$nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset, 'search' => $search];
    }



    public function pagination_without_search_inventory($URL, $limit, $offset, $storeid)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?offset=$offset_next&limit=$limit&storeid=$storeid";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?offset=$offset_previous&limit=$limit&storeid=$storeid";
        }

        return ['nomorBaris' => (int)$nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset];
    }


    public function pagination_with_search_inventory($URL, $limit, $offset, $search, $storeid)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?search=$search&offset=$offset_next&limit=$limit&storeid=$storeid";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?search=$search&offset=$offset_previous&limit=$limit&storeid=$storeid";
        }


        return ['nomorBaris' => (int)$nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset, 'search' => $search];
    }



    public function generate_docNum($storeCode, $objTypeCode)
    {
        //When using query builder
        if (empty(DB::table('m_setting_doc')->count())) {
            // Do something here

            $MStore = MStore::get(); // both get and all  will work here
            $MObjType = MObjType::get(); // both get and all  will work here

            $MStore_decode = json_decode($MStore, true);
            $MObjType_decode = json_decode($MObjType, true);


            foreach ($MStore_decode as $key => $value) {
                foreach ($MObjType_decode as $key2 => $value2) {
                    $array = array();

                    $array['objTypeCode'] = $value2['objTypeCode'];
                    $array['storeCode'] = $value['storeCode'];
                    $array['docNum'] = $value2['docNum'];
                    $array['prefix'] = $value2['prefix'];
                    $array['year'] = date('y');


                    $array['created_at'] =  date('Y-m-d H:i:s');
                    $array['created_by'] =  'automatic';
                    $idx = MSettingDoc::create($array)->id;
                }
            }
        }


        //GET docNum untuk kebutuhan dokumen
        $docNumxx =  MSettingDoc::select('*')
            ->where('storeCode', $storeCode)
            ->where('objTypeCode', "$objTypeCode")
            ->get()->toArray();



        //setting docnum jika berubah tahun
        if (date('y') != $docNumxx[0]['year']) {
            DB::table('m_setting_doc')
                ->update(['year' => date('y'), 'docNum' => 1]);
        }


        //GET docNum untuk kebutuhan dokumen
        $docNumxx =  MSettingDoc::select('*')
            ->where('storeCode', $storeCode)
            ->where('objTypeCode', "$objTypeCode")
            ->get()->toArray();


        $return_docNum = trim($docNumxx[0]['prefix']) . trim($docNumxx[0]['storeCode']) . trim($docNumxx[0]['year']) . str_pad($docNumxx[0]['docNum'], 6, "0", STR_PAD_LEFT);

        if ($cart = MSettingDoc::where('storeCode', $storeCode)
            ->where('objTypeCode', "$objTypeCode")
            ->first()
        ) {
            $cart->increment('docNum', 1);
        }

        return $return_docNum;
    }



    public function get_price_cost_data($storeCode, $itemCode)
    {

        $x['price'] = 0;
        $x['cost'] = 0;

        $data = DB::select(DB::raw('SELECT "price", "cost" FROM "m_itemPrice" 
        WHERE "itemCode" = ' . "'$itemCode'" . '
        AND "priceCode" = ( SELECT "priceCode" FROM "m_store" WHERE "storeCode" = ' . "'$storeCode'" . '
        )
        ORDER BY id ASC LIMIT 1'));


        if (count($data) > 0) {
            return $data[0];
        } else {
            return $x;
        }
    }


    public function get_discount_data($storeCode, $itemCode)
    {

        $x['discount'] = 0;

        $data = DB::select(DB::raw('SELECT "discount" FROM "m_itemDiscount" 
        WHERE "itemCode" = ' . "'$itemCode'" . '
        AND "discCode" = ( SELECT "discCode" FROM "m_store" WHERE "storeCode" = ' . "'$storeCode'" . '
        )
        ORDER BY id ASC LIMIT 1'));


        if (count($data) > 0) {
            return $data[0];
        } else {
            return $x;
        }
    }

    ///////////////////////////////////////////////////////////////////////////

    public function get_price_cost_data_totin($storeCode, $itemCode, $priceCode)
    {

        $x['price'] = 0;
        $x['cost'] = 0;

        $data = DB::select(DB::raw('SELECT "price", "cost" FROM "m_itemPrice" 
        WHERE "itemCode" = ' . "'$itemCode'" . '
        AND "priceCode" = ' . "'$priceCode'" . '
        
        ORDER BY id ASC LIMIT 1'));


        if (count($data) > 0) {
            return $data[0];
        } else {
            return $x;
        }
    }


    public function get_discount_data_totin($storeCode, $itemCode, $discCode)
    {

        $x['discount'] = 0;

        $data = DB::select(DB::raw('SELECT "discount" FROM "m_itemDiscount" 
        WHERE "itemCode" = ' . "'$itemCode'" . '
        AND "discCode" = ' . "'$discCode'" . '
        
        ORDER BY id ASC LIMIT 1'));


        if (count($data) > 0) {
            return $data[0];
        } else {
            return $x;
        }
    }
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    public function generate_docNum_manual_cc()
    {
        //When using query builder
        //if(empty(DB::table('m_setting_doc')->count())){
        // Do something here

        $MStore = MStore::get(); // both get and all  will work here
        //$MObjType = MObjType::get(); // both get and all  will work here
        $MObjType = MObjType::whereIn('objTypeCode', [104, 502])->get();

        $MStore_decode = json_decode($MStore, true);
        $MObjType_decode = json_decode($MObjType, true);


        foreach ($MStore_decode as $key => $value) {
            foreach ($MObjType_decode as $key2 => $value2) {
                $array = array();

                $array['objTypeCode'] = $value2['objTypeCode'];
                $array['storeCode'] = $value['storeCode'];
                $array['docNum'] = $value2['docNum'];
                $array['prefix'] = $value2['prefix'];
                $array['year'] = date('y');


                $array['created_at'] =  date('Y-m-d H:i:s');
                $array['created_by'] =  'automatic';
                $idx = MSettingDoc::create($array)->id;
            }
        }
        //}

    }
    ///////////////////////////////////////////////////////////////////////////
}
