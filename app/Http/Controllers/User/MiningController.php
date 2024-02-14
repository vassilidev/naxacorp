<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\MiningConfig;
use App\Models\MiningHistory;
use App\Models\MiningStack;
use Illuminate\Http\Request;

class MiningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view($this->activeTemplate.'user.mining.index', [
            'packages' => MiningConfig::all(),
            'user' => auth()->user(),
            'pageTitle' => 'Mining',
            'invested' => MiningStack::where('user_id', auth()->user()->id)->first(),
            'earned' => MiningHistory::where('user_id', auth()->user()->id)->where('paid', false)->sum('earned'),
            'history' => MiningHistory::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferToStack(Request $request)
    {
        $valideData = $request->validate([
            'mount' => 'required|numeric|min:0.1',
        ]);

        $user = auth()->user();

        if ($request->mount > $user->balance) {
            $notify[] = ['error', 'Influinced Balance'];

            return back()->withNotify($notify);
        }

        $user->balance -= $request->mount;
        $user->save();

        $stacks = MiningStack::where('user_id', $user->id)->first();

        if ($stacks === null) {
            MiningStack::create([
                'user_id' => $user->id,
                'mount' => $request->mount,
            ]);
        } else {
            $stacks->mount += $request->mount;
            $stacks->save();
        }

        $notify[] = ['success', 'Balance Updated'];

        return back()->withNotify($notify);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferToBalance(Request $request)
    {
        $valideData = $request->validate([
            'mount' => 'required|numeric|min:0.1',
        ]);

        $user = auth()->user();
        $stacks = MiningStack::where('user_id', $user->id)->first();

        if ($stacks === null || $stacks->mount < $request->mount) {
            $notify[] = ['error', 'Invalid Amount'];

            return back()->withNotify($notify);
        }

        $user->balance += $request->mount;
        $user->save();

        $stacks->mount -= $request->mount;
        $stacks->save();

        $notify[] = ['success', 'Balance Updated'];

        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MiningStack  $miningStack
     * @return \Illuminate\Http\Response
     */
    public function transferEarned()
    {

        $user = auth()->user();
        $earned = MiningHistory::where('user_id', $user->id)
            ->where('paid', false)
            ->get();

        if ($earned === null) {
            $notify[] = ['error', 'Invalid Amount'];

            return back()->withNotify($notify);
        }

        foreach ($earned as $coin) {
            $coin->update(['paid' => true]);
        }

        $user->balance += $earned->sum('earned');
        $user->save();

        $notify[] = ['success', 'Balance Updated'];

        return back()->withNotify($notify);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MiningStack $miningStack)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MiningStack $miningStack)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MiningStack $miningStack)
    {
        //
    }
}
