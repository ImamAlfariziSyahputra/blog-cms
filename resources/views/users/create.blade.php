@extends('layouts.dashboard')

@section('title')
{{ trans('users.title.create') }}
@endsection

@section('breadcrumbs')
{{ Breadcrumbs::render('addUser') }}
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <!-- name -->
                    <div class="form-group">
                        <label for="input_user_name" class="font-weight-bold">
                            {{ trans('users.label.name') }}
                        </label>
                        <input 
                            id="input_user_name" 
                            value="" name="name" 
                            type="text" 
                            class="form-control" 
                            placeholder="{{ trans('users.form.input.name.placeholder') }}" />
                        <!-- error message -->
                    </div>
                    <!-- role -->
                    <div class="form-group">
                        <label for="select_user_role" class="font-weight-bold">
                            {{ trans('users.label.role') }}
                        </label>
                        <select 
                            id="select_user_role" 
                            name="role" 
                            data-placeholder="{{ trans('users.form.select.role.placeholder') }}" 
                            class="custom-select w-100"
                        >
                            <option value="" selected="selected">Role</option>
                        </select>
                        <!-- error message -->
                    </div>
                    <!-- email -->
                    <div class="form-group">
                        <label for="input_user_email" class="font-weight-bold">
                            {{ trans('users.label.email') }}
                        </label>
                        <input 
                            id="input_user_email" 
                            value="" name="email" 
                            type="email" 
                            class="form-control" 
                            placeholder="{{ trans('users.form.input.email.placeholder') }}"
                            autocomplete="email" />
                        <!-- error message -->
                    </div>
                    <!-- password -->
                    <div class="form-group">
                        <label for="input_user_password" class="font-weight-bold">
                            {{ trans('users.form.input.password.label') }}
                        </label>
                        <input 
                            id="input_user_password" 
                            name="password" 
                            type="password" 
                            class="form-control" 
                            placeholder="{{ trans('users.form.input.password.placeholder') }}"
                            autocomplete="new-password" />
                        <!-- error message -->
                    </div>
                    <!-- password_confirmation -->
                    <div class="form-group">
                        <label for="input_user_password_confirmation" class="font-weight-bold">
                            {{ trans('users.form.input.password_confirmation.label') }}
                        </label>
                        <input 
                            id="input_user_password_confirmation" 
                            name="password_confirmation" 
                            type="password"
                            class="form-control" 
                            placeholder="{{ trans('users.form.input.password_confirmation.placeholder') }}" 
                            autocomplete="new-password" />
                        <!-- error message -->
                    </div>
                    <div class="float-right">
                        <a 
                            class="btn btn-warning text-white px-4 mx-2" 
                            href="{{ route('users.index') }}"
                        >
                            {{ trans('users.button.cancel.value') }}
                        </a>
                        <button type="submit" class="btn btn-primary float-right px-4">
                            {{ trans('users.button.save.value') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('cssExternal')
{{-- Select2 --}}
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
@endpush

@push('jsExternal')
{{-- Select2 --}}
<script src="{{asset('vendor/select2/js/select2.min.js') }}"></script>
<script src="{{asset('vendor/select2/js/i18n/'. app()->getLocale() .'.js') }}"></script>
@endpush

@push('jsInternal')
<script>
    // Role Select
    $('#select_user_role').select2({
            theme: 'bootstrap4',
            language: "{{ app()->getLocale() }}",
            allowClear: true,
            ajax: {
                url: "{{ route('users.select') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
</script>
@endpush