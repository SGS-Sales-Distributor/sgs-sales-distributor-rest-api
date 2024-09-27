<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicModel;
use App\Models\StoreType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class StoreTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $judul_halaman_notif;
    public function __construct(){
        $this->judul_halaman_notif = 'Form Store Type';
    }
     
    public function paging(Request $request)
	{
		$URL =  URL::current();

		if (!isset($request->search)) {
			$count = (new StoreType())->count();
			// $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
			$arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);
			$todos =(new StoreType())->get_data_($request->search, $arr_pagination);
		} else {
			$arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);
			// $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset, $request->search);
			$todos =  (new StoreType())->get_data_($request->search, $arr_pagination);
			$count = $todos->count();
		}

		return response()->json(
			// (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
			(new PublicModel())->array_respon_200_table_tr($todos, $count, $arr_pagination),
			200
		);
	}


    public function index()
    {
        try {

            $todos = StoreType::with('StoreType')->orderBy('store_type_id', 'desc')->paginate(10);
            // $todos = program::with('programa')->get();
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
		// 	'data.store_type_name' => 'required',
		// ]);

        $validator = Validator::make($request->all(), [
            'data.store_type_name' => 'required',
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
			$todos = StoreType::create($data);

			//return successful response
			return response()->json([
				'status' => true,
				'message' => $this->judul_halaman_notif . ' created successfully.',
				'data' => $data
			], 201);
		} catch (\Exception $e) {

			return response()->json([
				'status' => false,
				'message' => $e->getMessage(),
			], 403);
		}
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\store_type  $store_type
     * @return \Illuminate\Http\Response
     */
    public function show(int $store_type_id)
	{
		//$todo = $this->MUom->findOrFail($id);
        //die($store_type_id);
		$todo = StoreType::find($store_type_id);
		if ($todo) {
			return response()->json(['data' => $todo], 200);
		} else {
			return response()->json([
				'status' => false,
				'message' => $this->judul_halaman_notif . ' data not found.',
			], 404);
		}
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\store_type  $store_type
     * @return \Illuminate\Http\Response
     */
    public function edit(StoreType $store_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\store_type  $store_type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $store_type_id)
	{
		//validate incoming request 
		//echo "<pre>";echo print_r($request);die();

		//$type_id = $request->typeid;
		// $data = $this->validate($request, [
		// 	'data.store_type_name' => 'required',
		// ]);
        $validator = Validator::make($request->all(), [
            'data.store_type_name' => 'required',
        ]);

        if($validator->fails()) {
			return response()->json(
				$validator->errors(), 422
			);
		}

        $data = $request->data;

		try {
			$todo = StoreType::findOrFail($store_type_id);
			$todo->fill($data);
			$todo->save();

			//store_type::where('store_type_id', $store_type_id)->update(['updated_by' => $store_type_id, 'updated_at' => date('Y-m-d H:i:s')]);

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
     * @param  \App\Models\store_type  $store_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $store_type_id)
	{
		//echo "<pre>";echo print_r($request);die();
		// $store_type_id = $request->typeid;

		try {
			$todo = StoreType::findOrFail($store_type_id);
			// store_type::where('id', $store_type_id)->update(['deleted_by' => $store_type_id]);
			$todo->delete();
			//untuk me-restore softdelete
			// $this->MUom->where('id', $store_type_id)->withTrashed()->restore();
            // store_type::withTrashed()->where('id', $store_type_id)->restore();

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

    public function getTipeToko(Request $request)
	{
		try {
			$data = DB::table('store_type')->select(
			'store_type_id as ID',
			'store_type_name as NAMA TOKO',
			)
			->get();
			
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
}