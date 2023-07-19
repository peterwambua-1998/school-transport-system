<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetails;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function attendance()
    {

        $students = Student::all();
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('reports.attendance')->with([
            'notifications' => $notifications,
            'students' => $students
        ]);

    }


    public function getAttendance()
    {
        $date = date('Y-m');
        $students = Student::all();

        $attPresent = 0;

        $num = 1;

        $table = '';

        
        
        foreach ($students as $student) {

            $present = Attendance::where('present', 'LIKE', "true")->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $date. '%')->get();

            $absent = Attendance::where('present', 'LIKE', "false")->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $date. '%')->get();
            
            $attPresent = count($present);

            $attAbsent = count($absent);

            $presentArr = [];
            foreach ($present as $pre) {

                $final = substr_replace($pre->created_at, '', 11);

                $final .= ' ' . $pre->route_time . ' ';
                array_push($presentArr, $final);
            }
            
            $presentString = join(',',$presentArr);


            $absentArr = [];
            foreach ($absent as $abse) {

                $finalabse = substr_replace($abse->created_at , '', 11);

                $finalabse .= ' ' . $abse->route_time . ' ';

                array_push($absentArr, $finalabse);
            }
            
            $absentString = join(',',$absentArr);

            $table .= '<tr><td>' . $num++ .'</td>' . '<td>'. $student->first_name . $student->last_name .'</td>' . '<td>'. $student->grade .'</td>' . '<td data-toggle="popover" title="Dates Present" data-content="'. $presentString .'">'.$attPresent.'</td>' . '<td data-toggle="popover" title="Dates Absent" data-content="'. $absentString .'">'. $attAbsent .'</td></tr>';
            
        }

        return response($table);
       
        
       
    }

    public function getAttendanceQuery(Request $request)
    {
        //return response($request->month);
        $students = Student::all();

        $attPresent = 0;

        $num = 1;

        $table = '';
        foreach ($students as $student) {

            $present = Attendance::where('present', '=', 'present')->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $request->month. '%')->get();

            $absent = Attendance::where('present', '=', 'absent')->where('student_id', '=', $student->id)->where('created_at', 'LIKE','%'. $request->month . '%')->get();
            
            $attPresent = count($present);

            $attAbsent = count($absent);

            $presentArr = [];
            foreach ($present as $pre) {

                $final = substr_replace($pre->created_at, '', 11);

                $final .= ' ' . $pre->route_time . ' ';

                array_push($presentArr, $final);
            }
            
            $presentString = join(',',$presentArr);


            $absentArr = [];
            foreach ($absent as $abse) {

                $finalabse = substr_replace($abse->created_at, '', 11);

                $finalabse .= ' ' . $abse->route_time . ' ';
                array_push($absentArr, $finalabse);
            }
            
            $absentString = join(',', $absentArr);

            $table .= '<tr><td>' . $num++ .'</td>' . '<td>'. $student->first_name . $student->last_name .'</td>' . '<td>'. $student->grade .'</td>' . '<td data-toggle="popover" title="Dates Present" data-content="'. $presentString .'">'.$attPresent.'</td>' . '<td data-toggle="popover" title="Dates Absent" data-content="'. $absentString .'">'. $attAbsent .'</td></tr>';
        }

        return response([$table, $num]);
    }



    //financial reports

    //for view
    public function financialView()
    {
       

       $user = Auth::user();

       $notifications = User::find($user->id)->unreadNotifications;

       return view('reports.financial')->with([
        'notifications' => $notifications,
       ]);
    }

    //for data
    public function getFinancial()
    {
        $date = date('Y-m');

        $invoicesPaid = Invoice::where('updated_at', 'LIKE', '%'. $date .'%')->where('status', '=', 'paid')->get();
        $invoicesUnpaid = Invoice::where('updated_at', 'LIKE', '%'. $date .'%')->where('status', '=', 'unpaid')->get();

        $invoicePaidTotal = 0;
        $invoiceUnPaidTotal = 0;
        $total = 0;

        foreach ($invoicesPaid as $paid) {
           $invoicePaidTotal += $paid->amount;
        }

        foreach ($invoicesUnpaid as $unpaid) {
            $invoiceUnPaidTotal += $unpaid->amount;
        }

        $numOfInvoices = count($invoicesPaid) + count($invoicesUnpaid);

        $total = $invoicePaidTotal + $invoiceUnPaidTotal;

        return Response([$invoicePaidTotal, $invoiceUnPaidTotal, $total, $date, $numOfInvoices]);
    }

    //for querying data
    public function queryFinancial(Request $request)
    {
        $date = date('Y-m');

    

        $invoicesPaid = Invoice::whereBetween('updated_at', [$request->from, $request->to])->where('status', '=', 'paid')->get();
        $invoicesUnpaid = Invoice::whereBetween('updated_at', [$request->from, $request->to])->where('status', '=', 'unpaid')->get();

        $invoicePaidTotal = 0;
        $invoiceUnPaidTotal = 0;
        $total = 0;

        foreach ($invoicesPaid as $paid) {
           $invoicePaidTotal += $paid->amount;
        }

        foreach ($invoicesUnpaid as $unpaid) {
            $invoiceUnPaidTotal += $unpaid->amount;
        }

        $numOfInvoices = count($invoicesPaid) + count($invoicesUnpaid);

        $total = $invoicePaidTotal + $invoiceUnPaidTotal;

        return Response([$invoicePaidTotal, $invoiceUnPaidTotal, $total,  $numOfInvoices]);
    }
}
