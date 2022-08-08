<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AdvanceType;
use App\Models\EmployeeAdvance;
use App\Models\PaymentSetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {       
        
        //   $employees       =Employee::all();
        $paymentSettings = PaymentSetting::all()->toArray();           
       
        $from_date = $paymentSettings[0]['date'];
        $to_date   = $paymentSettings[0]['date_end'];   

        //$totalAdvance =  DB::select('select  employee_advances.employee_id, sum(employee_advances.advance_amount) as total_advance from `employees` left join `employees` on `employee_advances`.`empoyee_id` =  where `employee_advances` = "'.'employee_id'.'" and `employee_advances`.`created_at` between "'.$from_date.'" and "'.$to_date.'"');
        //return $totalAdvance;
        $employees  = Employee::select(\DB::raw('employees.*, SUM(advance_amount ) as total_advance '))
                                    ->leftJoin('employee_advances', 'employee_advances.employee_id', '=', 'employees.id')
                                    ->groupBy("employees.id")
                                    //->whereBetween('employees.created_at',array($from_date, $to_date )) 
                                    ->get();       
       
       //return $employees;

        $advance = AdvanceType::all();
        $payment = PaymentSetting::select('*')
                                    ->whereMonth('date', Carbon::now()->month) 
                                    ->first('id');
        
        
        return view('pages.backend.employees.index',compact('employees','advance','payment'))->with('no',1);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.backend.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request;
        $validated =  $request->validate([
            'name'       => 'required',
            'department' => 'required',
            'amount'     => 'required',

        ]);

        $employee = new Employee;

        $employee->name        =$request->name;
        $employee->department  =$request->department;
        $employee->amount      =$request->amount;     
        
        $employee->save();

        return redirect()->route('admin.employees.index')->with('message', 'Employee Add Successfuly!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $employees  = Employee::find($id);        
                     
        $paymentSettings = PaymentSetting::all()->toArray(); 
       
        $startDate = $paymentSettings[0]['date'];
        $endDate = $paymentSettings[0]['date_end'];        
        
        $totalAdvance =  DB::select('select  employee_advances.employee_id, sum(employee_advances.advance_amount) as total_advance from `advance_types` inner join `employee_advances` on `employee_advances`.`type_id` = `advance_types`.`id` where `employee_id` = "'.$id.'" and `employee_advances`.`created_at` between "'.$startDate.'" and "'.$endDate.'"');
        
        
          $advances = AdvanceType::join('employee_advances', function ($join) {
                                    $join->on('employee_advances.type_id', '=', 'advance_types.id');   
                                    })->where('employee_id', $id)->whereBetween('employee_advances.created_at',array($startDate, $endDate))->get();
 
        $payment = PaymentSetting::select('*')
                                    ->whereMonth('date', Carbon::now()->month) 
                                    ->first('id',$id);
            
                                
        return view('pages.backend.employees.show',compact('totalAdvance', 'employees','advances','payment'));
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Employee::findorfail($id);
        return view('pages.backend.employees.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated =  $request->validate([
            'name'       => 'required',
            'department' => 'required',
            'amount'     => 'required',

        ]);

        $employee = Employee::find($id);

        $employee->name        =$request->name;
        $employee->department  =$request->department;
        $employee->amount      =$request->amount;

        $employee->update();

        return redirect()->route('admin.employees.index')->with('message', 'Employee Updated Successfuly!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::findorfail($id);

        $employee->delete();
        Session()->flash('message', 'Delete Employee Successfully!');
        return ['status' => 'true'];
    }
}
