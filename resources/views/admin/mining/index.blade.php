@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Daily')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @forelse($packages as $package)
                                    <tr>

                                        <td>
                                            <span class="fw-bold">{{ $i++ }}</span>
                                        </td>

                                        <td>
                                            {{ $package->rates }}
                                        </td>

                                        <td>{{ $package->timers }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.mining.edit', $package->id) }}"
                                                class="btn btn--info btn-sm edit_btn me-2">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.mining.destroy', $package->id) }}" method="post">
                                                @csrf @method('delete')
                                                <button type="submit" class="btn btn--danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            </div>
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
                @if ($packages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($packages) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>

    </div>

    {{-- create and edit update modal --}}
    <div class="modal fade" id="action_modal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Mining Managment')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" id="action_form">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="package_rates">@lang('Manage Mining package')</label>
                                    <input type="text" name="rates" id="package_rates" class="form-control" placeholder="@lang('0.01')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="package_timers">@lang('Package Earning Timers')</label>
                                    <select type="text" name="timers" id="package_timers" class="form-control" >
                                        <option value="daily">@lang('Daily')</option>
                                        <option value="weekly">@lang('Weekly')</option>
                                        <option value="monthly">@lang('Monthly')</option>
                                        <option value="yearly">@lang('Yearly')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn-sm" data-bs-dismiss="modal" type="button">@lang('Cancel')</button>
                        <button class="btn btn--primary btn-sm" type="submit">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.mining.store') }}" class="btn btn--primary create_btn">@lang('Create New')</a>
@endpush
@push('script')
    <script>
        $(document).ready(function(){
            $('.create_btn').click(function(e){
                e.preventDefault();
                const action = $(this).attr('href');
                $('#action_form').attr('action', action);
                $('#action_modal').modal('show');
            });
            $('.edit_btn').click(function(e){
                e.preventDefault();
                const route = $(this).attr('href');
                $.ajax({
                    url:route,
                    type:'get',
                    dataType:'json',
                    success:function(res){
                        $('#action_form input[name="rates"]').val(res.rates);
                        $('#action_form').attr('action', `/manageme/mining/${res.id}/update`);
                        $('#action_modal').modal('show');
                    }
                })
            })
        })
    </script>
@endpush