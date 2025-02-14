<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
