@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                @if(request()->routeIs('admin.mining.active'))
                <div class="row gy-4">
                     
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalHolding,10) }}" title="Total Holding" style="2" bg="white" color="primary" icon="la la-coins" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalMount,10) }}" title="Total staking" style="2" bg="white" color="primary" icon="la la-coins" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalBalance, 10) }}" title="Total Naxa supply" style="2" bg="white" color="primary" icon="la la-coins" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-8">
                        <form action='' >
                            <div class="d-flex">
                                <input type='search' name='s' class='form-control' placeholder='search by Username'/>
                                <button type='submit' class='btn btn--primary btn-sm'>
                                    <i class='la la-search'></i>
                                </button>
                                @if(request('s'))
                                <a href="{{ route('admin.mining.active') }}">Reset</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @forelse($history as $log)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $i++ }}</span>
                                        </td>

                                        <td>
                                            {{ $log->user->username }}
                                        </td>

                                        <td>{{ $log->mount }}</td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($history->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($history) }}
                    </div>
                @endif
                @else
                <div class="card-body p-0">
                    <div class="row gy-4">
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalWithdrawed, 10) }}" title="Total Withdrawed" style="2" bg="white" color="primary" icon="la la-users" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalHolding, 10) }}" title="Total Holding" style="2" bg="white" color="primary" icon="la la-coins" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalMount, 10) }}" title="Total staking" style="2" bg="white" color="primary" icon="la la-coins" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <x-widget value="{{ showAmount($totalBalance, 10) }}" title="Total Naxa supply" style="2" bg="white" color="primary" icon="la la-coins" link="#" icon_style="solid" overlay_icon=0 />
                    </div>
                    <div class="col-xxl-3 col-sm-8">
                        <form action='' >
                            <div class="d-flex">
                                <input type='search' name='s' class='form-control' placeholder='search by Username'/>
                                <button type='submit' class='btn btn--primary btn-sm'>
                                    <i class='la la-search'></i>
                                </button>
                                @if(request('s'))
                                <a href="{{ route('admin.mining.history') }}">Reset</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Earned')</th>
                                    <th>@lang('Paid')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @forelse($history as $log)
                                    <tr>

                                        <td>
                                            <span class="fw-bold">{{ $i++ }}</span>
                                        </td>

                                        <td>
                                            {{ $log->user->username }}
                                        </td>

                                        <td>{{ $log->earned }}</td>
                                        <td>{{ $log->paid ? 'Withdrawed' : 'Holding' }}</td>
                                        <td>
                                            {{ showDateTime($log->created_at, 'd M, Y h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($history->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($history) }}
                    </div>
                @endif
                
                @endif
            </div><!-- card end -->
        </div>

    </div>

@endsection