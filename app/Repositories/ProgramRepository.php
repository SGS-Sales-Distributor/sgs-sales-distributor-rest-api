<?php

namespace App\Repositories;

use App\Models\MasterTypeProgram;
use App\Models\Program;
use App\Models\ProgramDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProgramRepository extends Repository implements ProgramInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $programsCache = Cache::remember(
            "programsCache", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery, $request)
        {
            if ($request->query('q') == "ASC") {
                return Program::with([
                    'masterTypeProgram', 
                    'details'
                ])
                ->orderBy('name_program', 'ASC')
                ->paginate($this::DEFAULT_PAGINATE);
            } 

            if ($request->query('q') == "DESC") {
                return Program::with([
                    'masterTypeProgram', 
                    'details'
                ])
                ->orderBy('name_program', 'DESC')
                ->paginate($this::DEFAULT_PAGINATE);
            }

            if ($request->query('q') == "latest") {
                return Program::with([
                    'masterTypeProgram', 
                    'details'
                ])
                ->orderBy('name_program', 'ASC')
                ->latest()
                ->paginate($this::DEFAULT_PAGINATE);
            }

            return Program::with([
                'masterTypeProgram', 
                'details'
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('name_program', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch programs.", 
            resource: $programsCache,
        );
    }

    public function getDataByDateRangeFilter(Request $request): JsonResponse
    {
        $filterByFromDate = Carbon::parse($request->query('from-date'));

        $filterByEndDate = Carbon::parse($request->query('end-date'));

        $programByPeriodeFilterCache = Cache::remember(
            'programByPeriodeFilter', 
            $this::DEFAULT_CACHE_TTL, 
            function () use (
                $filterByFromDate, 
                $filterByEndDate,
            ) 
        {
            return Program::with(['masterTypeProgram', 'details'])
            ->when($filterByFromDate and $filterByEndDate, function (Builder $query) use ($filterByFromDate, $filterByEndDate) {
                $query->whereBetween('periode_start', [$filterByFromDate, $filterByEndDate]);
                // ->whereBetween('periode_end', [$filterByFromDate, $filterByEndDate]);
            })
            ->orderBy('name_program', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch program based on periode start with filter '{$filterByFromDate}' and '{$filterByEndDate}'.", 
            resource: $programByPeriodeFilterCache,
        );
    }

    public function getOneData(int $id): JsonResponse
    {
        $programCache = Cache::remember(
            "program:{$id}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($id) 
        {
            return Program::with([
                'masterTypeProgram', 
                'details'
            ])
            ->where('id', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch program {$id}.", 
            resource: $programCache,
        );
    }

    public function getProgramTypeData(int $id): JsonResponse
    {
        $programTypeCache = Cache::remember(
            "programs:{$id}:types", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($id) 
        {
            return MasterTypeProgram::whereHas("programs", function (Builder $query) use ($id) {
                $query->where('id', $id);
            })->get();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch program type through program {$id}",
            resource: $programTypeCache,
        );
    }

    public function getProgramDetailsData(int $id): JsonResponse
    {
        $programDetailsCache = Cache::remember(
            "programs:{$id}:details", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($id) 
        {
            return ProgramDetail::whereHas("program", function (Builder $query) use ($id) {
                $query->where('id', $id);
            })
            ->orderBy('id_program', 'asc')
            ->get();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch program details through program {$id}",
            resource: $programDetailsCache,
        );
    }

    public function getOneProgramDetailData(int $id, int $detailId): JsonResponse
    {
        $programDetailCache = Cache::remember("programs:{$id}:details:{$detailId}", $this::DEFAULT_CACHE_TTL, function () use ($id, $detailId) {
            return ProgramDetail::whereHas("program", function (Builder $query) use ($id, $detailId) {
                $query->where('id', $id);
            })->where('id', $detailId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch program detail {$detailId} through program {$id}",
            resource: $programDetailCache,
        );
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_type_program' => ['required', 'integer'],
            'nama_program' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'periode_mulai' => ['required', 'date'],
            'periode_akhir' => ['required', 'date', 'different:periode_mulai'],
            'condition' => ['nullable', 'string', 'max:255'],
            'get' => ['nullable', 'string', 'max:255'],
            'code_product' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'max_digits:10'],
            'discount' => ['nullable', 'integer'],
            'created_by' => ['nullable', 'string', 'max:255'],
            'updated_by' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        try {
            DB::beginTransaction();

            $program = Program::create([
                'id_type_program' => $request->id_type_program,
                'name_program' => $request->nama_program,
                'keterangan' => $request->keterangan,
                'active' => 1,
                'periode_start' => Carbon::make($request->periode_mulai)->format('Y-m-d'),
                'periode_end' => Carbon::make($request->periode_akhir)->format('Y-m-d'),
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by,
            ]);

            ProgramDetail::create([
                'id_program' => $program->id,
                'condition' => $request->condition,
                'get' => $request->get,
                'product' => $request->code_product,
                'qty' => $request->quantity,
                'disc_val' => $request->discount / 100,
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new program",
                resource: $program,
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function updateOneData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'periode_mulai' => ['required', 'date'],
            'periode_akhir' => ['required', 'date', 'different:periode_mulai'],
            'condition' => ['nullable', 'string', 'max:255'],
            'get' => ['nullable', 'string', 'max:255'],
            'code_product' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'max_digits:10'],
            'discount' => ['nullable', 'decimal:10,2'],
            'created_by' => ['nullable', 'string', 'max:255'],
            'updated_by' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        // search program by id.
        $recentProgram = Program::where('id', $id)->firstOrFail();

        $recentProgramDetail = ProgramDetail::where('id_program', $id)->firstOrFail();

        // get value from type input.
        $typeProgram = $request->input('type');

        // get value from status input.
        $programStatus = $request->input('status');

        try {
            DB::beginTransaction();

            $recentProgram->update([
                'id_type_program' => $typeProgram,
                'name_program' => $request->nama_program,
                'keterangan' => $request->keterangan,
                'active' => $programStatus,
                'periode_start' => $request->periode_mulai,
                'periode_end' => $request->periode_akhir,
                'updated_by' => $request->updated_by,
                'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
            ]);

            $recentProgramDetail->update([
                'condition' => $request->condition,
                'get' => $request->get,
                'product' => $request->code_product,
                'qty' => $request->quantity,
                'disc_val' => $request->discount,
                'updated_by' => $request->updated_by,
                'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update program {$id}",
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function removeOneData(int $id): JsonResponse
    {
        $recentProgram = Program::findOrFail($id);

        $recentProgramDetail = ProgramDetail::findOrFail('id_program', $id);

        $recentProgramDetail->delete();
        
        $recentProgram->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove program {$id}",
        );
    }
}