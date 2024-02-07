@extends('layouts.master')
@section('title', 'Create Expenses')
@section('content')
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-12">
                        <h4 class="page-title">Payments Management</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('expenses/create') }}">Expenses</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Payments Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="pd-20 bg-white border-radius-4 box-shadow">
                <div class="container">
                    <h1 class="mb-4">Expense Payments</h1>
                    <div class="card">
                        <h5 class="card-header">Payments Details</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Job Order:</span>
                                    <span>{{ $expense->job_order }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Payment To:</span>
                                    <span>{{ $expense->payment_to }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Date:</span>
                                    <span>{{ $expense->date }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Type:</span>
                                    <span>{{ $expense->type }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Amount:</span>
                                    <span>{{ $expense->amount }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Payment Mode:</span>
                                    <span>{{ $expense->payment_mode }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
