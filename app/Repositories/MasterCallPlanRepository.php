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
            'masterCallPlanByDateFilter_' . $searchByDateQuery, // ✅ FIX CACHE
            $this::DEFAULT_CACHE_TTL,
            function () use ($filterByDateRange, $filterByDate, $filterByYearRange, $filterByYear) {

                $query = MasterCallPlan::with(['user', 'details']);

                // ✅ DATE RANGE
                if ($filterByDateRange) {
                    $query->whereHas('details', function ($q) use ($filterByDateRange) {
                        $q->whereBetween('date', $filterByDateRange);
                    });
                }

                // ✅ SINGLE DATE
                elseif ($filterByDate) {
                    $query->whereHas('details', function ($q) use ($filterByDate) {
                        $q->whereDate('date', $filterByDate);
                    });
                }

                // ✅ YEAR RANGE
                elseif ($filterByYearRange) {
                    $query->whereHas('details', function ($q) use ($filterByYearRange) {
                        $q->whereBetween('date', $filterByYearRange);
                    });
                }

                // ✅ SINGLE YEAR
                elseif ($filterByYear) {
                    $query->whereHas('details', function ($q) use ($filterByYear) {
                        $q->whereYear('date', $filterByYear);
                    });
                }

                // 🔥 DEFAULT (WAJIB ADA)
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
            // master call header
            DB::beginTransaction();

            // mengambil id header user, bulan, & tahun
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

            // master call plan detail
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
        $URL = URL::current();

        $searchByQuery = $request->query(key: 'search');
        $tanggalfr = $request->query(key: 'tanggalfr');
        $tanggalto = $request->query(key: 'tanggalto');

        if (!isset($searchByQuery) && !isset($tanggalfr) && !isset($tanggalto)) {
            $count = (new ProfilVisit())->count();
            $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery(
                $URL,
                $request->limit,
                $request->offset,
                $searchByQuery,
                $tanggalfr,
                $tanggalto
            );
            // DB::enableQueryLog();
            $data = DB::table('master_call_plan')
                ->selectRaw('master_call_plan.user_id,master_call_plan_detail.date AS tanggal,count(master_call_plan_detail.id) as plan_day_in,(select count(id) FROM profil_visit pv where pv."user" ="user_id" and pv.tanggal_visit =master_call_plan_detail.date) as day_in_terpenuhi,
            (count(master_call_plan_detail.id))-(select count(id) FROM profil_visit pv where pv."user" ="user_id" and pv.tanggal_visit =master_call_plan_detail.date) AS day_in_tidak_terpenuhi')
                ->join('master_call_plan_detail', 'master_call_plan_detail.call_plan_id', '=', 'master_call_plan.id')
                // ->where('master_call_plan_detail.date', '=', $chooseTgl)
                ->groupBy('master_call_plan.user_id')
                ->groupBy('master_call_plan_detail.date');

            $dataA = DB::query()
                ->selectRaw('store_cabang.kode_cabang,user_info.fullname,count(store_info_distri.store_id) as jml_coverage,a.user_id,a.tanggal,a.plan_day_in,a.day_in_terpenuhi,a.day_in_tidak_terpenuhi')
                ->fromSub($data, 'a')
                ->join('user_info', 'user_info.user_id', '=', 'a.user_id')
                ->join('store_cabang', 'store_cabang.id', 'user_info.cabang_id')
                ->join('store_info_distri', 'store_info_distri.subcabang_id', '=', 'store_cabang.id')
                ->groupBy('a.user_id')
                ->groupBy('user_info.fullname')
                ->groupBy('a.tanggal')
                ->groupBy('a.plan_day_in')
                ->groupBy('a.day_in_terpenuhi')
                ->groupBy('store_cabang.kode_cabang')
                ->groupBy('a.day_in_tidak_terpenuhi')
                ->orderBy('a.tanggal', 'asc')
                ->limit($arr_pagination['limit'])
                ->offset($arr_pagination['offset'])
                ->get();
        } else {
            $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery(
                $URL,
                $request->limit,
                $request->offset,
                $searchByQuery,
                $tanggalfr,
                $tanggalto
            );
            $data = DB::table('master_call_plan')
                ->selectRaw('master_call_plan.user_id,master_call_plan_detail.date AS tanggal,count(master_call_plan_detail.id) as plan_day_in,(select count(id) FROM profil_visit pv where pv."user" ="user_id" and pv.tanggal_visit =master_call_plan_detail.date) as day_in_terpenuhi,
                (count(master_call_plan_detail.id))-(select count(id) FROM profil_visit pv where pv."user" ="user_id" and pv.tanggal_visit =master_call_plan_detail.date) AS day_in_tidak_terpenuhi')
                ->join('master_call_plan_detail', 'master_call_plan_detail.call_plan_id', '=', 'master_call_plan.id')
                ->whereBetween('master_call_plan_detail.date', [$tanggalfr, $tanggalto])
                ->groupBy('master_call_plan.user_id')
                ->groupBy('master_call_plan_detail.date');

            $dataA = DB::query()
                ->selectRaw('store_cabang.kode_cabang,user_info.fullname,count(store_info_distri.store_id) as jml_coverage,a.user_id,a.tanggal,a.plan_day_in,a.day_in_terpenuhi,a.day_in_tidak_terpenuhi')
                ->fromSub($data, 'a')
                ->join('user_info', 'user_info.user_id', '=', 'a.user_id')
                ->join('store_cabang', 'store_cabang.id', 'user_info.cabang_id')
                ->join('store_info_distri', 'store_info_distri.subcabang_id', '=', 'store_cabang.id')
                ->where('user_info.fullname', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_cabang.kode_cabang', 'LIKE', '%' . $searchByQuery . '%')
                ->groupBy('a.user_id')
                ->groupBy('user_info.fullname')
                ->groupBy('a.tanggal')
                ->groupBy('a.plan_day_in')
                ->groupBy('a.day_in_terpenuhi')
                ->groupBy('store_cabang.kode_cabang')
                ->groupBy('a.day_in_tidak_terpenuhi')
                ->orderBy('a.tanggal', 'asc')
                // ->limit($arr_pagination['limit'])
                // ->offset($arr_pagination['offset'])
                ->get();

            $count = $dataA->count();
        }
        // $log = DB::getQueryLog();
        // dd($log);

        if (count($dataA) == 0) {
            return response()->json(
                (new PublicModel())->array_respon_200_table_tr([], 0, $arr_pagination),
                200
            );
        }


        return response()->json(
            // (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            (new PublicModel())->array_respon_200_table_tr($dataA, $count, $arr_pagination),
            200
        );
    }

    public function getCoverage_planWeeklySummary(Request $request): JsonResponse
    {
        $tanggalfr = $request->query(key: 'tanggalfr');
        $tanggalto = $request->query(key: 'tanggalto');
        $searchByQuery = $request->query(key: 'search');

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
            ->leftJoin('profil_visit as pv', function ($join) {
                $join->on('pv.user', '=', 'mcp.user_id')
                    ->on('pv.tanggal_visit', '=', 'mcpd.date')
                    ->on('pv.store_id', '=', 'mcpd.store_id');
            })
            ->whereBetween('mcpd.date', [$tanggalfr, $tanggalto])
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
                sid.store_code as kode_toko,
                sid.store_name as nama_toko,
                mcpd.date as tanggal_plan,
                CASE
                    WHEN EXTRACT(DAY FROM mcpd.date) BETWEEN 1 AND 7 THEN 1
                    WHEN EXTRACT(DAY FROM mcpd.date) BETWEEN 8 AND 14 THEN 2
                    WHEN EXTRACT(DAY FROM mcpd.date) BETWEEN 15 AND 21 THEN 3
                    ELSE 4
                END as week_num,
                pv.ket as ket_visit
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
            $firstRow = $storeRows->first();
            $weekGroups = $storeRows->groupBy('week_num');
            $weekKet = [];

            for ($week = 1; $week <= 4; $week++) {
                $weekRows = $weekGroups->get($week, collect());
                $weekCount = $weekRows->count();
                $weekKetValues = $weekRows
                    ->pluck('ket_visit')
                    ->filter(function ($value) {
                        return !empty(trim((string) $value));
                    })
                    ->map(function ($value) {
                        return trim((string) $value);
                    })
                    ->unique()
                    ->values();

                $weekKet["week_{$week}"] = $weekKetValues->isNotEmpty()
                    ? $weekKetValues->implode(' | ')
                    : '-';
                $weekKet["week_{$week}_count"] = $weekCount;
            }

            $formatted->push([
                'nama_sales' => $firstRow->nama_sales,
                'kode_toko' => $firstRow->kode_toko,
                'nama_toko' => $firstRow->nama_toko,
                'week_1' => $weekKet['week_1_count'],
                'week_2' => $weekKet['week_2_count'],
                'week_3' => $weekKet['week_3_count'],
                'week_4' => $weekKet['week_4_count'],
                'ket' => 'Week 1: ' . $weekKet['week_1'] . ' | Week 2: ' . $weekKet['week_2'] . ' | Week 3: ' . $weekKet['week_3'] . ' | Week 4: ' . $weekKet['week_4'],
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
                'rows' => $formatted->values(),
                'grand_total' => $grandTotal,
                'week_totals' => $weekTotals,
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
                ->select(
                    'u.fullname',
                    'u.email',
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

            $data = $query
                ->orderBy('mcp.id', 'asc')
                ->get();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Success get call plan with user',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Error get data',
                'error' => $e->getMessage()
            ]);
        }
    }
}
