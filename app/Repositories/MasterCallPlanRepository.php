<?php

namespace App\Repositories;

use App\Models\MasterCallPlan;
use App\Models\MasterCallPlanDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use App\Models\PublicModel;
use App\Models\ProfilVisit;

class MasterCallPlanRepository extends Repository implements MasterCallPlanInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query(key: 'search');

        $masterCallPlanCache = Cache::remember(
            'masterCallPlan',
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery) {
                return MasterCallPlan::with([
                    'user.type',
                    'user.status',
                    'details.store',
                ])
                    ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                        $query->whereHas('user', function (Builder $subQuery) use ($searchByQuery) {
                            $subQuery->where('fullname', 'LIKE', '%' . $searchByQuery . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchByQuery . '%');
                        });
                    })
                    ->orderBy('id', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch master call plan.",
            resource: $masterCallPlanCache,
        );
    }

    public function getAllDataByDateFilter(Request $request): JsonResponse
    {
        $searchByDateQuery = $request->query('q');

        $filterByDateRange = $this->dateRangeFilter->parseDateRange($searchByDateQuery);
        $filterByDate = $this->dateRangeFilter->parseDate($searchByDateQuery);
        $filterByYearRange = $this->dateRangeFilter->parseYearRange($searchByDateQuery);
        $filterByYear = $this->dateRangeFilter->parseYear($searchByDateQuery);

        $masterCallPlanByDateFilterCache = Cache::remember(
            'masterCallPlanByDateFilter_' . $searchByDateQuery,
            $this::DEFAULT_CACHE_TTL,
            function () use ($filterByDateRange, $filterByDate, $filterByYearRange, $filterByYear) {

                $query = MasterCallPlan::with(['user', 'details']);

                if ($filterByDateRange) {
                    $query->whereHas('details', function ($q) use ($filterByDateRange) {
                        $q->whereBetween('date', $filterByDateRange);
                    });
                } elseif ($filterByDate) {
                    $query->whereHas('details', function ($q) use ($filterByDate) {
                        $q->whereDate('date', $filterByDate);
                    });
                } elseif ($filterByYearRange) {
                    $query->whereHas('details', function ($q) use ($filterByYearRange) {
                        $q->whereBetween('date', $filterByYearRange);
                    });
                } elseif ($filterByYear) {
                    $query->whereHas('details', function ($q) use ($filterByYear) {
                        $q->whereYear('date', $filterByYear);
                    });
                }

                return $query
                    ->orderBy('id', 'asc')
                    ->paginate(10);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch master call plan with date filter {$searchByDateQuery}",
            resource: $masterCallPlanByDateFilterCache,
        );
    }

    public function getOneData(int $id): JsonResponse
    {
        $masterCallPlanCache = Cache::remember(
            "masterCallPlan:{$id}",
            $this::DEFAULT_CACHE_TTL,
            function () use ($id) {
                return MasterCallPlan::with(['user', 'details'])
                    ->where('id', $id)
                    ->firstOrFail();
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch master call plan {$id}.",
            resource: $masterCallPlanCache,
        );
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                // 'call_plan_id' => ['required', 'integer'],
                'month_plan' => ['required', 'integer'],
                'year_plan' => ['required', 'integer'],
                'user_id' => ['required', 'integer'],
                // 'store_id' => ['required', 'integer'],
                // 'date' => ['required', 'date'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $IdHeader = MasterCallPlan::where('user_id', '=', $request->user_id)
                ->where('month_plan', '=', $request->month_plan)
                ->where('year_plan', '=', $request->year_plan)
                ->first();


            DB::commit();

            if (!empty($IdHeader)) {
                $setLastId = $IdHeader->id;
            } else {
                $masterCallPlan = MasterCallPlan::create([
                    'month_plan' => $request->month_plan,
                    'year_plan' => $request->year_plan,
                    'user_id' => $request->user_id,
                    'created_by' => $request->created_by,
                ]);
                $setLastId = $masterCallPlan->id;
            }

            DB::beginTransaction();


            foreach ($request->daily_plan as $key => $value) {
                $detailPlanStore = MasterCallPlanDetail::where('store_id', '=', $value['toko'])
                    ->where('date', '=', $value['tanggal'])
                    ->where('call_plan_id', '=', $setLastId)
                    ->first();

                $data[] = [
                    'call_plan_id' => $setLastId,
                    'store_id' => $value['toko'],
                    'date' => $value['tanggal'],
                    'created_by' => $request->created_by,
                ];
            }

            if (!empty($detailPlanStore)) {
                return $this->clientErrorResponse(
                    statusCode: 422,
                    success: false,
                    msg: "Plan Visit Toko Ini Sudah Dibuat Sebelumnya.",
                );
            } else {
                MasterCallPlanDetail::insert($data);
            }

            DB::commit();
            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new master call plan data",
                resource: $data
            );

            // DB::beginTransaction();
            // MasterCallPlanDetail::create([
            //     'call_plan_id' => $masterCallPlan->id,
            //     'store_id' => $request->toko,
            //     'date' => $request->date,
            // ]);
            // DB::commit();


            // return $this->successResponse(
            //     statusCode: 201,
            //     success: true,
            //     msg: "Successfully create new master call plan data",
            //     resource: $masterCallPlan
            // );
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
        $validator = Validator::make(
            $request->all(),
            [
                'call_plan_id' => ['nullable', 'integer'],
                'month_plan' => ['nullable', 'integer'],
                'year_plan' => ['nullable', 'integer'],
                'user_id' => ['required', 'integer'],
                'store_id' => ['required', 'integer'],
                'date' => ['required', 'date'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $masterCallPlan = MasterCallPlan::where('id', $id)->firstOrFail();

        $masterCallPlanDetail = MasterCallPlanDetail::where('call_plan_id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $masterCallPlan->update([
                'month_plan' => $request->month_plan,
                'year_plan' => $request->year_plan,
                'user_id' => $request->user_id,
            ]);

            $masterCallPlanDetail->update([
                'call_plan_id' => $request->call_plan_id,
                'store_id' => $request->store_id,
                'date' => $request->date,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully update master call plan {$id}",
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
        $masterCallPlanDetail = MasterCallPlanDetail::where('call_plan_id', $id)
            ->firstOrFail();

        $masterCallPlan = MasterCallPlan::where('id', $id)
            ->firstOrFail();

        $masterCallPlanDetail->delete();

        $masterCallPlan->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove master call plan {$id} and it's detail.",
        );
    }

    public function notVisitedUsers(Request $request, int $userId): JsonResponse
    {
        // DB::enableQueryLog();
        $plansUsr = DB::table('master_call_plan_detail')
            ->select([
                'master_call_plan.user_id as user',
                'store_info_distri.store_name as nama_toko',
                'store_info_distri.store_address as alamat_toko',
                'master_call_plan_detail.id as idPlanDetail',
                'master_call_plan_detail.call_plan_id as idPlan',
                'master_call_plan_detail.store_id as idToko',
                'master_call_plan_detail.date as tanggal plan',
                'profil_visit.id as realisasi_visit',
                'profil_notvisit.id as idKetVisit',
            ])
            ->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
            ->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
            ->leftJoin('profil_visit', function ($leftJoin) {
                $leftJoin->on('profil_visit.user', '=', 'master_call_plan.user_id')
                    ->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
                    ->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
            })
            ->leftJoin('profil_notvisit', function ($leftJoin2) {
                $leftJoin2->on('profil_notvisit.id_master_call_plan_detail', '=', 'master_call_plan_detail.id');
            })
            ->where('master_call_plan.user_id', DB::raw("'" . $userId . "'"))
            // ->where('master_call_plan_detail.date', Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
            ->where('master_call_plan_detail.date', "$request->tomorrow")
            ->whereRaw('profil_visit.id is null')
            ->orderBy('master_call_plan_detail.date', 'desc')
            ->get();

        // $log = DB::getQueryLog();
        // dd($log);

        if (!$plansUsr) {
            return $this->clientErrorResponse(
                statusCode: 404,
                success: false,
                msg: "Unsuccessful Plan data UserId : {$userId} not found.",
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch Plan User : {$userId} data.",
            resource: $plansUsr,
        );
    }

    public function getCoverage_plan(Request $request): JsonResponse
    {
        $URL           = URL::current();
        $searchByQuery = trim((string) $request->query('search', ''));
        $tanggalfr     = $request->query('tanggalfr');
        $tanggalto     = $request->query('tanggalto');
        $customerCode  = $request->query('customer_code');
        $userId        = trim((string) $request->query('user_id', ''));

        $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery(
            $URL,
            $request->limit,
            $request->offset,
            $searchByQuery,
            $tanggalfr,
            $tanggalto
        );

        $query = DB::table('master_call_plan as mcp')
            ->join('master_call_plan_detail as mcpd', 'mcpd.call_plan_id', '=', 'mcp.id')
            ->join('user_info as ui', 'ui.user_id', '=', 'mcp.user_id')
            ->leftJoin('mst_customer as mc', 'mc.customer_code', '=', 'ui.customer_code')
            ->leftJoin('store_info_distri as sid', 'sid.store_id', '=', 'mcpd.store_id')
            ->leftJoin('store_cabang as sc', 'sc.id', '=', 'sid.subcabang_id')
            ->selectRaw("
                mcp.user_id,
                mcpd.date AS tanggal,
                COALESCE(MAX(sc.kode_cabang), '-') as kode_cabang,
                ui.fullname,
                mc.customer_name,
                COUNT(DISTINCT sid.store_id) as jml_coverage,
                COUNT(mcpd.id) as plan_day_in,
                (
                    SELECT COUNT(pv.id)
                    FROM profil_visit pv
                    WHERE pv.\"user\" = mcp.user_id
                    AND pv.tanggal_visit = mcpd.date
                ) as day_in_terpenuhi,
                COUNT(mcpd.id) - (
                    SELECT COUNT(pv.id)
                    FROM profil_visit pv
                    WHERE pv.\"user\" = mcp.user_id
                    AND pv.tanggal_visit = mcpd.date
                ) AS day_in_tidak_terpenuhi
            ")
            ->when(!empty($tanggalfr) && !empty($tanggalto), function ($q) use ($tanggalfr, $tanggalto) {
                $q->whereBetween('mcpd.date', [$tanggalfr, $tanggalto]);
            })
            ->when(!empty($customerCode), function ($q) use ($customerCode) {
                $q->where('ui.customer_code', $customerCode);
            })
            ->when($userId !== '', function ($q) use ($userId) {
                $q->where('mcp.user_id', $userId);
            })
            ->when($searchByQuery !== '', function ($q) use ($searchByQuery) {
                $q->where(function ($sub) use ($searchByQuery) {
                    $sub->where('ui.fullname', 'ILIKE', '%' . $searchByQuery . '%')
                        ->orWhere('sc.kode_cabang', 'ILIKE', '%' . $searchByQuery . '%')
                        ->orWhere('mc.customer_name', 'ILIKE', '%' . $searchByQuery . '%');
                });
            })
            ->groupBy(
                'mcp.user_id',
                'mcpd.date',
                'ui.fullname',
                'mc.customer_name'
            )
            ->orderBy('mcpd.date', 'asc');

        $count = DB::query()->fromSub($query, 'coverage_rows')->count();

        $dataA = (clone $query)
            ->limit($arr_pagination['limit'])
            ->offset($arr_pagination['offset'])
            ->get();

        if ($count == 0) {
            return response()->json(
                (new PublicModel())->array_respon_200_table_tr([], 0, $arr_pagination),
                200
            );
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table_tr($dataA, $count, $arr_pagination),
            200
        );
    }

    public function getCoverage_planWeeklySummary(Request $request): JsonResponse
    {
        $tanggalfr     = $request->query('tanggalfr');
        $tanggalto     = $request->query('tanggalto');
        $searchByQuery = $request->query('search');
        $customerCode  = $request->query('customer_code');
        $userId        = $request->query('user_id');

        if (empty($tanggalfr) || empty($tanggalto)) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: 'Tanggal awal dan tanggal akhir wajib diisi.',
            );
        }

        $detailRows = DB::table('master_call_plan_detail as mcpd')
            ->join('master_call_plan as mcp', 'mcp.id', '=', 'mcpd.call_plan_id')
            ->join('user_info as ui', 'ui.user_id', '=', 'mcp.user_id')
            ->join('store_info_distri as sid', 'sid.store_id', '=', 'mcpd.store_id')
            ->leftJoin('mst_customer as mc', 'mc.customer_code', '=', 'ui.customer_code')
            ->leftJoin('profil_visit as pv', function ($join) {
                $join->on('pv.user', '=', 'mcp.user_id')
                    ->on('pv.tanggal_visit', '=', 'mcpd.date')
                    ->on('pv.store_id', '=', 'mcpd.store_id');
            })
            ->whereBetween('mcpd.date', [$tanggalfr, $tanggalto])
            ->when(!empty($customerCode), function ($q) use ($customerCode) {
                $q->where('ui.customer_code', $customerCode);
            })
            ->when(!empty($userId), function ($q) use ($userId) {
                $q->where('mcp.user_id', $userId);
            })
            ->when($searchByQuery, function ($query) use ($searchByQuery) {
                $query->where(function ($subQuery) use ($searchByQuery) {
                    $subQuery->where('ui.fullname', 'LIKE', '%' . $searchByQuery . '%')
                        ->orWhere('sid.store_code', 'LIKE', '%' . $searchByQuery . '%')
                        ->orWhere('sid.store_name', 'LIKE', '%' . $searchByQuery . '%');
                });
            })
            ->selectRaw("
            mcp.user_id as user_id,
            ui.fullname as nama_sales,
            ui.customer_code as customer_code,
            mc.customer_name as customer_name,
            sid.store_code as kode_toko,
            sid.store_name as nama_toko,
            mcpd.date as tanggal_plan,
            CASE
                WHEN EXTRACT(DAY FROM mcpd.date) BETWEEN 1 AND 7 THEN 1
                WHEN EXTRACT(DAY FROM mcpd.date) BETWEEN 8 AND 14 THEN 2
                WHEN EXTRACT(DAY FROM mcpd.date) BETWEEN 15 AND 21 THEN 3
                ELSE 4
            END as week_num,
            pv.ket as ket_visit,
            pv.keterangan_out as ket_out_visit
        ")
            ->orderBy('ui.fullname', 'asc')
            ->orderBy('sid.store_code', 'asc')
            ->orderBy('mcpd.date', 'asc')
            ->get();

        $groupedStores = $detailRows->groupBy(function ($row) {
            return $row->user_id . '|' . $row->kode_toko . '|' . $row->nama_toko;
        });

        $formatted = collect();

        foreach ($groupedStores as $storeRows) {
            $firstRow   = $storeRows->first();
            $weekGroups = $storeRows->groupBy('week_num');
            $weekKet    = [];

            for ($week = 1; $week <= 4; $week++) {
                $weekRows  = $weekGroups->get($week, collect());
                $weekCount = $weekRows->count();

                $weekKetValues = $weekRows->map(function ($row) {
                    $parts  = [];
                    $ketIn  = trim((string) ($row->ket_visit ?? ''));
                    $ketOut = trim((string) ($row->ket_out_visit ?? ''));

                    if ($ketIn  !== '') $parts[] = "In: {$ketIn}";
                    if ($ketOut !== '') $parts[] = "Out: {$ketOut}";

                    return implode(', ', $parts);
                })
                    ->filter(fn($v) => $v !== '')
                    ->unique()
                    ->values();

                $weekKet["week_{$week}"]       = $weekKetValues->isNotEmpty()
                    ? $weekKetValues->implode(' | ')
                    : '-';
                $weekKet["week_{$week}_count"] = $weekCount;
            }

            $formatted->push([
                'nama_sales'    => $firstRow->nama_sales,
                'customer_name' => $firstRow->customer_name ?? '-', 
                'kode_toko'     => $firstRow->kode_toko,
                'nama_toko'     => $firstRow->nama_toko,
                'week_1'        => $weekKet['week_1_count'],
                'week_2'        => $weekKet['week_2_count'],
                'week_3'        => $weekKet['week_3_count'],
                'week_4'        => $weekKet['week_4_count'],
                'ket' => implode("\n", array_filter([
                    $weekKet['week_1_count'] > 0 ? "W1: {$weekKet['week_1']}" : null,
                    $weekKet['week_2_count'] > 0 ? "W2: {$weekKet['week_2']}" : null,
                    $weekKet['week_3_count'] > 0 ? "W3: {$weekKet['week_3']}" : null,
                    $weekKet['week_4_count'] > 0 ? "W4: {$weekKet['week_4']}" : null,
                ])),
                'total' => $storeRows->count(),
            ]);
        }

        $grandTotal = $detailRows->count();
        $weekTotals = [
            'week_1' => $detailRows->where('week_num', 1)->count(),
            'week_2' => $detailRows->where('week_num', 2)->count(),
            'week_3' => $detailRows->where('week_num', 3)->count(),
            'week_4' => $detailRows->where('week_num', 4)->count(),
        ];

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: 'Successfully fetch weekly summary visit report.',
            resource: [
                'rows'          => $formatted->values(),
                'grand_total'   => $grandTotal,
                'week_totals'   => $weekTotals,
                'customer_name' => $detailRows->first()?->customer_name ?? '-',
            ],
        );
    }

    public function getCallPlanJoin(Request $request): JsonResponse
    {
        try {
            $query = DB::table('master_call_plan as mcp')
                ->join('master_call_plan_detail as mcpd', 'mcp.id', '=', 'mcpd.call_plan_id')
                ->join('user_info as u', 'mcp.user_id', '=', 'u.user_id')
                ->join('store_info_distri as sid', 'mcpd.store_id', '=', 'sid.store_id')
                ->join('store_info_distri_person as sidp', 'sid.store_id', '=', 'sidp.store_id')
                ->leftJoin('mst_customer as mc', 'mc.customer_code', '=', 'u.customer_code')
                ->select(
                    'u.fullname',
                    'u.email',
                    'u.customer_code',
                    'mc.customer_name',
                    'mcp.user_id',
                    'mcp.month_plan',
                    'mcp.year_plan',
                    'mcpd.date',
                    'mcpd.call_plan_id',
                    'mcpd.store_id',
                    'sid.store_name',
                    'sid.store_code',
                );

            if ($request->month) {
                $query->where('mcp.month_plan', $request->month);
            }
            if ($request->year) {
                $query->where('mcp.year_plan', $request->year);
            }
            // ← TAMBAH filter customer
            if ($request->customer_code) {
                $query->where('u.customer_code', $request->customer_code);
            }
            // ← TAMBAH filter user_id (opsional)
            if ($request->user_id) {
                $query->where('mcp.user_id', $request->user_id);
            }

            $data = $query->orderBy('mcp.id', 'asc')->get();

            return response()->json([
                'status'  => 200,
                'success' => true,
                'message' => 'Success get call plan with user',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'message' => 'Error get data',
                'error'   => $e->getMessage()
            ]);
        }
    }

    public function importCallPlan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();

            // Baca file CSV/Excel sederhana
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data); // buang baris header

            // Header template: user_id | store_id | date (YYYY-MM-DD)
            $inserted = 0;
            $errors   = [];

            DB::beginTransaction();

            foreach ($data as $lineNum => $row) {
                if (count($row) < 3) continue;

                $userId  = trim($row[0]);
                $storeId = trim($row[1]);
                $date    = trim($row[2]);

                // Validasi format tanggal
                if (!Carbon::hasFormat($date, 'Y-m-d')) {
                    $errors[] = "Baris " . ($lineNum + 2) . ": Format tanggal salah ($date), harus YYYY-MM-DD";
                    continue;
                }

                // Ambil atau buat header call plan
                $callPlan = MasterCallPlan::firstOrCreate(
                    [
                        'user_id'    => $userId,
                        'month_plan' => Carbon::parse($date)->month,
                        'year_plan'  => Carbon::parse($date)->year,
                    ],
                    ['created_by' => $request->created_by ?? 1]
                );

                // Cek duplikat detail
                $exists = MasterCallPlanDetail::where('call_plan_id', $callPlan->id)
                    ->where('store_id', $storeId)
                    ->where('date', $date)
                    ->exists();

                if ($exists) {
                    $errors[] = "Baris " . ($lineNum + 2) . ": Duplikat (user=$userId, store=$storeId, date=$date)";
                    continue;
                }

                MasterCallPlanDetail::create([
                    'call_plan_id' => $callPlan->id,
                    'store_id'     => $storeId,
                    'date'         => $date,
                    'created_by'   => $request->created_by ?? 1,
                ]);

                $inserted++;
            }

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Import selesai. $inserted data berhasil dimasukkan." . (count($errors) ? " " . count($errors) . " baris dilewati." : ""),
                resource: ['inserted' => $inserted, 'errors' => $errors],
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
        }
    }
}
