<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Dailysale;
use App\Models\Expen;
use App\Models\Excate;
use App\Models\Order;
use App\Models\Item;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\PaymentSetting;
use App\Models\AdvanceType;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class AdminController extends Controller
{ 
    
    public function dashboard(Request $request)
    {
        $currentMonth=date('m');// Current month
        $maxDays=date('t');// Total number of days in month
        $currentDayOfMonth=date('j');// Current date in month        
        
        $product = Product::where('status',1)->count();
        $daily = Dailysale::where('date', 'like','%'.date('Y').'%')->sum('amount'); 
        $currentMonthIncome = Dailysale::where('date', 'like','%'.date('Y-'.$currentMonth).'%')->sum('amount');
        $expen = Expen::where('created_at', 'like', '%' .date('Y').'%')->sum('price'); 
        $currentMonthExpens = Expen::where('created_at', 'like', '%' .date('Y-'.$currentMonth).'%')->sum('price');       
        $object=$daily-$expen; 
        $getMonthIncome = array();
        $getMonthExpense = array();        
        for ($x = 1; $x <= $currentMonth; $x++) {
            if($x<10){                
                $getMonthIncome[] = Dailysale::whereMonth('created_at', '0'.$x)
                            ->whereYear('created_at', date('Y'))
                            ->sum('amount');
                $getMonthExpense[] = Expen::whereMonth('created_at', '0'.$x)
                            ->whereYear('created_at', date('Y'))
                            ->sum('price');
            } else {
                $getMonthIncome[] = Dailysale::whereMonth('created_at', $x)
                            ->whereYear('created_at', date('Y'))
                            ->sum('amount');
                $getMonthExpense[] = Expen::whereMonth('created_at', $x)
                            ->whereYear('created_at', date('Y'))
                            ->sum('price');
            }                     
        }        

        $weeks = array(); 
        $Totalweeks = 1;       
        for ($wk=0; $wk<$maxDays; $wk++) {
            if(($wk % 7) == 1){                
                $weeks[] = $Totalweeks++;
            }            
        }
       
        $numberOfDays = array(); 
        $daysIncome = array(); 
        $daysExpen = array();    
            for ($days = 1; $days <= $currentDayOfMonth; $days++) {
                if($days<10){
                    $numberOfDays[] = '0'.$days;
                    $daysIncome[] = Dailysale::where('date', 'like', '%' . date('Y-'.$currentMonth.'-0'.$days) . '%')->sum('amount');
                    $daysExpen[] = Expen::where('created_at', 'like', '%' . date('Y-'.$currentMonth.'-0'.$days) . '%')->sum('price');               
                }else {
                    $numberOfDays[] = $days; 
                    $daysIncome[]= Dailysale::where('date', 'like', '%' . date('Y-'.$currentMonth.'-'.$days) . '%')->sum('amount');
                    $daysExpen[] = Expen::where('created_at', 'like', '%' . date('Y-'.$currentMonth.'-'.$days) . '%')->sum('price');                                        
                }
            }        
        
        $monthlyTotalIncome = implode(',', $getMonthIncome);
        $getMonthExpense = implode(',', $getMonthExpense); 
        $currentNumberOfDays = implode(',', $numberOfDays);
        $EverydayIncome = implode(',', $daysIncome);
        $EverydaydaysExpen = implode(',', $daysExpen);
        $TotalsWeeksMonth = implode(',', $weeks);       
        
        return view('pages.backend.dashboard',
                compact('product',
                'daily',
                'currentMonthIncome',
                'expen',
                'currentMonthExpens',
                'object',
                'monthlyTotalIncome',
                'getMonthExpense',
                'currentNumberOfDays',
                'EverydayIncome',
                'EverydaydaysExpen',
                'TotalsWeeksMonth'
            ));
    }

    public function filterBy(Request $request)
    {
        $month = date('m');
        $year = date('Y');        
        if($request->filterBy=='month'):            
            $monthlDailySaleTotal = Dailysale::whereYear('created_at', $year)->sum('amount');
            $expenMonthly = Expen::whereYear('created_at', $year)->sum('price');
            return response()->json([
                            'success'=>$monthlDailySaleTotal , 
                            'expenMonthly'=>$expenMonthly ,
                            'monthlyData'=>'monthly'
                        ]);    
        elseif($request->filterBy=='week'):
            $DailySaleTotal= Dailysale::where('date', 'like', '%' . date('Y-'.$month) . '%')->sum('amount');
            $expenWeek = Expen::where('created_at', 'like', '%' . date('Y-'.$month) . '%')->sum('price');
            return response()->json([
                            'success'=>$DailySaleTotal,
                            'expenWeek'=>$expenWeek,
                            'weeklyData'=>'weekly'
                        ]);
        elseif($request->filterBy=='daily'):            
            $DailySaleTotal= Dailysale::where('date', 'like', '%' . date('Y-'.$month) . '%')->sum('amount');
            $expenDailyTotal = Expen::where('created_at', 'like', '%' . date('Y-'.$month) . '%')->sum('price'); 
                       
            return response()->json([
                                    'success'=>$DailySaleTotal,
                                    'expenDailyTotal'=>$expenDailyTotal, 
                                    'dailyData'=>'daily'
                                ]);
        endif; 
    }

    public function signin()
    {
        //return 'helloo';
        if (Auth::check()):
            $role = auth()->user()->role;
            if($role== 1):
                return redirect('admin/dashboard');
            else :
                return redirect('/');
            endif;
            else :
            return view('pages.backend.login');
        endif;
        
    }

    public function adminLogin(Request $request)
    {       
        //return $request;
        $validator = Validator::make($request->all(),[
            'email'    => 'required',
            'password' => 'required'         
        ]);

        if ($validator->fails()) {
            return redirect('admin')
            ->withErrors($validator)
            ->withInput();
        } 
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) 
        { 
            return redirect('admin/dashboard');
        } else {
            return redirect('/admin')->withErrors('invalid detail. Please try again.');
        }
    }

    public function changeStatus(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->status = $request->status;
        $product->save();
  
        return response()->json(['success'=>'Status change successfully.']);
    }

    public function changeCategorystatus(Request $request)
    {
        $category = Category::find($request->category_id);
        $category->status = $request->status;
        $category->save();
  
        return response()->json(['success'=>'Status change successfully.']);
    }

    public function changeExcatestatus(Request $request)
    {
        $excate = Excate::find($request->excate_id);
        $excate->status = $request->status;
        $excate->save();

        return response()->json(['success'=>'Status change successfully.']);

    }

    public function retriveNetProfite($show)
    {
        $expen = Expen::select(
                        DB::raw("year(created_at) as monht"),
                        DB::raw("SUM(click) as total_click"),
                        DB::raw("SUM(viewer) as total_viewer")) 
                        ->orderBy(DB::raw("YEAR(created_at)"))
                        ->groupBy(DB::raw("YEAR(created_at)"))
                        ->get();

            $result[] = ['Month','price','date'];
            foreach ($expen as $key => $value) {
            $result[++$key] = [$value->month, (int)$value->created_at, (int)$value->date];
            }

            return view('pages.backend.dashboard')
                ->with('expen',json_encode($result));
    
        //return view('dashboard', compact('data'));

    }

    public function changeOrderstatus(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success'=>'Status change successfully.']);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/admin');
    }

    public function OrderDetail()
    {
        $items = Item::all();
        return view('pages.backend.orders.order-details',compact('items'));
    }

    public  function ajaxData(Request $request){      
        
       
        $date   = explode(" - ", $request->date);

        $startDate =  date('Y-m-d', strtotime($date[0]));
        $endDate = date('Y-m-d', strtotime($date[1]));
     
       //return $endDate;
        $empActuallySalary = Employee::where('id', $request->employee_id)->first();    

       //return $empActuallySalary;
        $employee_id   = $request->employee_id;
        
        $totalAdvance =  DB::select('select  employee_advances.employee_id, sum(employee_advances.advance_amount) as total_advance from `advance_types` inner join `employee_advances` on `employee_advances`.`type_id` = `advance_types`.`id` where `employee_id` ="'.$employee_id.'"  and `employee_advances`.`created_at` between "'.$startDate.'" and "'.$endDate.'"');
        
        $advances = AdvanceType::join('employee_advances', function ($join) {
                                $join->on('employee_advances.type_id', '=', 'advance_types.id');   
                                })
                                ->where('employee_id', $employee_id)
                                ->whereBetween('employee_advances.created_at',array($startDate, $endDate))                                
                                ->get();
        
       
        $nameKey = 'name';
        //return $advances;
        $object_encoded2 = json_encode($advances);
        $object_decoded2 = json_decode($object_encoded2, true );

        $object_encoded = json_encode($totalAdvance);
        $object_decoded = json_decode($object_encoded, true );

        $totalAdvance   = $object_decoded[0]['total_advance'];
        $act_amout=$empActuallySalary->amount;
        $nam = array();
        $names ='';
        $dates ='';
       
        foreach($object_decoded2 as $key=> $vals):
            $names = $vals['name'];
            $datess = $vals['created_at']; 
            $dates = date('d-M-Y', strtotime($datess));
        endforeach;    
         
           
        return response()->json(['total_advance' => $totalAdvance, 'AdvanceType' => $advances, 'empActuallySalary' =>$act_amout, 'names' =>$names, 'dates' =>$dates]);  
            
    }

    public function employeeajaxData(Request $request)
    {       
        

        $selectdate = $request->selectdate;
        
        $selectEnddate = $request->selectEnddate;       

        $employees  = Employee::select(\DB::raw('employees.*, SUM(advance_amount ) as total_advance '))
                                ->leftJoin('employee_advances', 'employee_advances.employee_id', '=', 'employees.id')
                                ->groupBy("employees.name")
                                ->whereBetween('employee_advances.created_at',array($selectdate, $selectEnddate )) 
                                ->get();
        
        return response()->json(['employees' =>$employees,'selectdate' =>$selectdate,'selectEnddate' =>$selectEnddate]);
    }

    public function viewAjax($id,$selectdate, $selectEnddate)
    {
        $employees  = Employee::find($id);        
                     
        // $paymentSettings = PaymentSetting::all()->toArray(); 
       
        // $selectdate = $paymentSettings[0]['date'];
        // $selectEnddate = $paymentSettings[0]['date_end'];        
        
        $totalAdvance =  DB::select('select  employee_advances.employee_id, sum(employee_advances.advance_amount) as total_advance from `advance_types` inner join `employee_advances` on `employee_advances`.`type_id` = `advance_types`.`id` where `employee_id` = "'.$id.'" and `employee_advances`.`created_at` between "'.$selectdate.'" and "'.$selectEnddate.'"');
        
        
          $advances = AdvanceType::join('employee_advances', function ($join) {
                                    $join->on('employee_advances.type_id', '=', 'advance_types.id');   
                                    })->where('employee_id', $id)->whereBetween('employee_advances.created_at',array($selectdate, $selectEnddate))->get();
 
        $payment = PaymentSetting::select('*')
                                    ->whereMonth('date', Carbon::now()->month) 
                                    ->first('id',$id);
            
                                
        return view('pages.backend.employees.view',compact('totalAdvance', 'employees','advances','payment','selectdate','selectEnddate'));
    }
    
}
