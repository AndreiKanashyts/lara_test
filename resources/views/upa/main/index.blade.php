@extends('upa.layouts.main')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Главная</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Главная</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    @canany(['view-admin','view-controller','view-journal','view'], auth()->user())
    <section class="content">
        <div><first-component :authoperator='@json($auth_operator)'></first-component></div>
    </section>
    @endcan
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
