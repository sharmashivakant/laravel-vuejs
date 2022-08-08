<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AdvanceType;
class ModalController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        $advance   = AdvanceType::all();
        
        return view('pages.backend.modals.index',compact('employees','advance'));
    }
}
