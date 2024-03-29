<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MiningConfig;
use App\Models\MiningStack;
use App\Models\MiningHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageMiningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('admin.mining.index',[
            'packages' => MiningConfig::paginate(),
            'pageTitle' => 'Manage Mining packages'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rates' => 'required|numeric|min:0.001|max:100',
            'timers' => 'required|string|in:daily,monthly,weekly,yearly'
        ]);
        MiningConfig::create($validatedData);

        $notify[] = ['success', 'New package added successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MiningConfig  $miningConfig
     * @return \Illuminate\Http\Response
     */
    public function show(MiningConfig $miningConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MiningConfig  $miningConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(MiningConfig $miningConfig, $id)
    {
        return response()->json($miningConfig->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MiningConfig  $miningConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MiningConfig  $miningConfig ,$id)
    {
        $query = $miningConfig->find($id);
        
        $validatedData = $request->validate([
            'rates' => 'required|numeric|min:0.001|max:100',
            'timers' => 'required|string|in:daily,monthly,weekly,yearly'
        ]);
        
        $query->update($validatedData);

        $notify[] = ['success', 'New package added successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MiningConfig  $miningConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(MiningConfig $miningConfig, $id)
    {
        $miningConfig->destroy($id);
        
        $notify[] = ['success', 'the package deleted successfully'];
        
        return back()->withNotify($notify);
    }
    
    
    public function activeMining(){
        $history = MiningStack::where('mount', '!=', null);
        
        if(request('s') !== null){
            $history->whereHas('user', function($q){
               return $q->where('username','like' ,"%".request('s')."%"); 
            });
        }
        
        $totalMount = MiningStack::where('mount', '!=', null)->sum('mount');
        $totalUserBalance = User::sum('balance');
        $totalWithdrawed = MiningHistory::where('paid', true)->sum('earned');
        $totalHolding = MiningHistory::where('paid', false)->sum('earned');
        $totalBalance = $totalMount + $totalUserBalance + $totalHolding;
        
        return view('admin.mining.log', [
            'history' => $history->paginate(),
            'totalWithdrawed' => $totalWithdrawed,
            'totalHolding' => $totalHolding,
            'totalMount' => $totalMount,
            'totalBalance' => $totalBalance, 
            'pageTitle' => 'Active Mining Investment',
        ]);
    }
    
    public function earningLog(){
        
        $history = MiningHistory::orderBy('created_at', 'desc');
        
        if(request('s') !== null){
            $history->whereHas('user', function($q){
               return $q->where('username','like' ,"%".request('s')."%"); 
            });
        }
        $totalMount = MiningStack::where('mount', '!=', null)->sum('mount');
        $totalUserBalance = User::sum('balance');
        $totalWithdrawed = MiningHistory::where('paid', true)->sum('earned');
        $totalHolding = MiningHistory::where('paid', false)->sum('earned');
        $totalBalance = $totalMount + $totalUserBalance + $totalHolding;
        return view('admin.mining.log', [
            'history' => $history->paginate(),
            'totalWithdrawed' => $totalWithdrawed,
            'totalHolding' => $totalHolding,
            'totalMount' => $totalMount,
            'totalBalance' => $totalBalance,
            'pageTitle' => 'Mining Earnings History',
        ]);
    }
}
