@extends('layout.admin')
@section('title', 'Employees')
@section('content')
    <div class="content-body">
        <div class="container-fluid">
            <div class="col-lg-12">
                <div class="form-head d-flex mb-3 align-items-start">
                    <div class="me-auto d-none d-lg-block">
                        <h2 class="text-primary font-w600 mb-0"></h2>
                        <input type="hidden" id="employeesId" value="">
                        <p class="mb-0"> </p>
                    </div>
                    <div class="dropdown custom-dropdown">
                        <div class="btn btn-sm btn-primary light d-flex align-items-center svg-btn"
                            data-bs-toggle="dropdown">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path
                                        d="M22.4281 2.856H21.8681V1.428C21.8681 0.56 21.2801 0 20.4401 0C19.6001 0 19.0121 0.56 19.0121 1.428V2.856H9.71606V1.428C9.71606 0.56 9.15606 0 8.28806 0C7.42006 0 6.86006 0.56 6.86006 1.428V2.856H5.57206C2.85606 2.856 0.560059 5.152 0.560059 7.868V23.016C0.560059 25.732 2.85606 28.028 5.57206 28.028H22.4281C25.1441 28.028 27.4401 25.732 27.4401 23.016V7.868C27.4401 5.152 25.1441 2.856 22.4281 2.856ZM5.57206 5.712H22.4281C23.5761 5.712 24.5841 6.72 24.5841 7.868V9.856H3.41606V7.868C3.41606 6.72 4.42406 5.712 5.57206 5.712ZM22.4281 25.144H5.57206C4.42406 25.144 3.41606 24.136 3.41606 22.988V12.712H24.5561V22.988C24.5841 24.136 23.5761 25.144 22.4281 25.144Z"
                                        fill="#2F4CDD"></path>
                                </g>
                            </svg>
                            <div class="text-start ms-3">
                                <span class="d-block fs-16" id="filter" id="btnHome">Filter Periode</span>
                                <small class="d-block fs-13 BillingCycle" id="content">{{ $payment->date }} -
                                    {{ $payment->date_end }}</small>
                            </div>
                            <i class="fa fa-angle-down scale5 ms-3"></i>
                        </div>
                        <div class="dropdown-menu dropdown-menu-right" id="filter">
                            @for ($i = 1; $i < 12; $i++)
                                <a class="dropdown-item empDetail"
                                    data-selectdate="2022-<?= $i ?>-10"data-selectEnddate="2022-<?= $i + 1 ?>-10"
                                    href="javascript:void(0)">2022-<?= $i ?>-10 -
                                    2022-<?= $i + 1 ?>-10</a>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="card">

                    @if (session()->has('message'))
                        <div class="alert alert-success" id="msg">
                            {{ session()->get('message') }}
                        </div>
                    @endif

                    <div class="card-header">
                        <h4 class="card-title">Employees Details</h4>
                        <a href="{{ route('admin.employees.create') }}" class="float-right btn btn-success">Add Employee</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table header-border table-responsive-sm" id="selectData">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Salary</th>
                                        <th>Advance Total</th>
                                        <th>Paybal</th>
                                        <th>Show</th>
                                        {{-- <th>Date</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td><a href="javascript:void(0)">{{ $no++ }}</a>
                                            </td>
                                            <td>{{ $employee->name ?? null }}</td>
                                            <td><span class="text-muted">{{ $employee->department }}</span>
                                            </td>
                                            <td> &#8377;{{ $employee->amount }}</td>
                                            <td> &#8377;{{ $employee->total_advance ?? 0 }}</td>

                                            <td> &#8377;{{ $employee->amount - ($employee->total_advance ?? 0) }}</td>

                                            <td>
                                                <a href="" id="advanceId" class="btn btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal{{ $employee->id }}" name="name"
                                                    data-bs-whatever="@mdo">Advance</a>
                                                <div class="modal fade" id="exampleModal{{ $employee->id }}" tabindex="-1"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Add
                                                                    Advance</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('admin.employee-advance.store') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="mb-3">
                                                                        <label for="name"
                                                                            class="col-form-label">Name</label>

                                                                        <input type="text" name="name" id="name"
                                                                            value="{{ ucwords($employee->name) }}"
                                                                            class="form-control" id="name">
                                                                        <input type="hidden" name="employee_id"
                                                                            id="employee_id" value="{{ $employee->id }}"
                                                                            class="form-control" id="employee_id">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="amount"
                                                                            class="col-form-label">Amount</label>
                                                                        <input type="text" name="advance_amount"
                                                                            class="form-control" id="advance_amount">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="amount"
                                                                            class="col-form-label">Type</label>
                                                                        <select class="form-select" name="type"
                                                                            aria-label="Default select example">
                                                                            <option selected> select menu</option>
                                                                            @foreach ($advance as $object)
                                                                                <option value="{{ $object->id }}">
                                                                                    {{ ucwords($object->name) }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- <td>{{ $employee->created_at->format('d M,Y') }}</td> --}}
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-primary light sharp"
                                                        data-bs-toggle="dropdown">
                                                        <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                            version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24"
                                                                    height="24" />
                                                                <circle fill="#000000" cx="5" cy="12"
                                                                    r="2" />
                                                                <circle fill="#000000" cx="12" cy="12"
                                                                    r="2" />
                                                                <circle fill="#000000" cx="19" cy="12"
                                                                    r="2" />
                                                            </g>
                                                        </svg>
                                                    </button>
                                                    <div class="dropdown-menu" id="actionId">
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.employees.edit', $employee->id) }}">Edit</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.employees.show', $employee->id) }}">View</a>
                                                        <a class="dropdown-item" deleteEmployee
                                                            onclick="deleteEmployee('{{ route('admin.employees.destroy', $employee->id) }}')">Delete</a>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/loading.css') }}">
@endpush

@push('js')
    <script src="{{ asset('backend/js/loading.js') }}"></script>
    <script>
        $(document).ready(function() {

            $(".empDetail").click(function() {

                $('#selectData tbody').html('');
                var date = $(this).text();
                var employee_id = $("#employeesId").val();
                var selectdate = $(this).attr('data-selectdate');
                var selectEnddate = $(this).attr('data-selectEnddate');

                $('.BillingCycle').text(date);
                loader('show', 'circle', 'Please Wait...');

                $.ajax({
                    url: '/admin/employeeajaxData/',
                    method: "GET",
                    data: {
                        'date': date,
                        'employee_id': employee_id,
                        'selectdate': selectdate,
                        'selectEnddate': selectEnddate
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        loader('hide');

                        if (data.employees.length > 0) {
                            $.each(data.employees, function(index, value) {
                                index++;
                                var paybal = value.amount - value.total_advance;

                                var userurl = 'employees/' + value.id + '/edit';

                                //var viewurl = 'employees/' + value.id;

                                var deleteurl = 'employees/' + value.id ;
                                var ajaxurl = 'viewAjax/' + value.id + '/' +
                                    selectdate + '/' + selectEnddate;


                                //console.log(value);
                                $('#selectData tbody').append('<tr><td>' + index +
                                    '</td><td>' + value.name + '</td><td>' + value
                                    .department + '</td><td>' + value.amount +
                                    '</td><td>' + value.total_advance +
                                    '</td><td>' + 0 + '</td><td>' +
                                    '<a class="btn btn-warning">Advance</a>' +
                                    '</td><td>' +
                                    '<div class="dropdown"> <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown"><svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24" /><circle fill="#000000" cx="5" cy="12" r="2" /><circle fill="#000000" cx="12" cy="12" r="2" /><circle fill="#000000" cx="19" cy="12" r="2" /></g></svg></button><div class="dropdown-menu" id="actionId"><a class="dropdown-item" href="' +
                                    userurl +
                                    '">Edit</a><a class="dropdown-item" href="' +
                                    ajaxurl +
                                    '">View</a><a class="dropdown-item" ' +
                                    deleteurl + '>Delete</a></div></div>' +
                                    '</td></tr>');
                            })
                        } else {
                            $('#selectData tbody').html('<tr><td colspan="7" class="text-center">Employees Data not found</td></tr>');
                        }
                    }
                });

            });

        });

        function loader(action, animation = null, text = null) {
            if (action == 'show') {
                $('body').loadingModal({
                    'animation': animation,
                    text: text,
                    'color': '#ffffff',
                    'backgroundColor': '#484646e0',
                    'opacity': '0.9'
                });
            } else {
                $('body').loadingModal('hide');
                $('body').loadingModal('destroy');
            }
        }
    </script>
@endpush
