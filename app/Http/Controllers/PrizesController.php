<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;



class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::all();
        return view('prizes.index', ['prizes' => $prizes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {

        $newProbability = floatval($request->input('probability'));
        $currentProbability = floatval(Prize::sum('probability'));
        $remainingProbability = 100 - $currentProbability;

        if ($remainingProbability <= 0) {
            // Flash an error message
            return redirect()->back()->withInput()->withErrors(['probability' => 'No room for additional probabilities.']);
        }

        if ($newProbability > $remainingProbability) {
            // Flash an error message
            return redirect()->back()->withInput()->withErrors(['probability' => 'Adding this probability will exceed the total limit. You can add up to ' . $remainingProbability . '% more.']);
        }

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {

       

    // Find the prize by ID
    $prize = Prize::findOrFail($id);

    // Calculate the remaining probability allowed to update
    $remainingProbability = 100 - Prize::where('id', '!=', $id)->sum('probability');

    // Get the new probability from the request
    $newProbability = floatval($request->input('probability'));

    // Check if updating the probability exceeds the total limit
    if ($newProbability > $remainingProbability) {
        // Flash an error message
        return redirect()->back()->withInput()->withErrors(['probability' => 'Updating this probability will exceed the total limit. You can update up to ' . $remainingProbability . '% more.']);
    }
        
       // $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


 public function simulate(Request $request)
    {
$prizes = Prize::all();

// Configured probabilities for each product
$productProbabilities = [];

foreach ($prizes as $product) {
    $productName = $product['title'];
        
    $productProbabilities[$productName] = floatval($product['probability']);
}
//dd(array_sum($productProbabilities));

// Total number of prizes to distribute
$totalPrizes = floatval($request->input('number_of_prizes'));
//dd($totalPrizes);

// Ensure the sum of probabilities is 100
if (array_sum($productProbabilities) !== 100.0){
    // Handle error: Probabilities do not add up to 100
    die('Error: Probabilities should add up to 100.');
}

// Calculate the actual number of prizes for each product
$actualPrizes = [];
foreach ($productProbabilities as $product => $probability) {
    $actualPrizes[$product] = round(($probability / 100) * $totalPrizes);
}

// Adjust the total number of prizes to match the configured probabilities exactly
$remainingPrizes = $totalPrizes - array_sum($actualPrizes);
arsort($productProbabilities); // Sort probabilities in descending order

// Distribute the remaining prizes to the top products with the highest probabilities
foreach (array_keys($actualPrizes) as $product) {
    if ($remainingPrizes <= 0) {
        break;
    }

    $actualPrizes[$product]++;
    $remainingPrizes--;
}

// Output the actual distribution in percentage of probability
$percentageArray = [];
foreach ($actualPrizes as $product => $prizes) {
    $percentage = ($prizes / $totalPrizes) * 100;
    $percentageArray[$product] = $percentage;
}
       // dd($percentageArray);
       $newprizes = Prize::all();
       return view('prizes.index', ['percentageArray' => $percentageArray,'prizes' => $newprizes]);
    }

    public function reset()
    {
        // TODO : Write logic here
        return to_route('prizes.index');
    }
}
