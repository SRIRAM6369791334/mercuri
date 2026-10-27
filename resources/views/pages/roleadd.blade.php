@extends('layouts.app')
@section('title', 'Lead Entry')
@section('main-content')

    <section class="section leadsection">
        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 mb-2">
                            <h5 class="mt-2 ">Roles</h5>
                        </div>
                    </div>
                </div>
            </div>
            <form class="myForm" method="POST" action="{{ route('roles.storerole') }}">
                <div class="row mb-4">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group mb-2">
                            <label class="form-label">Role Name <span class="mandatory">*</span></label>
                            <input type="text" class="form-control role_name" name="name" placeholder="Role Name" required>
                        </div>
                    </div>


                </div>
                <button type="submit" class="btn btn-primary">Add Role</button>
            </form>
        </div>

    </section>

@endsection