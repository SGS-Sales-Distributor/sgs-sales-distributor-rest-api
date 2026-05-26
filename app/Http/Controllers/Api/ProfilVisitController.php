<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfilVisit;
use App\Models\PublicModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;  // ← TAMBAHAN: wajib untuk streamDownload
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProfilVisitController extends Controller
{
	public function getAll(Request $request): JsonResponse
	{
		$URL          = URL::current();
		$searchByQuery = $request->query('search');
		$tanggalfr    = $request->query('tanggalfr');
		$tanggalto    = $request->query('tanggalto');
		$depcode      = $request->query('depcode');
		$customerCode = $request->query('customer_code');
		$userId       = $request->query('user_id');

		// Jika customer atau tanggal belum diisi, return kosong
		if (!$customerCode || !$tanggalfr || !$tanggalto) {
			return response()->json(
				(new PublicModel())->array_respon_200_table_tr(collect([]), 0, [
					'limit'  => 10,
					'offset' => 0,
				]),
				200
			);
		}

		$baseQuery = function () use ($searchByQuery, $tanggalfr, $tanggalto, $customerCode, $userId) {
			return DB::table('master_call_plan_detail')
				->select([
					'profil_visit.id as id',
					'store_info_distri.store_id AS idToko',
					'store_info_distri.store_name as nama_toko',
					'store_info_distri.store_alias as alias_toko',
					'store_info_distri.store_address as alamat_toko',
					'store_info_distri.store_phone as nomor_telepon_toko',
					'store_info_distri.store_fax as nomor_fax_toko',
					'store_info_distri.store_type_id',
					'store_info_distri.subcabang_id',
					'store_info_distri.active as status_toko',
					'store_info_distri.store_code as kode_toko',
					'profil_visit.id as visit_id',
					'profil_visit.user as nama_salesman',
					'profil_visit.tanggal_visit as tanggal_visit',
					'profil_visit.time_in as waktu_masuk',
					'profil_visit.time_out as waktu_keluar',
					'profil_visit.photo_visit as photo_visit',
					'profil_visit.photo_visit_in_second as photo_visit_in_second',
					'profil_visit.photo_visit_out as photo_visit_out',
					'profil_visit.photo_visit_out_second as photo_visit_out_second',
					'profil_visit.ket as keterangan',
					'profil_visit.keterangan_out as keterangan_out',
					'profil_visit.approval as approval',
					'master_call_plan_detail.date as tanggal_plan',
					'user_info.fullname as userSalesman',
					'user_info.customer_code as customer_code',
					'mst_customer.customer_name as customer_name',
					'profil_notvisit.id as idNotVisit',
					'profil_notvisit.ket as ketNotVisit',
				])
				->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
				->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
				->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
				->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'user_info.customer_code')
				->leftJoin('profil_visit', function ($leftJoin) {
					$leftJoin->on('profil_visit.user', '=', 'master_call_plan.user_id')
						->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
						->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
				})
				->leftJoin('profil_notvisit', function ($lj) {
					$lj->on('profil_notvisit.id_master_call_plan_detail', '=', 'master_call_plan_detail.id');
				})
				->where('user_info.customer_code', $customerCode)
				->whereBetween('master_call_plan_detail.date', [$tanggalfr, $tanggalto])
				->when($userId, fn($q) => $q->where('master_call_plan.user_id', $userId))
				->when($searchByQuery, fn($q) => $q->where('user_info.fullname', 'LIKE', '%' . $searchByQuery . '%'))
				->orderBy('master_call_plan_detail.date', 'asc');
		};

		$arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery(
			$URL,
			$request->limit,
			$request->offset,
			$depcode,
			$tanggalfr,
			$tanggalto
		);

		$visits = $baseQuery()
			->limit($arr_pagination['limit'])
			->offset($arr_pagination['offset'])
			->get();

		$count = $baseQuery()->count();

		if ($visits->isEmpty()) {
			return $this->errorResponse(
				statusCode: 500,
				success: false,
				msg: 'Data Kosong',
			);
		}

		return response()->json(
			(new PublicModel())->array_respon_200_table_tr($visits, $count, $arr_pagination),
			200
		);
	}

	public function getOne(int $id): JsonResponse
	{
		$visit = DB::table('profil_visit')
			->select(
				'profil_visit.*',
				'store_info_distri.store_name',
				'store_info_distri.store_address as alamat_toko',
				'user_info.fullname',
				'user_info.phone',
				'user_info.email',
			)
			->leftJoin('store_info_distri', 'profil_visit.store_id', '=', 'store_info_distri.store_id')
			->join('user_info', 'profil_visit.user', '=', 'user_info.user_id')
			->where('profil_visit.id', $id)
			->first();

		if (!$visit) {
			return $this->clientErrorResponse(
				statusCode: 404,
				success: false,
				msg: "Visit data with id {$id} not found.",
			);
		}

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch visit {$id} data.",
			resource: $visit,
		);
	}

	public function getVisitUser(string $userId, Request $request): JsonResponse
	{
		$visit = DB::table('master_call_plan_detail')
			->select([
				'profil_visit.id as id',
				'store_info_distri.store_id',
				'store_info_distri.store_name as nama_toko',
				'store_info_distri.store_alias as alias_toko',
				'store_info_distri.store_address as alamat_toko',
				'store_info_distri.store_phone as nomor_telepon_toko',
				'store_info_distri.store_fax as nomor_fax_toko',
				'store_info_distri.store_type_id',
				'store_info_distri.subcabang_id',
				'store_info_distri.active as status_toko',
				'store_info_distri.store_code as kode_toko',
				'profil_visit.id as visit_id',
				'profil_visit.user as nama_salesman',
				'profil_visit.tanggal_visit as tanggal_visit',
				'profil_visit.time_in as waktu_masuk',
				'profil_visit.time_out as waktu_keluar',
				'profil_visit.photo_visit as photo_visit',
				'profil_visit.photo_visit_in_second as photo_visit_in_second',
				'profil_visit.photo_visit_out as photo_visit_out',
				'profil_visit.photo_visit_out_second as photo_visit_out_second',
				'profil_visit.ket as keterangan',
				'profil_visit.keterangan_out as keterangan_out',
				'profil_visit.approval as approval',
				'master_call_plan_detail.date as tanggal_plan',
				'user_info.fullname as userSalesman',
			])
			->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
			->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
			->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
			->leftJoin('profil_visit', function ($leftJoin) {
				$leftJoin->on('profil_visit.user', '=', 'master_call_plan.user_id')
					->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
					->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
			})
			->where('master_call_plan.user_id', DB::raw("'" . $userId . "'"))
			->where('user_info.user_id', DB::raw("'" . $userId . "'"))
			->whereBetween('master_call_plan_detail.date', ["2024-09-02", Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d')])
			->orderBy('master_call_plan_detail.date', 'desc')
			->get();

		if (!$visit) {
			return $this->clientErrorResponse(
				statusCode: 404,
				success: false,
				msg: "Visit data UserId : {$userId} not found.",
			);
		}

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch Visit UserId : {$userId} data.",
			resource: $visit,
		);
	}

	public function getVisitUserByTanggal(int $userId, Request $request): JsonResponse
	{
		$visit = DB::table('master_call_plan_detail')
			->select([
				'profil_visit.id as id',
				'store_info_distri.store_id',
				'store_info_distri.store_name as nama_toko',
				'store_info_distri.store_alias as alias_toko',
				'store_info_distri.store_address as alamat_toko',
				'store_info_distri.store_phone as nomor_telepon_toko',
				'store_info_distri.store_fax as nomor_fax_toko',
				'store_info_distri.store_type_id',
				'store_info_distri.subcabang_id',
				'store_info_distri.active as status_toko',
				'store_info_distri.store_code as kode_toko',
				'profil_visit.id as visit_id',
				'profil_visit.user as nama_salesman',
				'profil_visit.tanggal_visit as tanggal_visit',
				'profil_visit.time_in as waktu_masuk',
				'profil_visit.time_out as waktu_keluar',
				'profil_visit.photo_visit as photo_visit',
				'profil_visit.photo_visit_in_second as photo_visit_in_second',
				'profil_visit.photo_visit_out as photo_visit_out',
				'profil_visit.photo_visit_out_second as photo_visit_out_second',
				'profil_visit.ket as keterangan',
				'profil_visit.keterangan_out as keterangan_out',
				'profil_visit.approval as approval',
				'master_call_plan_detail.date as tanggal_plan',
				'user_info.fullname as userSalesman',
				'profil_notvisit.id as idNotVisit',
				'profil_notvisit.ket as ketNotVisit',
			])
			->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
			->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
			->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
			->leftJoin('profil_visit', function ($leftJoin) {
				$leftJoin->on('profil_visit.user', '=', 'master_call_plan.user_id')
					->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
					->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
			})
			->leftJoin('profil_notvisit', function ($leftJoin2) {
				$leftJoin2->on('profil_notvisit.id_master_call_plan_detail', '=', 'master_call_plan_detail.id');
			})
			->where('master_call_plan.user_id', DB::raw("'" . $userId . "'"))
			->where('user_info.user_id', DB::raw("'" . $userId . "'"))
			->whereBetween('master_call_plan_detail.date', ["'" . $request->dariTanggal . "'", "'" . $request->sampaiTanggal . "'"])
			->orderBy('master_call_plan_detail.date', 'desc')
			->get();

		if (count($visit) == 0) {
			return $this->clientErrorResponse(
				statusCode: 404,
				success: false,
				msg: "Visit data UserId : {$userId} not found.",
			);
		}

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch Visit UserId : {$userId} data.",
			resource: $visit,
		);
	}

	public function updateOne(Request $request, int $id)
	{
		$validator = Validator::make($request->all(), [
			'photo_visit'       => 'nullable',
			'photo_visit_out'   => 'nullable',
			'tanggal_visit'     => 'nullable',
			'purchase_order_in' => 'nullable',
			'condit_owner'      => 'nullable',
			'lat_in'            => 'nullable',
			'long_in'           => 'nullable',
			'lat_out'           => 'nullable',
			'long_out'          => 'nullable',
		]);

		if ($validator->fails()) {
			return $this->clientErrorResponse(
				statusCode: 422,
				success: false,
				msg: $validator->errors()->first(),
			);
		}

		$visit = ProfilVisit::where('id', $id)->firstOrFail();

		try {
			DB::beginTransaction();

			$visit->update(['approval' => 1]);

			DB::commit();

			return $this->successResponse(
				statusCode: 200,
				success: true,
				msg: "Successfully update visit {$id} data.",
			);
		} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
			DB::rollBack();
			return $this->errorResponse(statusCode: $e->getStatusCode(), success: false, msg: $e->getMessage());
		} catch (\Error $e) {
			DB::rollBack();
			return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
		}
	}

	public function removeOne(int $id): JsonResponse
	{
		$visit = ProfilVisit::findOrFail($id);
		$visit->delete();

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully remove visit {$id} data.",
		);
	}

	public function exportWeekly(Request $request): JsonResponse
	{
		$tanggalfr = $request->query('tanggalfr');
		$tanggalto = $request->query('tanggalto');

		if (empty($tanggalfr) || empty($tanggalto)) {
			return $this->clientErrorResponse(
				statusCode: 422,
				success: false,
				msg: 'Tanggal wajib diisi',
			);
		}

		$data = DB::table('master_call_plan_detail as mcpd')
			->join('master_call_plan as mcp', 'mcp.id', '=', 'mcpd.call_plan_id')
			->join('user_info as ui', 'ui.user_id', '=', 'mcp.user_id')
			->join('store_info_distri as sid', 'sid.store_id', '=', 'mcpd.store_id')
			->selectRaw("
                ui.fullname AS nama_sales,
                sid.store_code AS kode_toko,
                sid.store_name AS nama_toko,
                COALESCE(STRING_AGG(TO_CHAR(mcpd.date, 'YYYY-MM-DD'), ', ') FILTER (WHERE EXTRACT(DAY FROM mcpd.date) BETWEEN 1 AND 7), '-') AS week_1,
                COALESCE(STRING_AGG(TO_CHAR(mcpd.date, 'YYYY-MM-DD'), ', ') FILTER (WHERE EXTRACT(DAY FROM mcpd.date) BETWEEN 8 AND 14), '-') AS week_2,
                COALESCE(STRING_AGG(TO_CHAR(mcpd.date, 'YYYY-MM-DD'), ', ') FILTER (WHERE EXTRACT(DAY FROM mcpd.date) BETWEEN 15 AND 21), '-') AS week_3,
                COALESCE(STRING_AGG(TO_CHAR(mcpd.date, 'YYYY-MM-DD'), ', ') FILTER (WHERE EXTRACT(DAY FROM mcpd.date) >= 22), '-') AS week_4
            ")
			->whereBetween(DB::raw('DATE(mcpd.date)'), [$tanggalfr, $tanggalto])
			->groupBy('ui.fullname', 'sid.store_code', 'sid.store_name')
			->orderBy('ui.fullname')
			->orderBy('sid.store_code')
			->get();

		$countWeek = function ($val) {
			if (!$val || $val === '-') return 0;
			return count(explode(', ', $val));
		};

		$totalW1 = $data->sum(fn($row) => $countWeek($row->week_1));
		$totalW2 = $data->sum(fn($row) => $countWeek($row->week_2));
		$totalW3 = $data->sum(fn($row) => $countWeek($row->week_3));
		$totalW4 = $data->sum(fn($row) => $countWeek($row->week_4));

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: 'Successfully fetch export weekly data.',
			resource: [
				'rows'   => $data,
				'totals' => [
					'week_1'      => $totalW1,
					'week_2'      => $totalW2,
					'week_3'      => $totalW3,
					'week_4'      => $totalW4,
					'grand_total' => $totalW1 + $totalW2 + $totalW3 + $totalW4,
				],
			]
		);
	}

	public function getCustomerOptions(): JsonResponse
	{
		$customers = DB::table('mst_customer')
			->select('customer_code', 'customer_name')
			->whereNull('deleted_at')
			->where('status', 1)
			->orderBy('customer_name')
			->get();

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: 'Successfully fetch customer options.',
			resource: $customers,
		);
	}

	public function exportProfilVisit(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|JsonResponse
	{
		$customerCode = $request->query('customer_code');
		$userId       = $request->query('user_id');
		$tanggalFr    = $request->query('tanggalfr');
		$tanggalTo    = $request->query('tanggalto');

		if (empty($customerCode) || empty($tanggalFr) || empty($tanggalTo)) {
			return $this->clientErrorResponse(
				statusCode: 422,
				success: false,
				msg: 'Perusahaan, tanggal awal, dan tanggal akhir wajib diisi untuk export.',
			);
		}

		$rows = DB::table('master_call_plan_detail')
			->select([
				'user_info.fullname as nama_salesman',
				'user_info.nik as nik',
				'user_info.customer_code as customer_code',
				'mst_customer.customer_name as customer_name',
				'master_call_plan_detail.date as tanggal_plan',
				'store_info_distri.store_name as nama_toko',
				'store_info_distri.store_address as alamat_toko',
				'profil_visit.tanggal_visit',
				'profil_visit.time_in',
				'profil_visit.time_out',
				'profil_visit.ket as keterangan_in',
				'profil_visit.keterangan_out',
				'profil_visit.photo_visit as foto_in_1',
				'profil_visit.photo_visit_in_second as foto_in_2',
				'profil_visit.photo_visit_out as foto_out_1',
				'profil_visit.photo_visit_out_second as foto_out_2',
				'profil_visit.lat_in',
				'profil_visit.long_in',
				'profil_visit.lat_out',
				'profil_visit.long_out',
				'profil_visit.approval',

				DB::raw("
                CASE
                    WHEN profil_visit.tanggal_visit = master_call_plan_detail.date
                         AND profil_visit.time_in IS NOT NULL
                         AND profil_visit.time_out IS NOT NULL
                        THEN 'Terpenuhi'
                    WHEN profil_visit.time_in IS NOT NULL
                         AND profil_visit.time_out IS NULL
                        THEN 'Tidak Check Out'
                    WHEN master_call_plan_detail.date < CURRENT_DATE
                         AND profil_visit.time_in IS NULL
                        THEN 'Tidak Terpenuhi'
                    ELSE 'Belum Visit'
                END as status_visit
            "),

				DB::raw("
                CASE
                    WHEN profil_visit.approval = 1 THEN 'Approved'
                    WHEN profil_visit.time_in IS NOT NULL
                         AND profil_visit.time_out IS NOT NULL
                        THEN 'Menunggu Approval'
                    ELSE '-'
                END as status_approval
            "),
			])
			->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
			->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
			->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
			->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'user_info.customer_code')
			->leftJoin('profil_visit', function ($lj) {
				$lj->on('profil_visit.user', '=', 'master_call_plan.user_id')
					->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
					->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
			})
			->where('user_info.customer_code', $customerCode)
			->whereBetween('master_call_plan_detail.date', [$tanggalFr, $tanggalTo])
			->when($userId, fn($q) => $q->where('master_call_plan.user_id', $userId))
			->orderBy('user_info.fullname')
			->orderBy('master_call_plan_detail.date')
			->get();

		$customerName = DB::table('mst_customer')
			->where('customer_code', $customerCode)
			->value('customer_name') ?? $customerCode;

		$salesmanName = $userId
			? DB::table('user_info')->where('user_id', $userId)->value('fullname')
			: null;

		$sanitize = fn(string $s) => preg_replace('/[^A-Za-z0-9_\-]/', '_', $s);

		$fileName = "ProfilVisit_" .
			$sanitize($customerName) . "_" .
			($salesmanName ? $sanitize($salesmanName) : 'Semua_Salesman') . "_" .
			($tanggalFr && $tanggalTo
				? $sanitize($tanggalFr) . '_sd_' . $sanitize($tanggalTo)
				: 'Semua_Periode') .
			".xlsx";

		$baseImageUrl = 'https://absen.lspsgs.co.id:8087/images/';

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Profil Visit');

		$lastCol = 'S';

		$headers = [
			'A' => 'NO',
			'B' => 'NAMA SALESMAN',
			'C' => 'NIK',
			'D' => 'TANGGAL PLAN',
			'E' => 'NAMA TOKO',
			'F' => 'ALAMAT TOKO',
			'G' => 'TANGGAL VISIT',
			'H' => 'CHECK IN',
			'I' => 'CHECK OUT',
			'J' => 'FOTO IN 1',
			'K' => 'FOTO IN 2',
			'L' => 'FOTO OUT 1',
			'M' => 'FOTO OUT 2',
			'N' => 'KETERANGAN IN',
			'O' => 'KETERANGAN OUT',
			'P' => 'LOKASI IN',
			'Q' => 'LOKASI OUT',
			'R' => 'STATUS VISIT',
			'S' => 'STATUS APPROVAL',
		];

		$headerRow = 5;

		foreach ($headers as $col => $label) {
			$sheet->setCellValue("{$col}{$headerRow}", $label);
		}

		$writeFotoCell = function ($ws, $cell, $filename, $baseUrl) {
			if (!$filename) {
				$ws->setCellValue($cell, '-');
				return;
			}

			$url = $baseUrl . $filename;

			$ws->setCellValue($cell, 'Lihat Foto');
			$ws->getCell($cell)->getHyperlink()->setUrl($url);

			$ws->getStyle($cell)->applyFromArray([
				'font' => [
					'color' => ['argb' => 'FF1155CC'],
					'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE
				]
			]);
		};

		$writeLocationCell = function ($ws, $cell, $lat, $long) {
			if (
				$lat === null || $lat === '' ||
				$long === null || $long === ''
			) {
				$ws->setCellValue($cell, '-');
				return;
			}

			$coord = "{$lat}, {$long}";
			$url = "https://maps.google.com/?q={$lat},{$long}";

			$ws->setCellValue($cell, $coord);

			$ws->getCell($cell)
				->getHyperlink()
				->setUrl($url);

			$ws->getStyle($cell)->applyFromArray([
				'font' => [
					'color' => ['argb' => 'FF1155CC'],
					'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE
				]
			]);
		};

		$startRow = 6;

		foreach ($rows as $i => $row) {

			$r = $startRow + $i;

			$sheet->setCellValue("A$r", $i + 1);
			$sheet->setCellValue("B$r", $row->nama_salesman);
			$sheet->setCellValue("C$r", $row->nik ?? '-');
			$sheet->setCellValue("D$r", $row->tanggal_plan ?? '-');
			$sheet->setCellValue("E$r", $row->nama_toko);
			$sheet->setCellValue("F$r", $row->alamat_toko ?? '-');
			$sheet->setCellValue("G$r", $row->tanggal_visit ?? '-');
			$sheet->setCellValue("H$r", $row->time_in ?? 'Belum Check In');
			$sheet->setCellValue("I$r", $row->time_out ?? 'Belum Check Out');

			$writeFotoCell($sheet, "J$r", $row->foto_in_1, $baseImageUrl);
			$writeFotoCell($sheet, "K$r", $row->foto_in_2, $baseImageUrl);
			$writeFotoCell($sheet, "L$r", $row->foto_out_1, $baseImageUrl);
			$writeFotoCell($sheet, "M$r", $row->foto_out_2, $baseImageUrl);

			$sheet->setCellValue("N$r", $row->keterangan_in ?? '-');
			$sheet->setCellValue("O$r", $row->keterangan_out ?? '-');

			// FIX LONG LAT + LINK MAPS
			$writeLocationCell($sheet, "P$r", $row->lat_in, $row->long_in);
			$writeLocationCell($sheet, "Q$r", $row->lat_out, $row->long_out);

			$sheet->setCellValue("R$r", $row->status_visit);
			$sheet->setCellValue("S$r", $row->status_approval);
		}

		$widths = [
			'A' => 5,
			'B' => 22,
			'C' => 14,
			'D' => 14,
			'E' => 25,
			'F' => 30,
			'G' => 14,
			'H' => 14,
			'I' => 14,
			'J' => 14,
			'K' => 14,
			'L' => 14,
			'M' => 14,
			'N' => 28,
			'O' => 28,
			'P' => 35,
			'Q' => 35,
			'R' => 18,
			'S' => 18
		];

		foreach ($widths as $c => $w) {
			$sheet->getColumnDimension($c)->setWidth($w);
		}

		$sheet->freezePane('A6');

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

		return response()->streamDownload(
			fn() => $writer->save('php://output'),
			$fileName,
			[
				'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			]
		);
	}
}
