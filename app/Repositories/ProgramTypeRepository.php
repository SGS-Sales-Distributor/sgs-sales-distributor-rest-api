<?php

namespace App\Repositories;

use App\Models\MasterTypeProgram;
use App\Models\PublicModel;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;


class ProgramTypeRepository extends Repository implements ProgramTypeInterface
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Form Master Program';
    }

    public function getAllData(Request $request): JsonResponse
    {
        // $searchByQuery = $request->query('q');

        // $programTypesCache = Cache::remember("programTypesCache", $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
        //     return MasterTypeProgram::with([
        //         'programs',
        //         'programDetails',
        //     ])
        //     ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
        //         $query->where('type', 'LIKE', '%' . $searchByQuery . '%');
        //     })
        //     ->orderBy('id_type', 'asc')
        //     ->paginate($this::DEFAULT_PAGINATE);
        // });

        // return $this->successResponse(
        //     statusCode: 200,
        //     success: true,
        //     msg: "Successfully fetch type programs",
        //     resource: $programTypesCache,
        // );
        $URL =  URL::current();

        if (!isset($request->search)) {
            $count = (new MasterTypeProgram())->count();
            $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);
            $todos = (new MasterTypeProgram())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->paginateDataWithSearchQuery($URL, $request->limit, $request->offset, $request->search);
            $todos =  (new MasterTypeProgram())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->successResponse($todos, $count, $arr_pagination),
            200
        );
    }

    public function getOneData(int $id): JsonResponse
    {
        // $programTypeCache = Cache::remember("programType", $this::DEFAULT_CACHE_TTL, function () use ($id) {
        //     return MasterTypeProgram::with([
        //         'programs',
        //         'programDetails',
        //     ])
        //     ->where('id_type', $id)
        //     ->firstOrFail();
        // });

        // return $this->successResponse(
        //     statusCode: 200,
        //     success: true,
        //     msg: "Successfully fetch type program {$id}",
        //     resource: $programTypeCache,
        // );
        $typeProgram = MasterTypeProgram::where();

        return response()->json([
            'data' => $typeProgram,
        ], 200);
    }

    public function storeOneData(Request $request): JsonResponse
    {
        // $validator = Validator::make($request->all(), [
        //     'type_name' => ['required', 'string', 'max:255'],
        //     'created_by' => ['nullable', 'string', 'max:255'],
        //     'updated_by' => ['nullable', 'string', 'max:255'],
        // ]);

        // if ($validator->fails()) {
        //     return $this->clientErrorResponse(
        //         statusCode: 422,
        //         success: false,
        //         msg: $validator->errors()->first(),
        //         resource: $validator->errors()->all(),
        //     );
        // }

        // try {
        //     DB::beginTransaction();

        //     $newProgramType = MasterTypeProgram::create([
        //         'type' => $request->type_name,
        //         'created_by' => $request->created_by,
        //         'updated_by' => $request->updated_by,
        //     ]);

        //     DB::commit();

        //     return $this->successResponse(
        //         statusCode: 201,
        //         success: true,
        //         msg: "Successfully create new type program",
        //         resource: $newProgramType,
        //     );
        // } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        //     DB::rollBack();

        //     return $this->errorResponse(
        //         statusCode: $e->getStatusCode(),
        //         success: false,
        //         msg: $e->getMessage(),
        //     );
        // } catch (\Error $e) {
        //     DB::rollBack();

        //     return $this->errorResponse(
        //         statusCode: 500,
        //         success: false,
        //         msg: $e->getMessage(),
        //     );
        // } catch (\Exception $e) {
        //     DB::rollBack();

        //     return $this->errorResponse(
        //         statusCode: 500,
        //         success: false,
        //         msg: $e->getMessage(),
        //     );
        // }
        //echo "<pre>";echo print_r($request);die();
        //$type = $request->type;

        $validator = Validator::make($request->all(), [
            'data.type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $data = $request->data;

        try {
            //$data['data']['created_by'] = $type_id;
            //create table insert dan return id tabel
            $todos = MasterTypeProgram::create($data);

            //return successful response
            return response()->json([
                'status' => true,
                'message' => $this->judul_halaman_notif . ' created successfully.',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function updateOneData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data.type' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $todo = MasterTypeProgram::where('id_type', $id)->firstOrFail();

        $data = $request->data;

        try {
            $array = [
                'type' => $data['type'],
            ];

            DB::beginTransaction();

            $todo->update($array);

            DB::commit();

            //return successful response
            return response()->json([
                'status' => true,
                'message' => $this->judul_halaman_notif . ' updated successfully.',
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json([
                'status' => false,
                'message' => $this->judul_halaman_notif . ' failed update.',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function removeOneData(int $id): JsonResponse
    {

        try {
            $todo = MasterTypeProgram::where('id_type', $id)->firstOrFail();

            $todo->delete();

            return response()->json([
                'status' => true,
                'message' => $this->judul_halaman_notif . ' deleted successfully.',
                'user' => $todo
            ], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json([
                'status' => false,
                'message' => $this->judul_halaman_notif . ' failed delete.',
            ], 409);
        }
    }
}
