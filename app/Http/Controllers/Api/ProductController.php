<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductInfoDo;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'Form Master Product';
    }

    public function getMasterProduk(Request $request)
    {
        try {
            $data = DB::table('product_info_do')->select(
                'prod_number as MTG CODE',
                'prod_nAME as PRODUCT NAME',
                'prod_base_price as PRODUCT BASE PRICE',
                'prod_special_offer as DISKON REGULAR',
                'brand_id as BRAND ID',
            )->get();

            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function paging(Request $request)
    {
        $URL =  URL::current();

        if (!isset($request->search)) {
            $count = (new ProductInfoDo())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new ProductInfoDo())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset, $request->search);
            $todos =  (new ProductInfoDo())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function index()
    {
        try {
            $todos = ProductInfoDo::with('productInfoLmt')->orderBy('prod_number', 'asc')->paginate(10);
            return response()->json([
                'code' => 200,
                'status' => true,
                'total' => $todos->total(),
                'last_page' => $todos->lastPage(),
                'data' => $todos->items(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'failed get data',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //echo "<pre>";echo print_r($request);die();
        //$type = $request->type;
        // $data = $this->validate($request, [
        //     'data.prod_base_price' => 'required',
        //     'data.prod_unit_price' => 'required',
        //     'data.prod_special_offer' => 'required',
        // ]);

        $validator = Validator::make($request->all(), [
            'data.prod_base_price' => 'required',
            'data.prod_unit_price' => 'required',
            'data.prod_special_offer' => 'required',
        ]);

        if($validator->fails()) {
			return response()->json(
				$validator->errors(), 422
			);
		}

        $data = $request->data;

        try {

            //$data['data']['created_by'] = $type_id;

            //create table insert dan return id tabel
            $todos = ProductInfoDo::create($data);

            //return successful response
            return response()->json([
                'status' => true,
                'message' => $this->judul_halaman_notif . ' created successfully.',
                //'data' => $data['data']
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $data,
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $prod_number
     * @return \Illuminate\Http\Response
     */
    public function show(string $prod_number)
    {
        //$todo = $this->MUom->findOrFail($id);
        //die($id_type);
        //$todo = ProductInfoDo::find($prod_number);
        $todo = ProductInfoDo::where('prod_number', '=', $prod_number)->get();
        if ($todo) {
            return response()->json(['data' => $todo], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => $this->judul_halaman_notif . 'data not found.',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $prod_number
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $prod_number)
    {
        //validate incoming request 
        //echo "<pre>";echo print_r($request);die();

        //$type_id = $request->typeid;
        $validator = Validator::make($request->all(), [
            'data.prod_base_price' => 'required',
            'data.prod_unit_price' => 'required',
            'data.prod_special_offer' => 'required',
        ]);

        if($validator->fails()) {
			return response()->json(
				$validator->errors(), 422
			);
		}

        $data = $request->data;

        try {
            $array = [
                'prod_base_price' => $data['prod_base_price'],
                'prod_unit_price' => $data['prod_unit_price'],
                'prod_special_offer' => $data['prod_special_offer']
            ];

            $todo = ProductInfoDo::where('prod_number', '=', $prod_number)
                ->update($array);

            //$todo = ProductInfoDo::findOrFail($prod_number);
            //$todo->fill($data['data']);
            //$todo->save();



            // ProductInfoDo::where('prod_number', $prod_number)->update(['updated_by' => $prod_number, 'updated_at' => date('Y-m-d H:i:s')]);

            //return successful response
            return response()->json([
                'status' => true,
                'message' => $this->judul_halaman_notif . ' updated successfully.',
                'data' => $todo
            ], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json([
                'status' => false,
                'message' => $this->judul_halaman_notif . ' failed update.',
            ], 409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $prod_number
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $prod_number)
    {
        //echo "<pre>";echo print_r($request);die();
        // $id_type = $request->typeid;

        try {
            $todo = ProductInfoDo::findOrFail($prod_number);
            // master_type_program::where('id', $id_type)->update(['deleted_by' => $id_type]);
            $todo->delete();
            //untuk me-restore softdelete
            // $this->MUom->where('id', $id_type)->withTrashed()->restore();
            // master_type_program::withTrashed()->where('id', $id_type)->restore();

            //return successful response
            return response()->json([
                'status' => true,
                'message' => $this->judul_halaman_notif . ' deleted successfully.',
                'user' => $todo
            ], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json([
                'status' => false,
                'message' => $this->judul_halaman_notif . ' failed delete.',
            ], 409);
        }
    }

    public function getAll(Request $request): JsonResponse
    {
        return $this->productInterface->getAllData($request);
    }

    public function getOne(string $productNumber): JsonResponse
    {
        return $this->productInterface->getOneData($productNumber);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->productInterface->storeOneData($request);
    }

    public function updateOne(Request $request, string $productNumber): JsonResponse
    {
        return $this->productInterface->updateOneData($request, $productNumber);
    }

    public function removeOne(string $productNumber): JsonResponse
    {
        return $this->productInterface->removeOneData($productNumber);
    }
}
