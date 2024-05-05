<?php

namespace App\Repositories;

use App\Models\MasterTypeProgram;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProgramTypeRepository extends Repository implements ProgramTypeInterface
{
    public function getAll(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $programTypesCache = Cache::remember("programTypesCache", $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return MasterTypeProgram::with([
                'programs',
                'programDetails',
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('type', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('id_type', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch type programs",
            resource: $programTypesCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $programTypeCache = Cache::remember("programType", $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return MasterTypeProgram::with([
                'programs',
                'programDetails',
            ])
            ->where('id_type', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch type program {$id}",
            resource: $programTypeCache,
        );
    }

    public function storeOne(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type_name' => ['required', 'string', 'max:255'],
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

            $newProgramType = MasterTypeProgram::create([
                'type' => $request->type_name,
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new type program",
                resource: $newProgramType,
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

    public function updateOne(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type_name' => ['required', 'string', 'max:255'],
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

        $programType = MasterTypeProgram::where('id_type', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $programType->update([
                'type' => $request->type_name,
                'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update recent type program {$id}",
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

    public function removeOne(int $id): JsonResponse
    {
        $programType = MasterTypeProgram::findOrFail($id);

        $programType->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove type program {$id}",
        );
    }
}