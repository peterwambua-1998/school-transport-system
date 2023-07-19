<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Mail\MonthlyInvoiceMail;
use App\Mail\WeeklyInvoice;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invoices = Invoice::all();

        $user = Auth::user();

        $table = '';

        /*
        $month = date('Y-m');
        
        
        

        $invoiceCurrentMonth = Invoice::where('created_at', 'LIKE', '%'.$month.'%')->get();
        */


        $notifications = User::find($user->id)->unreadNotifications;

        $total = 0;
        $totalBalance = 0;

        $num = 1;

        foreach ($invoices as $invoice) {

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                $amtReceipt += $receipt->amount;
                $total += $receipt->amount;
                
            }
            $invAmt = $invoice->amount;
           
            
            $balance = $invAmt - $amtReceipt;

            $totalBalance += $balance;

            $parent = User::where('id', '=', $invoice->parent_id)->first();
            $student = Student::where('id', '=', $invoice->student_id)->first();

            
                
            

            $date=date_create($invoice->created_at);

            $fDate = date_format($date,"Y/m/d");

           
            if ($student && $parent) {
                $table .= '<tr><td class="name">'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name   .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if ($parent && !$student) {
                $table .= '<tr><td class="name">'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name  .'</td>' . '<td>'.  'deleted student' .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if (!$parent && $student) {
                $table .= '<tr><td class="name">'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';

            } else if (!$parent && !$student) {
                $table .= '<tr><td class="name">'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. 'deleted' .'</td>' . '<td>'. $fDate .'</td></tr>';

            }
        }

        
        
        return Response(['table' => $table, 'total' => $total, 'totalBalance' => $totalBalance]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $students = Student::all();
        

        foreach ($students as $student) {
            $amt = $student->vehicle->route->price;

            $parent = $student->parent;

            $invoice = new Invoice();
            $invoice->parent_id = $parent->id;
            $invoice->student_id = $student->id;
            
            $invoice->inv_num = 'inv_' . rand();
            $invoice->amount = $amt;
            $invoice->status = 'unpaid';
            $invoice->save();
        
            Mail::to($parent->email)->send(new WeeklyInvoice($invoice, $amt));
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function paid()
    {
        

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;


        return view('invoices.paid')->with([
    
            
            'notifications' => $notifications
        ]);
    }


    public function paidData()
    {
        $invoices = Invoice::where('status', '=','paid')->get();
        $total = 0;


        if (count($invoices) < 1) {
            return response(['table'=>'', 'total' => $total]);
        }

        $table = '';

        $num = 1;
        
        foreach ($invoices as $invoice) {

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                $amtReceipt += $receipt->amount;
            }
            $invAmt = $invoice->amount;

            $total +=  $invoice->amount;

            $balance = $invAmt - $amtReceipt;

            $parent = User::where('id', '=', $invoice->parent_id)->first();
            $student = Student::where('id', '=', $invoice->student_id)->first();

            $date=date_create($invoice->created_at);

            $fDate = date_format($date,"Y/m/d");

            if ($student && $parent) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name   .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if ($parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name  .'</td>' . '<td>'.  'deleted student' .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if (!$parent && $student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';

            } else if (!$parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. 'deleted' .'</td>' . '<td>'. $fDate .'</td></tr>';

            }
            //$table .= '<tr><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name ?? 'deleted' .'</td>' . '<td>'. $student->first_name . ' '. $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
        }

        
        
        return Response(['table'=>$table,'total' => $total]);
    }


    public function paidDataQuery(Request $request)
    {
        $invoices = Invoice::whereBetween('created_at', [$request->from, $request->to])->where('status', '=', 'paid')->get();

        $total = 0;

        if (count($invoices) < 1) {
            return response(['table'=>'', 'total' => $total]);
        }

        $table = '';

        $num = 1;
        
        
        foreach ($invoices as $invoice) {

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                $amtReceipt += $receipt->amount;
            }
            $invAmt = $invoice->amount;
            $total += $invoice->amount;

            $balance = $invAmt - $amtReceipt;

            $parent = User::where('id', '=', $invoice->parent_id)->first();
            $student = Student::where('id', '=', $invoice->student_id)->first();

            $date=date_create($invoice->created_at);

            $fDate = date_format($date,"Y/m/d");

            if ($student && $parent) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name   .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if ($parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name  .'</td>' . '<td>'.  'deleted student' .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if (!$parent && $student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';

            } else if (!$parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. 'deleted' .'</td>' . '<td>'. $fDate .'</td></tr>';

            }
            //$table .= '<tr><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name ?? 'deleted' .'</td>' . '<td>'. $student->first_name . ' '. $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
        }

        
        
        return Response(['table'=>$table,'total' => $total]);
    }


    public function unpaidData()
    {
        $total = 0;
        $invoices = Invoice::where('status', '=','unpaid')->get();

        if (count($invoices) < 1) {
            return response(['table'=>'', 'total' => $total]);
        }

        $table = '';

        $num = 1;
        
        foreach ($invoices as $invoice) {

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                $amtReceipt += $receipt->amount;
            }
            $invAmt = $invoice->amount;

            $total += $invoice->amount;

            $balance = $invAmt - $amtReceipt;

            $parent = User::where('id', '=', $invoice->parent_id)->first();
            $student = Student::where('id', '=', $invoice->student_id)->first();

            $date=date_create($invoice->created_at);

            $fDate = date_format($date,"Y/m/d");

            if ($student && $parent) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name   .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if ($parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name  .'</td>' . '<td>'.  'deleted student' .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if (!$parent && $student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';

            } else if (!$parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. 'deleted' .'</td>' . '<td>'. $fDate .'</td></tr>';

            }
            //$table .= '<tr><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name ?? 'deleted' .'</td>' . '<td>'. $student->first_name . ' '. $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
        }

        
        
        return Response(['table'=>$table,'total' => $total]);
    }


    public function unpaidDataQuery(Request $request)
    {
        $total = 0;
        $invoices = Invoice::whereBetween('created_at', [$request->from, $request->to])->where('status', '=', 'unpaid')->get();

        if (count($invoices) < 1) {
            return response(['table'=>'', 'total' => $total]);
        }

        $table = '';

       $num = 1;
        
        foreach ($invoices as $invoice) {

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                $amtReceipt += $receipt->amount;
            }
            $invAmt = $invoice->amount;

            $total += $invoice->amount;

            $balance = $invAmt - $amtReceipt;

            $parent = User::where('id', '=', $invoice->parent_id)->first();
            $student = Student::where('id', '=', $invoice->student_id)->first();

            $date=date_create($invoice->created_at);

            $fDate = date_format($date,"Y/m/d");
            if ($student && $parent) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name   .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if ($parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name  .'</td>' . '<td>'.  'deleted student' .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if (!$parent && $student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';

            } else if (!$parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. 'deleted' .'</td>' . '<td>'. $fDate .'</td></tr>';

            }
            //$table .= '<tr><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name .'</td>' . '<td>'. $student->first_name . ' '. $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
        }

        
        
        return Response(['table'=>$table,'total' => $total]);
        
    }

    public function unPaid()
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('invoices.unpaid')->with([
            'notifications' => $notifications 
        ]);


        
    }


    public function invoiceAll() {

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;
        return view('invoices.index')->with([
            'notifications' => $notifications
        ]);
    }


    public function search(Request $request)
    {

        $total = 0;
        $invoices = Invoice::whereBetween('created_at', [$request->from, $request->to])->get();

        if (count($invoices) < 1) {
            return response(['table' => '', 'total' => $total]);
        }

        $table = '';

        $num = 1;
        
        
        foreach ($invoices as $invoice) {

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                $amtReceipt += $receipt->amount;
            }
            $invAmt = $invoice->amount;

            $total += $invoice->amount;

            $balance = $invAmt - $amtReceipt;

            $parent = User::where('id', '=', $invoice->parent_id)->first();
            $student = Student::where('id', '=', $invoice->student_id)->first();

            $date=date_create($invoice->created_at);

            $fDate = date_format($date,"Y/m/d");
            if ($student && $parent) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name   .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if ($parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name  .'</td>' . '<td>'.  'deleted student' .'</td>' . '<td>'. $fDate .'</td></tr>';
            } else if (!$parent && $student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. $student->first_name . ' ' . $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';

            } else if (!$parent && !$student) {
                $table .= '<tr><td>'. $num++ .'</td><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. 'deleted'  .'</td>' . '<td>'. 'deleted' .'</td>' . '<td>'. $fDate .'</td></tr>';

            }
            //$table .= '<tr><td>' . $invoice->inv_num .'</td>' . '<td>'. $amtReceipt .'</td>' . '<td>'.$balance.'</td>' . '<td>'. $invAmt .'</td>' . '<td>'. $invoice->status .'</td>' . '<td>'. $parent->name .'</td>' . '<td>'. $student->first_name . ' '. $student->last_name .'</td>' . '<td>'. $fDate .'</td></tr>';
        }

        
        
        return response(['table' => $table, 'total' => $total]);

      
    }
}
