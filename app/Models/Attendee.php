<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    use HasFactory;
    // protected $table = 'attendee';
    protected $table = 'attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'attendee_date',
        'attendee_time_in',
        'attendee_longitude_in',
        'attendee_latitude_in',
        'images_in',
        'attendee_time_out',
        'attendee_longitude_out',
        'attendee_latitude_out',
        'images_out',
        'workhour_code',
        'absence_ref',
        'absence_ref_desc',
        'day',
        'users_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getShowAll()
    {
        $data = $this->selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, MIN(site_schedule.schedule_in) AS schedule_in, MIN(site_schedule.schedule_out) AS schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, (MIN(attendance.attendee_time_in) - MIN(site_schedule.schedule_in)) AS late_duration')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            ->orderBy('attendance.attendee_date', 'desc')
            ->groupBy('attendance.id')
            ->get();
        return $data;
    }

    public function getYesterdayAttendance($yesterday, $users_id)
    {
        $data = $this->select('profiles.profile_name', 'attendance.attendee_time_in', 'attendance.attendee_latitude_in', 'attendance.attendee_longitude_in', 'attendance.attendee_time_out', 'attendance.attendee_latitude_out', 'attendance.attendee_longitude_out', 'attendance.attendee_date')
            ->join('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->where('attendance.attendee_date', $yesterday)
            ->where('attendance.users_id', $users_id)
            ->orderBy('attendee_time_in', 'desc')
            ->get();
        return $data;
    }

    public function getTodayAttendance($date, $users_id)
    {
        $data = $this->select('profiles.profile_name', 'attendance.attendee_time_in', 'attendance.attendee_latitude_in', 'attendance.attendee_longitude_in', 'attendance.attendee_time_out', 'attendance.attendee_latitude_out', 'attendance.attendee_longitude_out', 'attendance.attendee_date')
            ->join('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->where('attendance.attendee_date', $date)
            ->where('attendance.users_id', $users_id)
            ->orderBy('attendee_time_in', 'desc')
            ->get();
        if (count($data) == 0) {
            // if office or shift 1 schedule overtime untill next day, they can absen out untill 03:00:00
            $maxHours = '03:00:00';
            $now = date('H:i:s');
            if ($now <= $maxHours) {
                $data = $this->getYesterdayAttendance(date('Y-m-d', strtotime('-1 Day')), $users_id);
            }
        }
        return $data;
    }

    public function getRekapByDate(Request $r)
    {
        if ($r->users_id != '') {
            $data = $this->selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, site_schedule.schedule_in, site_schedule.schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration')
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                ->whereBetween('attendee_date', [$r->from, $r->to])
                ->where('attendance.users_id', $r->users_id)
                ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
                ->whereRaw("site_schedule.day = attendance.day OR site_schedule.day = UPPER(attendance.day)")
                ->where('site_schedule.customer_code', $r->customer)
                ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
                // ->union($off)
                ->orderBy('attendance.attendee_date', 'desc')
                ->get();
            return $data;
        } else {
            // $data = $this->selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, site_schedule.schedule_in, site_schedule.schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration')
            //     ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            //     ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            //     ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
            //     ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            //     ->leftJoin('user_roles', 'user_roles.users_id', '=', 'profiles.users_id')
            //     ->whereBetween('attendee_date', [$r->from, $r->to])
            //     ->where('mst_customer.customer_code', $r->customer)
            //     ->where('site_schedule.customer_code', $r->customer)
            //     ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
            //     ->whereRaw('site_schedule.day = attendance.day')
            //     // ->where('user_roles.mst_roles_id', 1)
            //     // ->orderBy('attendance.users_id', 'asc')
            //     ->orderBy('attendance.attendee_date', 'desc')
            //     ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
            //     ->get();
            // return $data;
            $data = $this->selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, site_schedule.schedule_in, site_schedule.schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration, md.departemen_name')
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->leftJoin('placement_departemen AS pd', 'pd.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_departemen AS md', 'md.departemen_code', '=', 'pd.departemen_code')
                ->leftJoin('user_roles', 'user_roles.users_id', '=', 'profiles.users_id')
                // ->where('user_roles.mst_roles_id', 1)
                ->whereBetween('attendee_date', [
                    $r->from,
                    $r->to
                ])
                ->where('mst_customer.customer_code', $r->customer);
            if ($r->departemen != null && $r->has('departemen')) {
                $data = $data
                    ->where('md.departemen_code', $r->departemen)
                    ->where('site_schedule.customer_code', $r->customer)
                    ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
                    ->whereRaw('site_schedule.day = attendance.day OR site_schedule.day = UPPER(attendance.day)')
                    // ->where('user_roles.mst_roles_id', 1)
                    // ->orderBy('attendance.users_id', 'asc')
                    ->orderBy('attendance.attendee_date', 'desc')
                    ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out', 'md.id')
                    ->get();
            } else {
                $data = $data->where('site_schedule.customer_code', $r->customer)
                    ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
                    ->whereRaw('site_schedule.day = attendance.day OR site_schedule.day = UPPER(attendance.day)')
                    // ->where('user_roles.mst_roles_id', 1)
                    // ->orderBy('attendance.users_id', 'asc')
                    ->orderBy('attendance.attendee_date', 'desc')
                    ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out', 'md.id')
                    ->get();
            }
            return $data;
        }
    }

    public function getRekapByPeriode(Request $r)
    {
        $placement = Placement::where('users_id', $r->users_id)->first();
        $customer = $placement->customer_code;
        $absence = Absence::selectRaw(
            "absence.id,
                absence_detail.absence_date,
                absence.users_id,
                '00:00:00',
                absence.absence_latitude,
                absence.absence_longitude,
                '00:00:00',
                absence.absence_description,
                '',
                MIN(mst_absence_type.absence_name),
                '00:00:00',
                MIN(profiles.profile_name) AS profile_name,
                MIN(profiles.nik) AS nik,
                '00:00:00',
                MIN (mst_absence_type.absence_name) AS absence_name,
                null,
                MIN(approval_1) AS approval_1"
        )
            ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
            ->leftJoin('mst_absence_type', 'mst_absence_type.absence_code', '=', 'absence.absence_code')
            ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
            ->whereBetween('absence_detail.absence_date', [$r->start, $r->end])
            ->where('absence.users_id', $r->users_id)
            ->where('absence.absence_code', '!=', 'MTA003')
            ->where('absence.deleted_at', null)
            ->groupBy('absence.id', 'absence_detail.id');

        $holiday = Holiday::selectRaw(
            "holiday.id,
                holiday.holiday_date,
                null,
                '00:00:00',
                '',
                '',
                '00:00:00',
                '',
                '',
                holiday.holiday_name,
                '00:00:00',
                '',
                '',
                '00:00:00',
                holiday_name,
                null,
                null"
        )
            ->whereBetween('holiday.holiday_date', [$r->start, $r->end]);

        $attendee = Attendee::selectRaw(
            "attendance.id,
                attendee_date,
                attendance.users_id,
                attendee_time_in,
                attendee_latitude_in,
                attendee_longitude_in,
                attendee_time_out,
                attendee_latitude_out,
                attendee_longitude_out,
                MIN(site_schedule.schedule_name) AS schedule_name,
                MIN(site_schedule.schedule_in) AS schedule_in,
                MIN(profiles.profile_name) AS profile_name,
                MIN(profiles.nik) AS nik,
                MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
                '' AS deskripsi,
                absence_ref AS absence_ref,
                null AS approval_1"
        )
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            ->whereBetween('attendee_date', [$r->start, $r->end])
            ->where('attendance.users_id', $r->users_id)
            ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
            ->whereRaw('site_schedule.day = attendance.day')
            ->where('site_schedule.customer_code', $customer)
            ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
            // ->union($absence)
            // ->union($holiday)
            ->orderBy('attendee_date', 'desc')
            ->get();

        $data = $attendee;
        return $data;
    }

    public function countAttendance(Request $r)
    {
        $placement = Placement::where('users_id', $r->users_id)->first();
        $customer = $placement->customer_code;
        $data = Attendee::selectRaw(
            "attendance.id,
                attendee_date,
                attendance.users_id,
                attendee_time_in,
                attendee_latitude_in,
                attendee_longitude_in,
                attendee_time_out,
                attendee_latitude_out,
                attendee_longitude_out,
                MIN(site_schedule.schedule_name) AS schedule_name,
                MIN(site_schedule.schedule_in) AS schedule_in,
                MIN(profiles.profile_name) AS profile_name,
                MIN(profiles.nik) AS nik,
                MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
                '' AS deskripsi,
                absence_ref AS absence_ref,
                null AS approval_1"
        )
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            ->whereBetween('attendee_date', [$r->start, $r->end])
            ->where('attendance.users_id', $r->users_id)
            ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
            ->whereRaw('site_schedule.day = attendance.day')
            ->where('site_schedule.customer_code', $customer)
            ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
            ->orderBy('attendee_date', 'desc')
            ->get();

        return count($data);
    }

    public function getDataForExport(Request $r)
    {
        // if (!$r->has('users_id')) {
            $getUsersId = User::selectRaw('profiles.nik, profiles.profile_name, placement.users_id')
                ->leftjoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftjoin('placement_departemen AS pd', 'pd.users_id', '=', 'users.id')
                ->leftjoin('profiles', 'profiles.users_id', '=', 'users.id')
                ->where('users.status', 1)
                ->where('profiles.profile_name', 'not like', "%ADMINISTRATOR%")
                ->where('users.deleted_at', null)
                ->where('placement.customer_code', $r->customer);
            if ($r->users_id != null && $r->has('users_id')) {
                $getUsersId = $getUsersId
                    ->where('users.id', $r->users_id);
                    // ->get();
            }
            if ($r->departemen != null && $r->has('departemen')) {
                $getUsersId = $getUsersId
                    ->where('pd.departemen_code', $r->departemen)
                    ->get();
            } else {
                $getUsersId = $getUsersId
                    ->get();
            }

            if (count($getUsersId) == 0) {
                $status = [
                    'query'     => 'user',
                    'status'    => false,
                    'message'   => 'Empty User',
                    'code'      => 400
                ];
                return $status;
            }
            // get schedule between date
            foreach ($getUsersId as $key => $value) {
                // $getScheduleDate = Schedule::selectRaw('users_id, schedule.schedule_start_date, schedule.schedule_end_date, site_schedule.schedule_in, site_schedule.schedule_out, site_schedule.schedule_code')
                //     ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'schedule.schedule_code')
                //     ->where('site_schedule.customer_code', $r->customer)
                //     ->where('schedule_start_date', '>=', $r->from)
                //     ->where('schedule_end_date', '<=', $r->to)
                //     ->where('schedule.users_id', $value->users_id)
                //     ->orderBy('schedule_start_date')
                //     ->groupBy('schedule.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out', 'site_schedule.schedule_code')
                //     ->get();

                $dates = DB::select(
                    "SELECT
                            to_char(CURRENT_DATE + i, 'Day') AS day,
                            (CURRENT_DATE + i) AS date
                            FROM generate_series(DATE '" . $r->from . "'- CURRENT_DATE,
                            DATE '" . $r->to . "' - CURRENT_DATE ) i"
                );

                foreach ($dates as $key => $val) {
                    $schedule[] = DB::select(
                        "SELECT
                            '$val->day' AS day,
                            '$val->date' AS attendee_date,
                            '$value->users_id' AS users_id,
                            '' AS attendee_time_in,
                            '' AS attendee_latitude_in,
                            '' AS attendee_longitude_in,
                            '' AS attendee_time_out,
                            '' AS attendee_latitude_out,
                            '' AS attendee_longitude_out,
                            MIN(site_schedule.schedule_name) AS schedule_name,
                            MIN(site_schedule.schedule_in) AS schedule_in,
                            MIN(site_schedule.schedule_out) AS schedule_out,
                            '" . str_replace("'", "", $value->profile_name) . "' AS profile_name,
                            '$value->nik' AS nik,
                            '00:00:00' AS late_duration,
                            '00:00:00' AS jam_efektif,
                            '00:00:00' AS lembur,
                            '00:00:00' AS pulang_cepat,
                            '00:00:00' AS multiplikasi,
                            --holiday.holiday_name AS keterangan,
                            --'' AS alasan
                            CASE WHEN MIN(holiday.holiday_name) IS NOT NULL THEN MIN(holiday.holiday_name)
                            ELSE
                            (
                                SELECT
                                    case
                                        when ck.nama_cuti_khusus is not null then nama_cuti_khusus
                                        else absence_name
                                    end
                                FROM absence_detail
                                JOIN absence ON absence.id = absence_detail.absence_id
                                LEFT JOIN mst_absence_type ON mst_absence_type.absence_code = absence.absence_code
                                LEFT JOIN cuti_khusus ck on absence.absence_code = ck.kode_cuti_khusus
                                WHERE absence_detail.absence_date = '$val->date'
                                AND users_id = '$value->users_id'
                                AND absence.approval_1 != 3
                                order by absence.id desc
                                limit 1
                            ) END AS keterangan,
                            (
                                SELECT absence_description
                                FROM absence_detail
                                LEFT JOIN absence ON absence.id = absence_detail.absence_id
                                WHERE absence_detail.absence_date = '$val->date'
                                AND users_id = '$value->users_id'
                                AND absence.approval_1 != 3
                                order by absence.id desc
                                limit 1
                            ) AS alasan,
                            (
                                SELECT approval_1::char
                                FROM absence_detail
                                LEFT JOIN absence ON absence.id = absence_detail.absence_id
                                WHERE absence_detail.absence_date = '$val->date'
                                AND users_id = '$value->users_id'
                                AND absence.approval_1 != 3
                                order by absence.id desc
                                limit 1
                            ) AS status
                            FROM schedule
                            LEFT JOIN site_schedule ON site_schedule.schedule_code = schedule.schedule_code
                            LEFT JOIN holiday ON holiday.holiday_date = '$val->date'
                            WHERE schedule.users_id = $value->users_id
                            AND site_schedule.customer_code = '$r->customer'
                            AND '$val->date' BETWEEN schedule.schedule_start_date AND schedule.schedule_end_date
                            AND site_schedule.day = '" . str_replace(' ', '', $val->day) . "'
                            GROUP BY profile_name
                            "
                    );
                }
            }
            $mergeSchedule = array_merge(...$schedule);

            foreach ($getUsersId as $key => $value) {
                $absence = Absence::selectRaw(
                    "to_char(absence_detail.absence_date, 'Day'),
                        absence_detail.absence_date,
                        absence.users_id,
                        absence.start_time,
                        absence.absence_latitude,
                        absence.absence_longitude,
                        absence.end_time,
                        absence.absence_description,
                        '',
                        '',
                        '00:00:00',
                        '00:00:00',
                        MIN(profiles.profile_name) AS profile_name,
                        MIN(profiles.nik) AS nik,
                        '00:00:00',
                        '00:00:00',
                        '00:00:00',
                        '00:00:00',
                        '00:00:00',
                        MIN(mst_absence_type.absence_name),
                        absence.absence_description"
                )
                    ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
                    ->leftJoin(
                        'mst_absence_type',
                        'mst_absence_type.absence_code',
                        '=',
                        'absence.absence_code'
                    )
                    ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
                    ->leftJoin('placement', 'placement.users_id', '=', 'absence.users_id')
                    ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                    ->whereBetween('absence_detail.absence_date', [$r->from, $r->to])
                    ->where('absence.users_id', $value->users_id)
                    ->where('mst_customer.customer_code', $r->customer)
                    ->groupBy('absence.id', 'absence_detail.id');

                $attendee = Attendee::selectRaw(
                    "to_char(attendee_date, 'Day') AS day,
                        attendee_date,
                        MIN(attendance.users_id) as users_id,
                        MIN(attendee_time_in) as attendee_time_in,
                        MIN(attendee_latitude_in) as attendee_latitude_in,
                        MIN(attendee_longitude_in) as attendee_longitude_in,
                        MAX(attendee_time_out) as attendee_time_out,
                        MAX(attendee_latitude_out) as attendee_latitude_out,
                        MAX(attendee_longitude_out) as attendee_longitude_out,
                        MIN(site_schedule.schedule_name) AS schedule_name,
                        MIN(site_schedule.schedule_in) AS schedule_in,
                        MAX(site_schedule.schedule_out) AS schedule_out,
                        MIN(profiles.profile_name) AS profile_name,
                        MIN(profiles.nik) AS nik,
                        MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
                        MIN(attendance.attendee_time_out - attendance.attendee_time_in - interval '1 minute' * site_schedule.break_time) AS jam_efektif,
                        MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS lembur,
                        MIN(site_schedule.schedule_out - attendance.attendee_time_out) AS pulang_cepat,
                        MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS multiplikasi,
                        --holiday_name AS keterangan,
                        CASE WHEN MIN(holiday_name) IS NOT NULL THEN MIN(holiday_name)
                        ELSE
                        (
                            select
                                case
                                    when ck.nama_cuti_khusus is not null then nama_cuti_khusus
                                    else absence_name
                                end
                            from
                                absence_detail
                            join absence on
                                absence.id = absence_detail.absence_id
                            left join mst_absence_type on
                                mst_absence_type.absence_code = absence.absence_code
                            left join cuti_khusus ck on
                                absence.absence_code = ck.kode_cuti_khusus
                            where
                                absence_detail.absence_date = attendee_date
                                and users_id = '$value->users_id'
                                and absence.approval_1 != 3
                                order by absence.id desc
                                limit 1
                        ) END AS keterangan,
                        (SELECT absence_description FROM absence_detail LEFT JOIN absence ON absence.id = absence_detail.absence_id WHERE absence_detail.absence_date = attendee_date AND users_id = '$value->users_id' AND absence.approval_1 != 3 order by absence.id desc limit 1) AS alasan,
                        (SELECT approval_1::char FROM absence_detail LEFT JOIN absence ON absence.id = absence_detail.absence_id WHERE absence_detail.absence_date = attendee_date AND users_id = '$value->users_id' AND absence.approval_1 != 3 order by absence.id desc limit 1) AS status
                        "
                )
                    ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                    ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                    ->leftJoin('mst_customer', 'mst_customer.customer_code', 'placement.customer_code')
                    ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                    ->leftJoin('holiday', 'holiday.holiday_date', '=', 'attendee_date')
                    ->whereBetween('attendee_date', [$r->from, $r->to])
                    ->where('attendance.users_id', $value->users_id)
                    ->where('site_schedule.customer_code', $r->customer)
                    ->whereRaw("site_schedule.day = attendance.day")
                    // ->union($absence)
                    ->groupBy('attendance.attendee_date', 'holiday.holiday_name')
                    ->orderBy('attendee_date', 'desc')
                    ->get();

                $weekend = DB::select(
                    "SELECT
                        to_char(CURRENT_DATE + i, 'Day') AS day,
                        (CURRENT_DATE + i) AS attendee_date,
                        '$value->users_id' AS users_id,
                        '' AS attendee_time_in,
                        '' AS attendee_latitude_in,
                        '' AS attendee_longitude_in,
                        '' AS attendee_time_out,
                        '' AS attendee_latitude_out,
                        '' AS attendee_longitude_out,
                        '' AS schedule_name,
                        '00:00:00' AS schedule_in,
                        '00:00:00' AS schedule_out,
                        '" . str_replace("'", "", $value->profile_name) . "' AS profile_name,
                        '$value->nik' AS nik,
                        '' AS late_duration,
                        '' AS jam_efektif,
                        '' AS lembur,
                        '' AS pulang_cepat,
                        '' AS multiplikasi,
                        '' AS keterangan,
                        '' AS alasan,
                        '' AS status
                        FROM generate_series(DATE '" . $r->from . "'- CURRENT_DATE,
                        DATE '" . $r->to . "' - CURRENT_DATE ) i"
                );

                $weekend = array_map(function ($a) use ($mergeSchedule) {
                    foreach ($mergeSchedule as $data) {
                        if ($a->attendee_date === $data->attendee_date && $a->users_id === $data->users_id) {
                            return $data;
                        }
                    }
                    return $a;
                }, $weekend);

                $weekend = array_map(function ($a) use ($attendee) {
                    foreach ($attendee as $data) {
                        if ($a->attendee_date === $data->attendee_date) {
                            return $data;
                        }
                    }
                    return $a;
                }, $weekend);
                $data[] = $weekend;
            }
            return array_merge(...$data);
        // } else {
        //     // if single user export
        //     $absence = Absence::selectRaw(
        //         "to_char(absence_detail.absence_date, 'Day'),
        //     absence_detail.absence_date,
        //     absence.users_id,
        //     '00:00:00',
        //     absence.absence_latitude,
        //     absence.absence_longitude,
        //     '00:00:00',
        //     absence.absence_description,
        //     '',
        //     '',
        //     '00:00:00',
        //     '00:00:00',
        //     MIN(profiles.profile_name) AS profile_name,
        //     MIN(profiles.nik) AS nik,
        //     '00:00:00',
        //     '00:00:00',
        //     '00:00:00',
        //     '00:00:00',
        //     '00:00:00',
        //     MIN(mst_absence_type.absence_name),
        //     ''"
        //     )
        //         ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
        //         ->leftJoin('mst_absence_type', 'mst_absence_type.absence_code', '=', 'absence.absence_code')
        //         ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
        //         ->leftJoin('placement', 'placement.users_id', '=', 'absence.users_id')
        //         ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
        //         ->whereBetween('absence_detail.absence_date', [$r->from, $r->to])
        //         ->where('absence.users_id', $r->users_id)
        //         ->where('mst_customer.customer_code', $r->customer)
        //         ->groupBy('absence.id', 'absence_detail.id');

        //     $holiday = Holiday::selectRaw(
        //         "to_char(holiday.holiday_date, 'Day'),
        //     holiday.holiday_date,
        //     null,
        //     '00:00:00',
        //     '',
        //     '',
        //     '00:00:00',
        //     '',
        //     '',
        //     holiday.holiday_name,
        //     '00:00:00',
        //     '00:00:00',
        //     '',
        //     '',
        //     '00:00:00',
        //     '00:00:00',
        //     '00:00:00',
        //     '00:00:00',
        //     '00:00:00',
        //     holiday.holiday_name,
        //     ''"
        //     )
        //         ->whereBetween('holiday.holiday_date', [$r->from, $r->to]);

        //     $attendee = Attendee::selectRaw(
        //         "to_char(attendee_date, 'Day') AS day,
        //     attendee_date,
        //     attendance.users_id,
        //     attendee_time_in,
        //     attendee_latitude_in,
        //     attendee_longitude_in,
        //     attendee_time_out,
        //     attendee_latitude_out,
        //     attendee_longitude_out,
        //     MIN(site_schedule.schedule_name) AS schedule_name,
        //     MIN(site_schedule.schedule_in) AS schedule_in,
        //     MIN(site_schedule.schedule_out) AS schedule_out,
        //     MIN(profiles.profile_name) AS profile_name,
        //     MIN(profiles.nik) AS nik,
        //     MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
        //     MIN(attendance.attendee_time_out - attendance.attendee_time_in) AS jam_efektif,
        //     MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS lembur,
        //     MIN( site_schedule.schedule_out - attendance.attendee_time_out) AS pulang_cepat,
        //     '00:00:00' AS multiplikasi,
        //     null AS keterangan,
        //     absence_ref_desc AS alasan"
        //     )
        //         ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
        //         ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
        //         ->leftJoin('mst_customer', 'mst_customer.customer_code', 'placement.customer_code')
        //         ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
        //         ->whereBetween('attendee_date', [$r->from, $r->to])
        //         ->where('attendance.users_id', $r->users_id)
        //         ->where('mst_customer.customer_code', $r->customer)
        //         ->whereRaw("site_schedule.day = attendance.day")
        //         ->union($absence)
        //         ->union($holiday)
        //         ->groupBy('attendance.id')
        //         ->orderBy('attendee_date')
        //         ->get();

        //     $weekend = DB::select(
        //         "SELECT
        //     to_char(CURRENT_DATE + i, 'Day') AS day,
        //     (CURRENT_DATE + i) AS attendee_date,
        //     '' AS users_id,
        //     '00:00:00' AS attendee_time_in,
        //     '' AS attendee_latitude_in,
        //     '' AS attendee_longitude_in,
        //     '00:00:00' AS attendee_time_out,
        //     '' AS attendee_latitude_out,
        //     '' AS attendee_longitude_out,
        //     '' AS schedule_name,
        //     '00:00:00' AS schedule_in,
        //     '00:00:00' AS schedule_out,
        //     '' AS profile_name,
        //     '' AS nik,
        //     '00:00:00' AS late_duration,
        //     '00:00:00' AS jam_efektif,
        //     '00:00:00' AS lembur,
        //     '00:00:00' AS pulang_cepat,
        //     '00:00:00' AS multiplikasi,
        //     '' AS keterangan,
        //     '' AS alasan
        //     FROM generate_series(DATE '" . $r->from . "'- CURRENT_DATE,
        //     DATE '" . $r->to . "' - CURRENT_DATE ) i"
        //     );

        //     $weekend = array_map(function ($a) use ($attendee) {
        //         foreach ($attendee as $data) {
        //             if ($a->attendee_date === $data->attendee_date) {
        //                 return $data;
        //             }
        //         }
        //         return $a;
        //     }, $weekend);

        //     $data = $weekend;
        //     return $data;
        // }
    }

    public function getSummary(Request $r)
    {
        $data = $this->selectRaw("users.id, profiles.nik, profiles.profile_name, attendance.users_id, COUNT(attendance.id) AS total_hari,
                (SELECT COUNT(late)
                FROM (SELECT count(attendance.id)
                        FROM attendance
                        LEFT JOIN site_schedule ON site_schedule.schedule_code = attendance.workhour_code
                        WHERE attendance.attendee_time_in > site_schedule.schedule_in
                        AND attendee_date BETWEEN '" . $r->dateFrom . "' AND '" . $r->dateTo . "'
                        AND site_schedule.day = attendance.day
                        AND site_schedule.customer_code = '" . $r->customer_code . "'
                        AND users.id = attendance.users_id
                        GROUP BY attendee_date) AS late)
                AS total_terlambat,
                (SELECT COUNT(cuti)
                FROM (SELECT count(absence.id)
                        FROM absence
                        LEFT JOIN absence_detail ON absence.id = absence_detail.absence_id
                        WHERE absence_detail.absence_date BETWEEN '" . $r->dateFrom . "' AND '" . $r->dateTo . "'
                        AND absence.users_id = attendance.users_id
                        AND absence_code IN ('MTA002', 'MTA005','MTA006')
                        GROUP BY absence_detail.absence_date) AS cuti)
                AS total_cuti,
                (SELECT COUNT(sdr)
                FROM (SELECT count(absence.id)
                        FROM absence
                        LEFT JOIN absence_detail ON absence.id = absence_detail.absence_id
                        WHERE absence_detail.absence_date BETWEEN '" . $r->dateFrom . "' AND '" . $r->dateTo . "'
                        AND absence.users_id = attendance.users_id
                        AND absence_code IN ('MTA001')
                        GROUP BY absence_detail.absence_date) AS sdr)
                AS total_sdr
            ")
            ->leftJoin('users', 'users.id', '=', 'attendance.users_id')
            ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
            ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->whereBetween('attendance.attendee_date', [$r->dateFrom, $r->dateTo])
            ->where('mst_customer.customer_code', $r->customer_code)
            ->groupBy('users.id', 'profiles.nik', 'profiles.profile_name', 'attendance.users_id')
            ->orderBy('profiles.profile_name', 'asc')
            ->get();

        return $data;
    }

    public function getExportDataSummary(Request $r)
    {
        $data = $this->selectRaw("profiles.nik, profiles.profile_name, attendance.users_id, COUNT(attendance.id) AS total_hari, 0 AS total_terlambat,
            (SELECT COUNT(late)
            FROM (SELECT count(attendance.id)
                    FROM attendance
                    LEFT JOIN site_schedule ON site_schedule.schedule_code = attendance.workhour_code
                    WHERE attendance.attendee_time_in > site_schedule.schedule_in
                    AND attendee_date BETWEEN '" . $r->dateFrom . "' AND '" . $r->dateTo . "'
                    AND site_schedule.day = attendance.day
                    AND site_schedule.customer_code = '" . $r->customer_code . "'
                    AND users.id = attendance.users_id
                    GROUP BY attendee_date) AS late)
            AS total_terlambat
            ")
            ->leftJoin('users', 'users.id', '=', 'attendance.users_id')
            ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
            ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->whereBetween('attendance.attendee_date', [$r->dateFrom, $r->dateTo])
            ->where('mst_customer.customer_code', $r->customer_code)
            ->groupBy('users.id', 'profiles.nik', 'profiles.profile_name', 'attendance.users_id')
            ->orderBy('profiles.profile_name', 'asc')
            ->get();
        return $data;
    }


    public function getAttendeePayroll($customer_code, $departemen_code, $start_date, $end_date)
    {
        $data = $this->selectRaw(
            "profiles.id,
                profiles.users_id,
                mst_customer.customer_name,
                profiles.profile_name,
                profiles.identity_number,
                profiles.nama_di_bank,
                (SELECT count(attendance.id)
                    FROM attendance
                    WHERE attendee_date >= '" . $start_date . "'
                    AND attendee_date <= '" . $end_date . "'
                    AND attendee_time_in IS NOT null
                    AND attendee_time_out IS NOT null
                    AND attendance.users_id = profiles.users_id
                    GROUP BY profiles.profile_name)
                    AS total_hadir,
                mst_ump.daerah as daerah_ump,
                mst_ump.nilai_ump,
                mst_departemen.departemen_name,
                mst_departemen.departemen_name,
                MAX(contract_periode.start_contract) AS start_contract,
                MAX(contract_periode.end_contract) AS end_contract,
                profiles.rek_number,
                profiles.nama_bank,
                0 AS contract_duration,
                0 AS total_jam_kerja,
                0 AS total_upah,
                0 AS uang_makan,
                0 AS total_penyesuaian,
                0 AS total_keseluruhan_upah,
                0 AS adjust"
        )
            ->leftJoin('contract_periode', 'contract_periode.users_id', '=', 'attendance.users_id')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
            ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            ->leftJoin('user_roles', 'user_roles.users_id', '=', 'attendance.users_id')
            ->leftJoin('placement_departemen', 'placement_departemen.users_id', 'attendance.users_id')
            ->leftJoin('mst_departemen', 'mst_departemen.departemen_code', 'placement_departemen.departemen_code')
            ->leftJoin('mst_ump', 'mst_ump.daerah', '=', 'mst_departemen.daerah_ump')
            ->where('user_roles.mst_roles_id', 1)
            // ->where('contract_periode.deleted_at', null)
            ->where('mst_customer.customer_code', $customer_code)
            ->where('mst_departemen.departemen_code', $departemen_code)
            ->whereMonth('contract_periode.start_contract', date('m', strtotime($start_date)))
            ->where('attendee_date', '>=', $start_date)
            ->where('attendee_date', '<=', $end_date)
            ->where('mst_ump.tahun', date('Y'))
            ->groupBy('profiles.id', 'mst_ump.id', 'mst_customer.id', 'mst_departemen.id')
            ->orderBy('profiles.profile_name', 'asc')
            ->get();

        return $data;
    }

    public function getAttendeeSchedule($users_id, $start_date, $end_date)
    {
        $data = $this->selectRaw('attendance.id, profiles.profile_name, attendance.attendee_date, attendee_time_in, attendee_time_out, site_schedule.break_time, attendance.day, schedule.total_workday, 0 AS nilai_ump, attendance.users_id')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('schedule', 'schedule.users_id', '=', 'attendance.users_id')
            ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            ->whereRaw('site_schedule.day = attendance.day')
            ->whereRaw('attendee_date BETWEEN schedule.schedule_start_date AND schedule.schedule_end_date')
            ->where('attendance.users_id', $users_id)
            ->where('attendee_time_in', '!=', null)
            ->where('attendee_time_out', '!=', null)
            ->where('attendee_date', '>=', $start_date)
            ->where('attendee_date', '<=', $end_date)
            ->orderBy('attendee_date', 'desc')
            ->get();

        return $data;
    }

    // public function getHrpsFormat(Request $request)
    // {
    //     // from temp finger absen
    //     $finger = DB::table('temp_finger_absen as tfa')
    //         ->selectRaw("tfa.nik,tanggal as date, jam as time, status::int as status, p.profile_name, 'MB - Finger' as company_name")
    //         ->join('profiles as p','p.nik','=','tfa.nik')
    //         ->join('placement_departemen as pd','pd.users_id','=','p.users_id')
    //         ->where('pd.departemen_code',$request->depcode)
    //         ->where('tanggal','>=', $request->start)
    //         ->where('tanggal','<=', $request->end);

    //     // from attendance android
    //     $in = Attendee::selectRaw("
    //     p.nik,
    //     att1.attendee_date as date,
    //     att1.attendee_time_in as time,
    //     1 as status,
    //     p.profile_name,
    //     'MARTINA BERTO' as company_name
    //     ")
    //         ->from('attendance AS att1')
    //         ->join('profiles AS p', 'p.users_id', '=', 'att1.users_id')
    //         ->join('placement_departemen AS pd','pd.users_id','=','att1.users_id')
    //         // ->where('att1.users_id', $request->users_id)
    //         ->where('pd.departemen_code',$request->depcode)
    //         ->where('attendee_date','>=', $request->start)
    //         ->where('attendee_date','<=', $request->end)
    //         ->whereNotNull('att1.attendee_time_in');

    //     $out = Attendee::selectRaw("
    //     p.nik,
    //     att2.attendee_date as date,
    //     att2.attendee_time_out as time,
    //     0 as status,
    //     p.profile_name,
    //     'MARTINA BERTO' as company_name
    //     ")
    //         ->from('attendance AS att2')
    //         ->join('profiles AS p', 'p.users_id', '=', 'att2.users_id')
    //         ->join('placement_departemen AS pd','pd.users_id','=','att2.users_id')
    //         // ->where('att2.users_id', $request->users_id)
    //         ->where('pd.departemen_code',$request->depcode)
    //         ->where('attendee_date','>=', $request->start)
    //         ->where('attendee_date','<=', $request->end)
    //         ->whereNotNull('att2.attendee_time_in')
    //         ->union($in)
    //         ->union($finger)
    //         ->orderBy('profile_name', 'asc')
    //         ->orderBy('date', 'asc')
    //         ->orderBy('status', 'desc')
    //         ->get();

    //     return $out;
    //     // return $data;
    // }


    public function getHrpsFormat(Request $request)
    {
        $data = DB::select(
            "select
                nik,
                date,
                case
                    when status = 1 then min(time)
                    when status = 0 then max(time)
                end
                as time,
                status,
                min(profile_name) as profile_name,
                max(company_name) as company_name
            from
            (
                (select
                    tfa.nik,
                    tfa.tanggal as date,
                    tfa.jam as time,
                    tfa.status::int as status,
                    p.profile_name,
                    'MARTINA BERTO' as company_name
                from
                    temp_finger_absen tfa
                join profiles p on
                    p.nik = tfa.nik
                join placement_departemen pd on
                    pd.users_id = p.users_id
                where
                    --pd.departemen_code = '" . $request->depcode . "'
                    tanggal >= '" . $request->start . "'
                    and tanggal <= '" . $request->end . "')
            union
                (select
                    p.nik,
                    CASE
					  WHEN att2.attendee_time_in > att2.attendee_time_out THEN att2.attendee_date + 1
					ELSE
					  attendee_date
					END AS date,
                    att2.attendee_time_out as time,
                    0 as status,
                    p.profile_name,
                    'MARTINA BERTO' as company_name
                from
                    attendance as att2
                inner join profiles as p on
                    p.users_id = att2.users_id
                inner join placement_departemen as pd on
                    pd.users_id = att2.users_id
                where
                    --pd.departemen_code = '" . $request->depcode . "'
                    attendee_date >= '" . $request->start . "'
                    and attendee_date <= '" . $request->end . "'
                    and att2.attendee_time_out IS NOT null)
            union
                (select 
                    p.nik,
                    att1.attendee_date as date,
                    att1.attendee_time_in as time,
                    1 as status,
                    p.profile_name,
                    'MARTINA BERTO' as company_name
                from
                    attendance as att1
                inner join profiles as p on
                    p.users_id = att1.users_id
                inner join placement_departemen as pd on
                    pd.users_id = att1.users_id
                where
                    --pd.departemen_code = '" . $request->depcode . "'
                    attendee_date >= '" . $request->start . "'
                    and attendee_date <= '" . $request->end . "'
                    and att1.attendee_time_in IS NOT null)
            ) as list
            group by nik, date, status
            order by
                profile_name asc,
                date asc,
                status desc"
        );

        return $data;
    }



    // new export excel kehadiran
    public function exportKehadiran(Request $r)
    {
        $getUsersId = User::selectRaw('profiles.nik, profiles.profile_name, placement.users_id')
            ->leftjoin('placement', 'placement.users_id', '=', 'users.id')
            ->leftjoin('placement_departemen AS pd', 'pd.users_id', '=', 'users.id')
            ->leftjoin('profiles', 'profiles.users_id', '=', 'users.id')
            ->where('users.status', 1)
            ->where('users.deleted_at', null)
            ->where('placement.customer_code', $r->customer);
        if ($r->departemen != null && $r->has('departemen')) {
            $getUsersId = $getUsersId
                ->where('pd.departemen_code', $r->departemen);
        }
        $getUsersId = $getUsersId->get();

        foreach ($getUsersId as $key => $value) {
            $attendee = Attendee::selectRaw(
                "to_char(attendee_date, 'Day') AS day,
                    attendee_date,
                    MIN(attendance.users_id) as users_id,
                    MIN(attendee_time_in) as attendee_time_in,
                    MIN(attendee_latitude_in) as attendee_latitude_in,
                    MIN(attendee_longitude_in) as attendee_longitude_in,
                    MAX(attendee_time_out) as attendee_time_out,
                    MAX(attendee_latitude_out) as attendee_latitude_out,
                    MAX(attendee_longitude_out) as attendee_longitude_out,
                    MIN(site_schedule.schedule_name) AS schedule_name,
                    MIN(site_schedule.schedule_in) AS schedule_in,
                    MAX(site_schedule.schedule_out) AS schedule_out,
                    MIN(profiles.profile_name) AS profile_name,
                    MIN(profiles.nik) AS nik,
                    MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
                    MIN(attendance.attendee_time_out - attendance.attendee_time_in) AS jam_efektif,
                    MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS lembur,
                    MIN(site_schedule.schedule_out - attendance.attendee_time_out) AS pulang_cepat,
                    MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS multiplikasi,
                    holiday_name AS keterangan,
                    --'' AS alasan
                    (SELECT string_agg(absence_description, ', ' ORDER BY absence_description) FROM absence_detail LEFT JOIN absence ON absence.id = absence_detail.absence_id WHERE absence_detail.absence_date = attendee_date AND users_id = '$value->users_id') AS alasan"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', 'placement.customer_code')
                ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                ->leftJoin('holiday', 'holiday.holiday_date', '=', 'attendee_date')
                ->whereBetween('attendee_date', [$r->from, $r->to])
                ->where('attendance.users_id', $value->users_id)
                ->where('site_schedule.customer_code', $r->customer)
                ->whereRaw("site_schedule.day = attendance.day")
                ->groupBy('attendance.attendee_date', 'holiday.holiday_name')
                ->orderBy('attendee_date', 'desc')
                ->get();

            $weekend = DB::select(
                "SELECT
                        to_char(CURRENT_DATE + i, 'Day') AS day,
                        (CURRENT_DATE + i) AS attendee_date,
                        '$value->users_id' AS users_id,
                        '' AS attendee_time_in,
                        '' AS attendee_latitude_in,
                        '' AS attendee_longitude_in,
                        '' AS attendee_time_out,
                        '' AS attendee_latitude_out,
                        '' AS attendee_longitude_out,
                        '' AS schedule_name,
                        '00:00:00' AS schedule_in,
                        '00:00:00' AS schedule_out,
                        '" . str_replace("'", "", $value->profile_name) . "' AS profile_name,
                        '$value->nik' AS nik,
                        '' AS late_duration,
                        '' AS jam_efektif,
                        '' AS lembur,
                        '' AS pulang_cepat,
                        '' AS multiplikasi,
                        '' AS keterangan,
                        '' AS alasan
                        FROM generate_series(DATE '" . $r->from . "'- CURRENT_DATE,
                        DATE '" . $r->to . "' - CURRENT_DATE ) i"
            );


            $weekend = array_map(function ($a) use ($attendee) {
                foreach ($attendee as $data) {
                    if ($a->attendee_date === $data->attendee_date) {
                        return $data;
                    }
                }
                return $a;
            }, $weekend);
            $data[] = $weekend;
        }

        return array_merge(...$data);
    }

    public function get_data_($search, $arr_pagination, $depcode, $date_start, $date_end, $users_id)
    {
        $search = strtolower($search);
        $absence = Absence::selectRaw(
            "absence.id,
            absence_detail.absence_date as attendee_date,
            absence.users_id,
            start_time as attendee_time_in,
            absence.absence_latitude,
            absence.absence_longitude,
            '',
            end_time,
            '',
            '',
            '',
            MIN(mst_absence_type.absence_name),
            '00:00:00',
            '00:00:00',
            MIN(profiles.profile_name) AS profile_name,
            MIN(profiles.nik) AS nik,
            '00:00:00',
            MIN(absence.absence_description),
            'absence' AS type"
        )
        ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
        ->leftJoin('mst_absence_type', 'mst_absence_type.absence_code', '=','absence.absence_code')
        ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
        ->leftJoin('placement', 'placement.users_id', '=', 'absence.users_id')
        ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
        ->join('placement_departemen', 'placement_departemen.users_id', '=', 'absence.users_id')
        ->join('mst_departemen', 'mst_departemen.departemen_code', '=', 'placement_departemen.departemen_code')
        ->where('absence.deleted_at', null)
        ->whereIn('absence.absence_code', ['MTA002','MTA003','MTA007'])
        ->where('mst_departemen.departemen_code', $depcode)
        ->whereRaw(" lower(profile_name) like '%$search%' ")
        ->where('absence_detail.absence_date', '>=', date('Y-m-d', strtotime($date_start)))
        ->where('absence_detail.absence_date', '<=', date('Y-m-d', strtotime($date_end)))
        ->where('absence.users_id', 'like', '%'.$users_id.'%')
        ->groupBy('absence.id', 'absence_detail.id','absence_detail.absence_date');

        $data = $this->selectRaw("
        attendance.id, 
        attendee_date, 
        attendance.users_id, 
        attendee_time_in, 
        attendee_latitude_in, 
        attendee_longitude_in, 
        images_in AS img_in, 
        attendee_time_out, 
        attendee_latitude_out, 
        attendee_longitude_out, 
        images_out AS img_out, 
        MIN(site_schedule.schedule_name) AS schedule_name, 
        MIN(site_schedule.schedule_in) AS schedule_in, 
        MIN(site_schedule.schedule_out) AS schedule_out, 
        MIN(profiles.profile_name) AS profile_name, 
        MIN(profiles.nik) AS nik, 
        (MIN(attendance.attendee_time_in) - MIN(site_schedule.schedule_in)) AS late_duration,
        MIN(absence.absence_description) AS remarks,
        'attendee' as type")
            ->leftJoin('absence','absence.id','=','attendance.absence_ref')
            ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            ->join('placement_departemen', 'placement_departemen.users_id', '=', 'attendance.users_id')
            ->join('mst_departemen', 'mst_departemen.departemen_code', '=', 'placement_departemen.departemen_code')
            ->whereRaw(" lower(profile_name) like '%$search%' ")
            ->where('attendance.deleted_at', null)
            ->where('mst_departemen.departemen_code', $depcode)
            ->whereRaw("site_schedule.day = attendance.day")
            ->union($absence)
            ->where('attendance.attendee_date', '>=', date('Y-m-d', strtotime($date_start)))
            ->where('attendance.attendee_date', '<=', date('Y-m-d', strtotime($date_end)))
            ->where('attendance.users_id', 'like', '%'.$users_id.'%')
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('attendee_date', 'desc')
            ->orderBy('attendee_time_in', 'asc')
            ->orderBy('profile_name', 'asc')
            ->groupBy('attendance.id','absence_detail.absence_date')
            ->get();
        return $data;
    }

    public function count_data_($search, $depcode, $date_start, $date_end, $users_id)
    {
        $data = $this->selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, MIN(site_schedule.schedule_in) AS schedule_in, MIN(site_schedule.schedule_out) AS schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, (MIN(attendance.attendee_time_in) - MIN(site_schedule.schedule_in)) AS late_duration')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
            ->join('placement_departemen', 'placement_departemen.users_id', '=', 'attendance.users_id')
            ->join('mst_departemen', 'mst_departemen.departemen_code', '=', 'placement_departemen.departemen_code')
            ->whereRaw(" lower(profile_name) like '%$search%' ")
            ->where('attendance.deleted_at', null)
            ->where('mst_departemen.departemen_code', $depcode)
            ->where('attendance.attendee_date', '>=', date('Y-m-d', strtotime($date_start)))
            ->where('attendance.attendee_date', '<=', date('Y-m-d', strtotime($date_end)))
            ->where('attendance.users_id', 'like', '%'.$users_id.'%')
            ->orderBy('attendance.attendee_date', 'desc')
            ->groupBy('attendance.id')
            ->get();
        return $data;
    }
}
