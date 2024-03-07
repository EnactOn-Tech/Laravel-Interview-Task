<?php

use App\Models\Prize;

$current_probability = floatval(Prize::sum('probability'));
$remaining_probability = 100 - $current_probability;

?>
{{-- TODO: add Message logic here --}}
{{-- Display error message if there's not enough remaining probability --}}
@if(Session::has('error'))
    <div class="alert alert-danger">
        {{ Session::get('error') }}
    </div>
@endif

{{-- Display success message if a new prize is added successfully --}}
@if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
@endif

{{-- Display remaining and total probability information --}}
<div>
    <p>Remaining Probability: {{ $remaining_probability }}%</p>
    <p>Total Probability Utilized: {{ $current_probability }}%</p>
</div>