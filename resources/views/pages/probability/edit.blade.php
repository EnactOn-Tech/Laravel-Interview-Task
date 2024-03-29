@extends('layouts.master.master')

@section('page_title') Edit Probability @endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Probability</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="{{ route('probability.update',$probability->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title" value="@if(old('title')){{old('title')}}@else{{$probability->title}}@endif" />
                            @if($errors->has('title'))
                                <span class="text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="percentage">Percentage</label>
                            <input type="number" name="percentage" class="form-control" id="percentage" min="0" max="100" step="0.1" placeholder="Enter Percentage" value="@if(old('percentage')){{old('percentage')}}@else{{$probability->percentage}}@endif" />
                            @if($errors->has('percentage'))
                                <span class="text-danger">{{ $errors->first('percentage') }}</span>
                            @endif
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('probability.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
