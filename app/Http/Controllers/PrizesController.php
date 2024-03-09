<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


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
        $this->validateProbabilitySum($request->input('probability'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $this->validateProbabilitySum($request->input('probability'));

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return redirect()->route('prizes.index');
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
        $prize = Prize::findOrFail($id);
        $this->validateProbabilitySum($request->input('probability'), $id);
    
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();
    
        return redirect()->route('prizes.index');
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

        $numSimulations = $request->number_of_prizes ?? 10;

        $prizes = Prize::all();

        for ($i = 0; $i < $numSimulations; $i++) {
            $selectedPrize = $this->selectPrize($prizes);
            $selectedPrize->increment('winner_count');
        }

        return redirect()->route('prizes.index');
    }

   
    public function reset()
    {
        Prize::query()->update(['winner_count' => 0]);

        return redirect()->route('prizes.index');
    }

  
    private function selectPrize($prizes)
    {
        $probabilities = $prizes->pluck('probability')->toArray();
        $selectedPrizeIndex = array_rand($probabilities);

        return $prizes[$selectedPrizeIndex];
    }

    private function validateProbabilitySum($newProbability, $prizeId = null)
    {
        $currentSum = Prize::where('id', '!=', $prizeId)->sum('probability');
        $remainingProbability = 100 - $currentSum;
        $newSum = $currentSum + floatval($newProbability);
    
        if ($newSum > 100) {
            throw ValidationException::withMessages(['probability' => "The sum of probabilities cannot exceed 100%. Remaining probability: {$remainingProbability}%."]);
        }
    }
}
