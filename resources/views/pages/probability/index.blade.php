@extends('layouts.master.master')

@section('page_title') Probabilities @endsection

@section('stylesheets')
    <!-- Chart.js style -->
    <link rel="stylesheet" href="{{ asset('/css/Chart.min.css') }}" />
@endsection

@section('scripts')
    <!-- Chart.js js -->
    <script src="{{ asset('/js/Chart.min.js') }}"></script>
    <script>
        // Function to generate random colors
        function randomColor() {
            return '#' + Math.random().toString(16).slice(2, 8);
        }

        var donutOptions = {
            maintainAspectRatio : false,
            responsive : true,
        }

        // Probability Settings
        var probabilitySettingsDonutChartCanvas = $('#probabilitySettingsDonutChart').get(0).getContext('2d')
        var probabilitySettingsLabels = @json($probabilitySettingsLabels);
        var probabilitySettingsData = @json($probabilitySettingsData);

        // Generate random background colors based on data length
        var backgroundColors = [];
        for (var i = 0; i < probabilitySettingsLabels.length; i++) {
            backgroundColors.push(randomColor());
        }

        var probabilitySettingsDonutChartData = {
            labels: probabilitySettingsLabels,
            datasets: [
                {
                    data: probabilitySettingsData,
                    backgroundColor : backgroundColors,
                }
            ]
        }

        new Chart(probabilitySettingsDonutChartCanvas, {
            type: 'doughnut',
            data: probabilitySettingsDonutChartData,
            options: donutOptions
        })

        // Actual Rewards
        var actualRewardsDonutChartCanvas = $('#actualDonutChart').get(0).getContext('2d')
        var probabilityRewardsData = @json($probabilityRewardsData);
        var actualRewardsDonutChartData = {
            labels: probabilitySettingsLabels,
            datasets: [
                {
                    data: probabilityRewardsData,
                    backgroundColor : backgroundColors,
                }
            ]
        }

        new Chart(actualRewardsDonutChartCanvas, {
            type: 'doughnut',
            data: actualRewardsDonutChartData,
            options: donutOptions
        })

        if(probabilitySettingsData.length==0){
            document.getElementById('probabilitySettingsDonutChartDiv').style.display = 'none';
        }

        if(probabilityRewardsData.length==0){
            document.getElementById('actualRewardsChartDiv').style.display = 'none';
        }
    </script>
@endsection

@section('content')
    @if($totalPercentage<100)
        <div class="alert alert-info">Sum of all prizes probability must be 100%. Currently its {{$totalPercentage}}% You have yet to add
            {{$leftPercentage}}% to the prize</div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-11"></div>
                        <div class="col-md-1 text-right">
                            <a href="{{ route('probability.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($probabilities as $key => $p)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $p->title }}</td>
                                <td>{{ $p->percentage }}</td>
                                <td>{{ @$p->reward->awarded }}</td>
                                <td class="d-flex">
                                    <form method="post" action="{{ route('probability.destroy',$p->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('probability.edit',$p->id) }}" class="btn btn-primary btn-sm rounded-pill mr-2">Edit</a>
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No Records</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    @include('layouts.master.components.pagination',['paginator' => $probabilities])
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">Simulate</div>
                    </div>
                </div>
                <!-- /.card-header -->
                <form method="post" action="{{ route('simulation.process') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="simulations">Number of Prizes</label>
                            <input type="number" name="simulations" class="form-control" id="simulations" placeholder="Enter Simulations" value="@if(old('simulations')){{old('simulations')}}@endif" />
                            @if($errors->has('simulations'))
                                <span class="text-danger">{{ $errors->first('simulations') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simulate</button>
                        <a href="{{ route('reset.simulation') }}" class="btn btn-default float-right">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">Simulate Report</div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6" id="probabilitySettingsDonutChartDiv">
                            <h3>Probability Settings</h3>
                            <canvas id="probabilitySettingsDonutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <div class="col-md-6" id="actualRewardsChartDiv">
                            <h3>Actual Rewards</h3>
                            <canvas id="actualDonutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
