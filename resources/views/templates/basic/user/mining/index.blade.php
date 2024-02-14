@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center gy-4">
            <div class="col-lg-4 col-md-6">
                <div class="card-widget section--bg2 text-center bg_img" style="background-image: url(' {{ asset($activeTemplateTrue . 'images/elements/card-bg.png') }} ');">
                    <span class="caption text-white mb-3">@lang('Available Balance')</span>
                    <h3 class="d-number text-white">{{ $general->cur_sym }}{{ showAmount($user->balance,10) }}</h3>
                    <a href="{{ route('user.mining.transferToStack') }}" class="btn btn--light py-1 px-3 mt-2 btn-block w-100 action_btn">
                        <i class="la la-exchange-alt"></i>
                        @lang('Staking')</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card-widget section--bg2 text-center bg_img" style="background-image: url(' {{ asset($activeTemplateTrue . 'images/elements/card-bg.png') }} ');">
                    <span class="caption text-white mb-3">@lang('Invested Amount')</span>
                    <h3 class="d-number text-white">{{ $general->cur_sym }}{{ $invested ? showAmount($invested->mount,10) : '0.0000000000' }}</h3>
                    <a href="{{ route('user.mining.transferToBalance') }}" class="btn btn--light py-1 px-3 mt-2 btn-block w-100 action_btn">
                        <i class="la la-exchange-alt"></i>
                        @lang('Withdraw staking')</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card-widget section--bg2 text-center bg_img" style="background-image: url(' {{ asset($activeTemplateTrue . 'images/elements/card-bg.png') }} ');">
                    <span class="caption text-white mb-3">@lang('Earned Amount')</span>
                    <h3 class="d-number text-white">{{ $general->cur_sym }}{{ $earned ? showAmount($earned,10) : '0.0000000000' }}</h3>
                    <a href="{{ route('user.mining.transferEarned') }}" class="btn btn--light py-1 px-3 mt-2 btn-block w-100">
                        <i class="la la-exchange-alt"></i>
                        @lang('Withdraw interest')</a>
                </div>
            </div>

        </div>

        <div class="row gy-4 mt-3">
            <div class="col-lg-12">
                <h3 class="mb-3">@lang('Mining History')
                </h3>
                <div class="custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md">
                            <table class="table custom--table mb-0">
                                <thead>
                                    <tr>
                                        <th>@lang('#')</th>
                                        <th>@lang('Earned')</th>
                                        <th>@lang('Date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @foreach($history as $log)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $general->cur_sym }}{{ $log->earned }}</td>
                                            <td>{{ showDateTime($log->created_at, 'd M, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $history->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- create and edit update modal --}}
    <div class="modal fade" id="action_modal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Transfer Amount')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" id="action_form">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mount">@lang('Amount to transfer')</label>
                                    <input type="text" name="mount" id="mount" class="form-control" placeholder="@lang('0.01')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark py-1 px-3" data-bs-dismiss="modal" type="button">@lang('Cancel')</button>
                        <button class="btn btn--primary py-1 px-3" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .modal-backdrop.show {
    opacity: .5;
    z-index: -1;
}
.btn[type=submit], .h-45 {
    height: 34px;
}
</style>
@endpush
@push('script')
    <script>
        $(document).ready(function(){
           $('.action_btn').click(function(e){
              e.preventDefault();
              const route = $(this).attr('href');
              $('#action_form').attr('action', route);
              $('#action_modal').modal('show');
           }); 
        });
    </script>
@endpush
