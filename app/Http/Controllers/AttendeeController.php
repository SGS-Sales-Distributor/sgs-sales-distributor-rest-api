<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\PublicModel;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class AttendeeController extends Controller
{

    public function getDataAbsen(int $user_id, Request $request): JsonResponse
    {
        // DB::enableQueryLog();
        $visit = DB::table('attendance')
            ->select('attendance.*')
            ->where('users_id', $user_id)
            ->where('attendee_date', Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
            ->first();
        // $count = $visit->count();
        // $log = DB::getQueryLog();
        // dd($log);


        if (!$visit) {
            return $this->clientErrorResponse(
                statusCode: 404,
                success: false,
                msg: "Unsuccessful Absen UserId : {$user_id} not found.",
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully Fetch Data.",
            resource: $visit,
        );
    }

    public function getDataAbsenById(int $id, Request $request): JsonResponse
    {
        // DB::enableQueryLog();
        $ateende = Attendee::select("attendance.id as id",
        "attendee_date",
        "attendance.users_id", 
        "attendee_time_in", 
        "attendee_latitude_in", 
        "attendee_longitude_in", 
        "images_in AS images_in", 
        "attendee_time_out", 
        "attendee_latitude_out", 
        "attendee_longitude_out", 
        "images_out AS images_out", 
        // "null as shedule_name", 
        // "null as schedule_in", 
        // "null as schedule_out",  
        "user_info.fullname AS profile_name", 
        "user_info.nik AS nik", 
        // "null as late_duration",
        // "null as remarks",
        // "/ as type"
        )
            ->join('user_info', 'user_info.user_id', '=', 'attendance.users_id')
            ->where('attendance.id', $id)
            // ->where('attendee_date', Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
            ->first();
        // $count = $visit->count();
        // $log = DB::getQueryLog();
        // dd($log);
        $ateende['attendee_date'] = date('d/m/Y', strtotime($ateende['attendee_date']));
        if ($ateende['images_in'] != null) {
            $ateende['images_in'] = 'https://absen.lspsgs.co.id:8087/images/' . $ateende['images_in'];
        }

        if ($ateende['images_out'] != null) {
            $ateende['images_out'] = 'https://absen.lspsgs.co.id:8087/images/' . $ateende['images_out'];
        }


        if (!$ateende) {
            return $this->clientErrorResponse(
                statusCode: 404,
                success: false,
                msg: "Unsuccessful Absen not found.",
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully Fetch Data.",
            resource: $ateende,
        );
    }

    public static function str_random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function addIn(Request $request, string $userNumber): JsonResponse
    {
        try {
            DB::beginTransaction();

            $image = $request->file('image');

            $image_name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';

            $destinationPath = public_path('/images');

            $image->move($destinationPath, $image_name);

            $user = User::where('number', $userNumber)->firstOrFail();

            $checkInVisit = Attendee::create([
                'attendee_date' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
                'attendee_time_in' => Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'),
                'attendee_longitude_in' => $request->lat_in,
                'attendee_latitude_in' => $request->long_in,
                'images_in' => $image_name,
                'attendee_time_out' => null,
                'attendee_longitude_out' => null,
                'attendee_latitude_out' => null,
                'images_out' => null,
                'workhour_code' => null,
                'absence_ref' => null,
                'absence_ref_desc' => null,
                'users_id' => $user->user_id,
                'created_by' => $user->user_id,
                'updated_by' => $user->user_id,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully Attendee In {$userNumber}.",
                resource: $checkInVisit
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

    public function getAllAbsen(Request $request):JsonResponse
    {
        $URL = URL::current();

        $search =  $request->query(key: 'search');
        $depcode = $request->query(key: 'depcode');
        $date_start = $request->query(key: 'start');
        $date_end = $request->query(key: 'end');
        $users_id = $request->query(key: 'users_id');

        $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);
        $count = (new Attendee())->count();
        // DB::enableQueryLog();
        $data = Attendee::select(["attendance.id as id",
        "attendee_date",
        "attendance.users_id", 
        "attendee_time_in", 
        "attendee_latitude_in", 
        "attendee_longitude_in", 
        "images_in AS img_in", 
        "attendee_time_out", 
        "attendee_latitude_out", 
        "attendee_longitude_out", 
        "images_out AS img_out", 
        // "null as shedule_name", 
        // "null as schedule_in", 
        // "null as schedule_out",  
        "user_info.fullname AS profile_name", 
        "user_info.nik AS nik", 
        "sts_jabatan.jabatan as jabatan",
        "sts_jabatan.level_atas as atasan1",
        "sts_jabatan.level_atas_1 as atasan2",
        "sts_jabatan.level_atas_2 as atasan3"
        ])
            ->join('user_info', 'user_info.user_id', '=', 'attendance.users_id')
            ->leftJoin('sts_jabatan','sts_jabatan.id','=','user_info.jabatan_id')
            ->whereRaw(" user_info.fullname like '%$search%' ")
            ->where('attendance.deleted_at', null)
            ->where('attendance.attendee_date', '>=', date('Y-m-d', strtotime($date_start)))
            ->where('attendance.attendee_date', '<=', date('Y-m-d', strtotime($date_end)))
            ->where('attendance.users_id', 'like', '%'.$users_id.'%')
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('attendee_date', 'desc')
            ->orderBy('attendee_time_in', 'asc')
            ->orderBy('user_info.fullname', 'asc')
            ->groupBy('attendance.id','user_info.fullname','user_info.nik','sts_jabatan.jabatan','sts_jabatan.level_atas','sts_jabatan.level_atas_1','sts_jabatan.level_atas_2')
            ->get();
            
        $count = $data->count();
        // $log = DB::getQueryLog();
        // dd($log);
        
        // return $data;
        if (count($data) == 0) {
            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: 'Data Kosong',
            );
        }


        return response()->json(
			// (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
			(new PublicModel())->array_respon_200_table_tr($data, $count, $arr_pagination),
			200
		);
    }

    public function ExportExcel($customer_data, $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
           
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->setCellValue('A1', 'Data Absensi Karyawan');
            // $spreadSheet->getActiveSheet()->setCellValue('A2', 'Perusahaan ');
            // $spreadSheet->getActiveSheet()->setCellValue('B2', ': ' . $user->customer_name);
            $spreadSheet->getActiveSheet()->setCellValue('A3', 'Periode ');
            $spreadSheet->getActiveSheet()->setCellValue('B3', ': ' . date('d F Y', strtotime($request->start)) . ' s/d ' . date('d F Y', strtotime($request->end)));

            $spreadSheet->getActiveSheet()->setCellValue('A5', 'NIP');
            $spreadSheet->getActiveSheet()->setCellValue('B5', 'Nama');
            $spreadSheet->getActiveSheet()->setCellValue('C5', 'Hari');
            $spreadSheet->getActiveSheet()->setCellValue('D5', 'Tanggal');
            $spreadSheet->getActiveSheet()->setCellValue('E5', 'Jadwal Masuk');
            $spreadSheet->getActiveSheet()->setCellValue('F5', 'Absensi Masuk');
            $spreadSheet->getActiveSheet()->setCellValue('G5', 'Absensi Pulang');
            $spreadSheet->getActiveSheet()->setCellValue('H5', 'Terlambat');
            $spreadSheet->getActiveSheet()->setCellValue('I5', 'Pulang Cepat');
            $spreadSheet->getActiveSheet()->setCellValue('J5', 'Lembur');
            $spreadSheet->getActiveSheet()->setCellValue('K5', 'Multiplikasi');
            $spreadSheet->getActiveSheet()->setCellValue('L5', 'Jam Efektif');
            $spreadSheet->getActiveSheet()->setCellValue('M5', 'Keterangan');
            $spreadSheet->getActiveSheet()->setCellValue('N5', 'Alasan');
            $spreadSheet->getActiveSheet()->setCellValue('O5', 'Status');

            $count = 6;
            foreach ($customer_data as $data) {
                $hari = '';
                if ($data->day == 'Monday') {
                    $hari = 'Senin';
                } else if ($data->day == 'Tuesday') {
                    $hari = 'Selasa';
                } else if ($data->day == 'Wednesday') {
                    $hari = 'Rabu';
                } else if ($data->day == 'Thursday') {
                    $hari = 'Kamis';
                } else if ($data->day == 'Friday') {
                    $hari = 'Jumat';
                } else if ($data->day == 'Saturday') {
                    $hari = 'Sabtu';
                } else if ($data->day == 'Sunday') {
                    $hari = 'Minggu';
                }

                $spreadSheet->getActiveSheet()->setCellValue('A' . $count, $data->nik);
                $spreadSheet->getActiveSheet()->setCellValue('B' . $count, $data->profile_name);
                $spreadSheet->getActiveSheet()->setCellValue('C' . $count, $hari);
                $spreadSheet->getActiveSheet()->setCellValue('D' . $count, $data->attendee_date);
                $spreadSheet->getActiveSheet()->setCellValue('E' . $count, ($data->keterangan != 'OFF' ? $data->schedule_in . ' - ' . $data->schedule_out : 'OFF'));
                $spreadSheet->getActiveSheet()->setCellValue('F' . $count, $data->attendee_time_in);
                $spreadSheet->getActiveSheet()->setCellValue('G' . $count, $data->attendee_time_out);
                $spreadSheet->getActiveSheet()->setCellValue('H' . $count, $data->late_duration);
                $spreadSheet->getActiveSheet()->setCellValue('I' . $count, $data->pulang_cepat);
                $spreadSheet->getActiveSheet()->setCellValue('J' . $count, $data->lembur);
                $spreadSheet->getActiveSheet()->setCellValue('K' . $count, $data->multiplikasi);
                $spreadSheet->getActiveSheet()->setCellValue('L' . $count, $data->jam_efektif);
                $spreadSheet->getActiveSheet()->setCellValue('M' . $count, $data->keterangan);
                $spreadSheet->getActiveSheet()->setCellValue('N' . $count, $data->alasan);
                $spreadSheet->getActiveSheet()->setCellValue('O' . $count, ($data->status == "0" ? "Menunggu Approval Atasan" : ($data->status == "1" ? "Diapprove Atasan" : ($data->status == "2" ? "Ditolak Atasan" : ($data->status == "3" ? "Dibatalkan" : "")))));

                if ($data->status == "3") {
                    $spreadSheet->getActiveSheet()->getStyle('O' . $count)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                } else if ($data->status == "2") {
                    $spreadSheet->getActiveSheet()->getStyle('O' . $count)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                }

                $count = $count + 1;
            }
            // }


            // check folder is exists
            if (!File::isDirectory(base_path('public/excel'))) {
                // if not exists then create folder
                File::makeDirectory(base_path('public/excel'));
            }

            if (!File::isDirectory(base_path('public/excel/kehadiran'))) {
                File::makeDirectory(base_path('public/excel/kehadiran'));
            }

            $Excel_writer = new Xls($spreadSheet);
            $path = base_path('public/excel/kehadiran/KEHADIRAN_' . $request->start . '_sd_' . $request->end . '.xls');
            $Excel_writer->save($path);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }


    public function getExport(Request $request): StreamedResponse{
        $search =  $request->query(key: 'search');
        $date_start = $request->query(key: 'start');
        $date_end = $request->query(key: 'end');
        $users_id = $request->query(key: 'users_id');
        
    
        $data = Attendee::select(["attendance.id as id",
        "attendee_date",
        "attendance.users_id", 
        "attendee_time_in", 
        "attendee_latitude_in", 
        "attendee_longitude_in", 
        "images_in AS img_in", 
        "attendee_time_out", 
        "attendee_latitude_out", 
        "attendee_longitude_out", 
        "images_out AS img_out", 
        // "null as shedule_name", 
        // "null as schedule_in", 
        // "null as schedule_out",  
        "user_info.fullname AS profile_name", 
        "user_info.nik AS nik", 
        // "null as late_duration",
        // "null as remarks",
        // "'attendee' as type"
        ])
            ->join('user_info', 'user_info.user_id', '=', 'attendance.users_id')
            ->whereRaw(" user_info.fullname like '%$search%' ")
            ->where('attendance.deleted_at', null)
            ->where('attendance.attendee_date', '>=', date('Y-m-d', strtotime($date_start)))
            ->where('attendance.attendee_date', '<=', date('Y-m-d', strtotime($date_end)))
            ->where('attendance.users_id', 'like', '%'.$users_id.'%')
            ->orderBy('attendee_date', 'desc')
            ->orderBy('attendee_time_in', 'asc')
            ->orderBy('user_info.fullname', 'asc')
            ->groupBy('attendance.id','user_info.fullname','user_info.nik')
            ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header kolom
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nama');
            $sheet->setCellValue('C1', 'Tanggal');
            $sheet->setCellValue('D1', 'Jam Masuk');
            $sheet->setCellValue('E1', 'Jam Pulang');
        
            // Isi data
            $rowIndex = 2;
            foreach ($data as $row) {
                $sheet->setCellValue("A$rowIndex", $row->id);
                $sheet->setCellValue("B$rowIndex", $row->profile_name);
                $sheet->setCellValue("C$rowIndex", $row->attendee_date);
                $sheet->setCellValue("D$rowIndex", $row->attendee_time_in);
                $sheet->setCellValue("E$rowIndex", $row->attendee_time_out);
                $rowIndex++;
            }
        
            $filename = "rekap_absensi_{$date_start}_to_{$date_end}.xlsx";
        
            // return new StreamedResponse(function () use ($spreadsheet, $filename) {
            //     $writer = new Xlsx($spreadsheet);
            //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //     header("Content-Disposition: attachment; filename=\"$filename\"");
            //     $writer->save('php://output');
            // });

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename);


    }


    public function addOut(Request $request, string $userNumber, int $attendId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $image = $request->file('image');

            $name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';

            $destinationPath = base_path('public/images');

            $image->move($destinationPath, $name);

            $salesman = User::where('user_id', $userNumber)->firstOrFail();

            $latestVisit = Attendee::where('id', $attendId)->firstOrFail();

            $latestVisit->update([
                'attendee_time_out' => Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'),
                'attendee_longitude_out' => $request->lat_out,
                'attendee_latitude_out' => $request->long_in,
                'images_out' => $name,
                'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully Attendee Out {$userNumber}.",
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

    public function add(Request $r)
    {
        // get user data
        $users = User::findOrFail($r->users_id);

        // apakah sedang izin
        $perizinan = Absence::where('absence_date', date('Y-m-d'))->where('users_id', $r->users_id)->orderBy('id', 'desc')->first();

        if ($r->has('accept_distance')) {
            if ($r->accept_distance == false) {
                if (!$perizinan) {
                    if ($users->is_mobile == 0) {
                        $this->return = array_merge((array) $this->return, [
                            'status' => false,
                            'message' => 'Absen gagal, jarak Anda terlalu jauh.',
                            'code' => 400
                        ]);
                        return response()->json($this->return, $this->return['code']);
                    }
                }
            }
        }
        // else {
        //     $this->return = array_merge((array)$this->return, [
        //         'status' => false,
        //         'message' => 'Absen gagal, jarak tidak diketahui',
        //         'code' => 400
        //     ]);
        //     return response()->json($this->return, $this->return['code']);
        // }

        $yesterday = date('Y-m-d', strtotime('-1 Day'));
        $date = date('Y-m-d');

        $placement = Placement::where('users_id', $r->users_id)->first();

        // check yesterday schedule, if yesterday schedule is through the day then get the yesterday schedule
        $schedule = (new SiteSchedule)->getYesterdaySchedule($yesterday, $placement, $r->users_id);

        // check attendee
        $isAttend = Attendee::select('attendance.id', 'attendance.attendee_time_out')
            ->where('attendee_date', $yesterday)
            ->where('users_id', $r->users_id)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($schedule)) {
            $isAttend = Attendee::select('attendance.id', 'attendance.attendee_time_out')
                ->where('attendee_date', $date)
                ->where('users_id', $r->users_id)
                ->orderBy('id', 'desc')
                ->first();
        }
        if (!empty($schedule)) {
            if ($schedule->schedule_in <= $schedule->schedule_out || empty($isAttend)) {
                // if the yesterday schedule is normal then get today schedule
                $schedule = (new SiteSchedule)->getTodaySchedule($date, $placement, $r->users_id);
                $isAttend = Attendee::select('attendance.id', 'attendance.attendee_time_out')
                    ->where('attendee_date', $date)
                    ->where('users_id', $r->users_id)
                    ->orderBy('id', 'desc')
                    ->first();
                if (empty($isAttend)) {
                    // if office or shift 1 schedule overtime untill next day, they can absen out untill 03:00:00
                    $maxHours = '03:00:00';
                    $now = date('H:i:s');
                    if ($now <= $maxHours) {
                        // check attendee
                        $isAttend = Attendee::select('attendance.id', 'attendance.attendee_time_out')
                            ->where('attendee_date', $yesterday)
                            ->where('users_id', $r->users_id)
                            ->orderBy('id', 'desc')
                            ->first();
                    }
                }
            }
        }

        if ($r->has('workhour_code')) {
            if ($perizinan) {
                $hoursNow = date('H:i:s');
                $schedulePlus2Hours = date('H:i:s', strtotime('+2 Hour', strtotime($schedule->schedule_in)));
                if ($perizinan->absence_code == 'MTA008' && $hoursNow >= $schedulePlus2Hours) {
                    $this->return = array_merge((array) $this->return, [
                        'status' => false,
                        'message' => 'Anda melebihi batas waktu izin terlambat, silakan ajukan cuti.',
                        'code' => 400
                    ]);
                    return response()->json($this->return, $this->return['code']);
                }
            }
            // else {
            //     $hoursNow = date('H:i:s');
            //     $schedulePlus2Hours = date('H:i:s', strtotime('+2 Hour', strtotime($schedule->schedule_in)));
            //     if ($hoursNow >= $schedulePlus2Hours) {
            //         $this->return = array_merge((array) $this->return, [
            //             'status' => false,
            //             'message' => 'Anda absen masuk lebih dari 2 jam, silakan ajukan cuti.',
            //             'code' => 400
            //         ]);
            //         return response()->json($this->return, $this->return['code']);
            //     }
            // }
        }

        if (!$isAttend || $isAttend->attendee_time_out != null) {
            $data = [
                'attendee_date' => $r->attendee_date,
                // 'attendee_time_in' => date('H:i:s'),
                'attendee_time_in' => $r->attendee_time_in,
                'attendee_latitude_in' => $r->attendee_latitude_in,
                'attendee_longitude_in' => $r->attendee_longitude_in,
                'images_in' => $r->images_in,
                'workhour_code' => $r->workhour_code,
                'absence_ref_desc' => $r->absence_ref_desc,
                'absence_ref' => $r->absence_ref,
                'users_id' => $r->users_id,
                'created_by' => $r->created_by,
                'day' => $r->day
            ];
            if (!($id = (new Attendee)->add($data))) {
                $this->return = array_merge((array) $this->return, [
                    'status' => false,
                    'message' => 'Gagal menyimpan data',
                    'code' => 400
                ]);
            } else {
                $this->return = array_merge((array) $this->return, [
                    'data' => $id
                ]);
            }
        } else {
            $data = [
                'attendee_time_out' => $r->attendee_time_out,
                'attendee_latitude_out' => $r->attendee_latitude_out,
                'attendee_longitude_out' => $r->attendee_longitude_out,
                'images_out' => $r->images_out,
                'updated_by' => $r->updated_by
            ];

            if (!($id = (new Attendee)->edit($data, $isAttend->id))) {
                $this->return = array_merge((array) $this->return, [
                    'status' => false,
                    'message' => 'Gagal mengubah data',
                    'code' => 400
                ]);
            } else {
                $this->return = array_merge((array) $this->return, [
                    'data' => $id->id
                ]);
            }
        }

        return response()->json($this->return, $this->return['code']);
    }
}
