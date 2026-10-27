@extends('layouts.app')
@section('title', 'Add Permissions')
@section('main-content')



    <section class="section leadsection container">
        <div class=" form-card">
            <div class="row">
                <div class="col-lg-10">
                    <h5 class="mt-2">Add Permissions</h5>
                </div>
                <div class="col-lg-2">
                    <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                </div>
            </div>
        </div>
    </section>

    @php
        $roles = App\Models\Role::find($roleId);
    @endphp

    <div class="container-fluid mt-5 add-permission-container">
        <div class="card position-relative">
            <form action="/allocate-permission" method="POST" class="allocate-permission-form">
                @csrf
                <input type="hidden" name="roleId" value="{{ $roleId }}">
                <div class="submit-div">
                    <button class="btn btn-primary permission-submit">Submit</button>
                </div>
                <div class="card-body">
                    <h4 class="mt-2 text-start mb-5">{{ $roles->name }} - Permissions</h4>
                    <div class="row">
                        @php
                            $permissionGroups = App\Models\PermissionGroup::all();
                        @endphp

                        @foreach ($permissionGroups as $data)
                            <div class="col-lg-3 mb-4">
                                <div class="permissions-box">
                                    <div class="title-box mb-3">
                                        <i class="permission-check"></i>
                                        <h5>{{ $data->name }}</h5>
                                    </div>
                                    <div class="row">
                                        @php
                                            $permissions = App\Models\Permission::where('permission_group_id', $data->id)->get();
                                        @endphp
                                        @foreach ($permissions as $dataPermission)
                                            <div class="col-12 mb-1">
                                                <input type="checkbox" name="permissionsId[]" value="{{ $dataPermission->id }}"
                                                    id="{{ $dataPermission->name }}" {{ in_array($dataPermission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label for="{{ $dataPermission->name }}"
                                                    class="form-label">{{ $dataPermission->permission_name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </form>
        </div>
    </div>



@endsection