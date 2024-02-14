@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container pt-80 pb-80">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="alert alert-warning" role="alert">
                            <strong> <i class="la la-info-circle"></i> @lang('You need to complete your profile to get access to your dashboard')</strong>
                        </div>
                        <form method="POST" action="{{ route('user.data.submit') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="container parent">
                                        <p class="text-center">@lang('Account Type')</p>
                                      <div class="row">
                                        <div class='col text-center'>
                                          <input type="radio" name="account_type" id="img1" class="d-none imgbgchk" value="personal" checked>
                                          <label for="img1">
                                            Personal
                                            <div class="tick_container">
                                              <div class="tick"><i class="fa fa-check"></i></div>
                                            </div>
                                          </label>
                                        </div>
                                        <div class='col text-center'>
                                          <input type="radio" name="account_type" id="img2" class="d-none imgbgchk" value="business">
                                          <label for="img2">
                                            Business
                                            <div class="tick_container">
                                              <div class="tick"><i class="fa fa-check"></i></div>
                                            </div>
                                          </label>
                                        </div>
                                            
                                      </div>
                                    </div>
                                </div>
                                <div class="personal_account_fields col-sm-12">
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label class="form-label required">@lang('First Name')</label>
                                            <input type="text" class="form--control" name="firstname" value="{{ old('firstname') }}" />
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label required">@lang('Last Name')</label>
                                            <input type="text" class="form--control" name="lastname" value="{{ old('lastname') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="business_account_fields col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label required">@lang('Business company name')</label>
                                        <input type="text" class="form--control" name="company" value="{{ old('company') }}" />
                                    </div>
                                </div>
                                
                                <div class="form-group col-12">
                                    <label class="form-label required">@lang('Image')</label>
                                    <input type="file" class="form--control" name="image" id="imageUpload" value="{{ old('firstname') }}" accept=".png, .jpg, .jpeg" required>
                                    <div class="proifle-image-preview d-none"><img src="" alt="profile-image"></div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Address')</label>
                                    <input type="text" class="form--control" name="address" value="{{ old('address') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('State')</label>
                                    <input type="text" class="form--control" name="state" value="{{ old('state') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Zip Code')</label>
                                    <input type="text" class="form--control" name="zip" value="{{ old('zip') }}">
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('City')</label>
                                    <input type="text" class="form--control" name="city" value="{{ old('city') }}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-md btn--base w-100">
                                @lang('Submit')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .proifle-image-preview {
            margin-top: 15px;
        }

        .proifle-image-preview img {
            width: 200px;
            height: 160px;
        }

.col label {
    overflow: hidden;
    position: relative;
    background: #2f3c52;
    padding: 12px 50px;
    border-radius: 2px;
    color: #fff;
    cursor: pointer;
}
.imgbgchk:checked + label > .tick_container {
  opacity: 1;
}
/*         aNIMATION */

.tick_container {
  transition: 0.5s ease;
  opacity: 0;
  position: absolute;
  top: 50%;
  left: 20%;
  transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  cursor: pointer;
  text-align: center;
}
.tick {
    background-color: #fff3cd;
    color: #2f3c52;
    font-size: 14px;
    padding: 5px 5px;
    height: 30px;
    width: 32px;
    border-radius: 100%;
}
    </style>
@endpush

@push('script')
    <script>
        $("#imageUpload").on('change', function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.proifle-image-preview').removeClass('d-none');
                    $('.proifle-image-preview img').attr('src', e.target.result)
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        $(document).ready(function(){
           var accountType = $('[name="account_type"]').val();
           if(accountType === 'personal'){
               $('.business_account_fields').hide();
           }else{
               $('.personal_account_fields').hide();
           }
           
           $('[name="account_type"]').change(function(){
               $('.business_account_fields').toggle('slow');
               $('.personal_account_fields').toggle('slow');
           })
        });
    </script>
@endpush
