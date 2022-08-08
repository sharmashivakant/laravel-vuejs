@extends('layout.admin')
@section('title', 'Employees Advance')
@section('content')
@foreach ($employees as $employee)    

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
@endforeach

@endsection

@push('js')
    
@endpush