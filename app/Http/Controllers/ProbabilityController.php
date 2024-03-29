<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Probability;
use App\Models\ProbabilityRewards;

class ProbabilityController extends Controller
{
    protected $maxPercentage = 100;
    protected $numParticipants = 1000;

    public function index()
    {
        $totalPercentage = Probability::get()->sum('percentage');
        $leftPercentage = ($this->maxPercentage - $totalPercentage);
        $probabilities = Probability::with('reward')->paginate(10);

        $probabilitySettings = Probability::orderBy('id')->get(['title','percentage']);
        $probabilitySettingsLabels = $probabilitySettings->pluck('title')->toArray();
        $probabilitySettingsData = $probabilitySettings->pluck('percentage')->toArray();

        $probabilityRewardsData = ProbabilityRewards::orderBy('probability_id')->get('awarded_percentage')->pluck('awarded_percentage')->toArray();

        return view('pages.probability.index',compact('probabilities','totalPercentage','leftPercentage','probabilitySettingsLabels','probabilitySettingsData','probabilityRewardsData'));
    }

    public function create()
    {
        return view('pages.probability.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:190',
            'percentage' => 'required|numeric|max:100',
        ]);

        $totalPercentage = Probability::get()->sum('percentage');
        $afterPercentage = ($request->percentage + $totalPercentage);

        if($afterPercentage > $this->maxPercentage){
            $notification = ['message'=>"The probability field must not be greater than 100%",'type'=>'danger'];
            return redirect()->back()->with($notification);
        }

        $probability = $request->only('title','percentage');

        DB::beginTransaction();
        try {

            Probability::create($probability);

            DB::commit();
            $notification = ['message'=>"probability create successfully",'type'=>'success'];
            return redirect()->route('probability.index')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getLine());
            Log::error($e->getMessage());
        }
    }

    public function show(string $id)
    {
    }

    public function edit(string $id)
    {
        $probability = Probability::find($id);
        return view('pages.probability.edit',compact('probability'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|max:190',
            'percentage' => 'required|numeric|max:100',
        ]);

        $totalPercentage = Probability::whereNotIn('id',[$id])->get()->sum('percentage');
        $afterPercentage = ($request->percentage + $totalPercentage);

        if($afterPercentage > $this->maxPercentage){
            $notification = ['message'=>"The probability field must not be greater than 100%",'type'=>'danger'];
            return redirect()->back()->with($notification);
        }

        $probability = $request->only('title','percentage');

        DB::beginTransaction();
        try {

            Probability::where('id',$id)->update($probability);

            DB::commit();
            $notification = ['message'=>"probability updated successfully",'type'=>'success'];
            return redirect()->route('probability.index')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getLine());
            Log::error($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $probability = Probability::find($id);
        DB::beginTransaction();
        try {
            if($probability->delete()){
                ProbabilityRewards::where('probability_id',$id)->delete();
                DB::commit();
                $notification = ['message'=>"probability delete successfully",'type'=>'success'];
                return redirect()->route('probability.index')->with($notification);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getLine());
            Log::error($e->getMessage());
        }
    }

    public function simulationProcess(Request $request)
    {
        $request->validate([
            'simulations' => 'required|numeric|min:1|max:100',
        ]);

        $totalSimulations = $request->simulations;
        $probabilities = Probability::get();

        if($probabilities->count() == 0){
            $notification = ['message'=>"probabilities not added",'type'=>'success'];
            return redirect()->route('probability.index')->with($notification);
        } else {
            ProbabilityRewards::truncate();

            for ($i = 0; $i < $totalSimulations; $i++) {
                $rand = rand(1, $this->maxPercentage);

                foreach ($probabilities as $p) {
                    if ($rand <= $p->percentage) {
                        $awarded = (($this->numParticipants * $p->percentage)/100);
                        ProbabilityRewards::updateOrCreate(['probability_id' => $p->id],[
                            'awarded_percentage' => $rand,
                            'awarded' => $awarded
                        ]);
                    }
                }
            }

            $notification = ['message'=>"probabilities simulate successfully",'type'=>'success'];
            return redirect()->route('probability.index')->with($notification);
        }
    }

    public function resetSimulation()
    {
        ProbabilityRewards::truncate();
        $notification = ['message'=>"Simulate reset successfully",'type'=>'success'];
        return redirect()->route('probability.index')->with($notification);
    }
}
