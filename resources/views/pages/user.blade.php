@extends('layouts.app')
@section('main-content')
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <section class="section leadsection container">
        <div class=" form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Users</h5>
                        </div>
                        <div class="col-lg-4"></div>
                        <div class="col-lg-2">
                            @can('users-create')
                                <button type="button" class="btn btn-primary  waves-effect waves-light" data-bs-toggle="modal"
                                    data-bs-target=".bs-example-modal-center2" data-id="" data-name="">
                                    <i class="mdi mdi-plus"></i> Add User
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container bg-white form-card mt-4">
        <div class="row">
            <div class="col-lg-12">
                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="text-align: center;">S.No</th>
                            <th style="text-align: center;">Name</th>
                            <th style="text-align: center;">E-Mail</th>
                            <th style="text-align: center;">Mobile</th>
                            <th style="text-align: center;">Role</th>
                            @canany(['users-edit', 'users-delete'])
                                <th style="text-align: center;">Action</th>
                            @endcanany
                            <th style="text-align: center;">Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            $sd = App\Models\User::all();
                        @endphp
                        @foreach ($sd as $s)
                            <tr style="text-align: center;">
                                <td>{{ $i++ }}</td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->mobilenumber }}</td>
                                <td>{{ $s->role }}</td>
                                @canany(['users-edit', 'users-delete'])
                                    <td>
                                        <div class="buttons_td d-flex justify-content-center align-items-center gap-1">
                                            @can('users-edit')
                                                <button type="button" class="btn btn-bull_yellow edit-btn waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center"
                                                    data-id="{{ $s->id }}" data-userstatus="{{ $s->userstatus }}"
                                                    data-name="{{ $s->name }}" data-mobilenumber="{{ $s->mobilenumber }}"
                                                    data-email="{{ $s->email }}" data-role="{{ $s->role }}" data-tl_id="{{ $s->tl_id }}"
                                                    title="Edit User">
                                                    <i class="mdi mdi-lead-pencil"></i>
                                                </button>
                                            @endcan
                                            @can('users-delete')
                                                @if(auth()->id() !== $s->id)
                                                    <button type="button" data-url="{{ route('users.destroy', $s->id) }}"
                                                        class="btn btn-bull_red common-delete-btn waves-effect waves-light"
                                                        title="Delete User">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany

                                <td>
                                    <div class="buttons_td">
                                        <button type="button" class="btn btn-bull_org  waves-effect waves-light pass_edit"
                                            data-bs-toggle="modal" data-bs-target=".bs-example-modal-center1"
                                            data-id="{{ $s->id }}">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade bs-example-modal-center2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="myForm" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label class="form-label">Name <span class="mandatory">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Name" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Mobile <span class="mandatory">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+91-</span>
                                <input type="text" class="form-control" id="mobileInput" placeholder="Enter mobile number"
                                    required>
                                <input type="hidden" name="mobilenumber" id="fullMobileInput">
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">E-Mail <span class="mandatory">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="E-Mail" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Designation <span class="mandatory">*</span></label>
                            <select name="role" class="form-control role-select" required>
                                <option value="" disabled selected>Choose...</option>
                                @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">User Status <span class="mandatory">*</span></label>
                            <select name="userstatus" class="form-control" required>
                                <option value="" disabled selected>Choose...</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>

                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Team Leader</label>
                            <select name="tl_id" class="form-control">
                                <option value="" selected>No Team Leader</option>
                                @php
                                    $allUsers = \App\Models\User::where('userstatus', 'Active')->get();
                                @endphp
                                @foreach ($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Password <span class="mandatory">*</span></label>
                            <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control password" placeholder="Enter password"
                                    aria-label="Password" aria-describedby="password-addon" name="password" required>
                                <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i
                                        class="mdi mdi-eye-outline"></i></button>
                            </div>
                            {{-- <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control password" name="password"
                                    placeholder="Enter password" required>
                            </div> --}}
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Confirm Password <span class="mandatory">*</span></label>
                            <input type="password" class="form-control confirmpassword" name="password_confirmation"
                                required>
                            <span class="confpassword-error error-msg"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.update') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <input type="hidden" class="id" name="id">
                            <label for="" class="form-label"> Name <span class="mandatory">*</span></label>
                            <input type="text" class="form-control name" placeholder="Name" name="name" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Mobile <span class="mandatory">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+91-</span>
                                <input type="text" class="form-control mobilenumber-visible" id="edit-mobile-visible"
                                    placeholder="Mobile Number" required>
                                <input type="hidden" name="mobilenumber" class="mobilenumber-hidden"
                                    id="edit-mobile-hidden">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="input4" class="form-label">E-Mail <span class="mandatory">*</span></label>
                            <input type="email" class="form-control email" name="email" placeholder="E-Mail" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="input7" class="form-label">Designation <span class="mandatory">*</span></label>
                            <select name="type" class="form-control type role-select" required>
                                <option disabled selected="">Choose...</option>
                                @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">User Status <span class="mandatory">*</span></label>
                            <select name="userstatus" class="form-control userstatus" required>
                                <option disabled selected>Choose...</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>

                            </select>
                        </div>
                         <div class="form-group mb-2">
                            <label class="form-label">Team Leader</label>
                            <select name="tl_id" class="form-control tl_id">
                                <option value="" selected>No Team Leader</option>
                                @php
                                    $allUsers = \App\Models\User::where('userstatus', 'Active')->get();
                                @endphp
                                @foreach ($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade bs-example-modal-center1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Password Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="/editpassword">
                    @csrf
                    <input type="hidden" class="id" name="id">
                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="" class="form-label">Password <span class="mandatory">*</span></label>
                            <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control passworded" placeholder="Enter password"
                                    aria-label="Password" name="passworded" required>
                                <button class="btn btn-light shadow-none ms-0 toggle-password" type="button">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="" class="form-label">Confirm Password <span class="mandatory">*</span></label>
                            <input type="password" class="form-control confirmpassworded" name="confirmpass" required>
                            <span class="confpassworded-error error-msg"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('scripts')
    <script src="/assets/js/pages/pass-addon.init.js"></script>
    <script>
        $('.country').on('change', function () {
            var country = $(this).val();

            $('.state').empty();
            $('.state').append(`<option value="" disabled selected>Processing...</option>`);
            $.ajax({
                type: 'GET',
                url: '/GetState/' + country,
                success: function (response) {
                    $('.state').empty();
                    $('.state').append(
                        `<option value="" disabled selected>Select State</option>`
                    );
                    response.forEach(element => {
                        $('.state').append(
                            `<option value="${element['id']}">${element['name']}</option>`
                        );
                    });
                }
            })
        });

        $('.state').on('change', function () {
            var state = $(this).val();

            $('.district').empty();
            $('.district').append(`<option value="" disabled selected>Processing...</option>`);
            $.ajax({
                type: 'GET',
                url: '/Getdistrict/' + state,
                success: function (response) {
                    $('.district').empty();
                    $('.district').append(
                        `<option value="" disabled selected>Select District</option>`
                    );
                    response.forEach(element => {
                        $('.district').append(
                            `<option value="${element['id']}">${element['district']}</option>`
                        );
                    });
                }
            })
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.confirmpassword').on('keyup', function () {
                const password = $('.password').val();
                const confirmPassword = $('.confirmpassword').val();
                const errorMsg = $('.confpassword-error');
                const submitButton = $('.add-employee-btn');

                if (!password) {
                    errorMsg.text('Enter the password first').css('color', 'red');
                    submitButton.attr('type', 'button'); // Change to button to prevent form submission
                } else if (password !== confirmPassword) {
                    errorMsg.text('Confirm password is wrong').css('color', 'red');
                    submitButton.attr('type', 'button'); // Change to button to prevent form submission
                } else {
                    errorMsg.text('Password is correct').css('color', 'green');
                    submitButton.attr('type', 'submit'); // Change to submit to allow form submission
                }
            });

            $('.password').on('keyup', function () {
                $('.confpassword-error').text('');
                $('.add-employee-btn').attr('type', 'button'); // Reset to button on typing in password
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.confirmpassworded').on('keyup', function () {
                const password = $('.passworded').val();
                const confirmPassworded = $('.confirmpassworded').val();
                const errorMsg = $('.confpassworded-error');
                const submitButton = $('.add-employee-btn');

                if (!password) {
                    errorMsg.text('Enter the password first').css('color', 'red');
                    submitButton.attr('type', 'button'); // Change to button to prevent form submission
                } else if (password !== confirmPassworded) {
                    errorMsg.text('Confirm password is wrong').css('color', 'red');
                    submitButton.attr('type', 'button'); // Change to button to prevent form submission
                } else {
                    errorMsg.text('Password is correct').css('color', 'green');
                    submitButton.attr('type', 'submit'); // Change to submit to allow form submission
                }
            });

            $('.passworded').on('keyup', function () {
                $('.confpassworded-error').text('');
                $('.add-employee-btn').attr('type', 'button'); // Reset to button on typing in password
            });
        });
    </script>

    <script>
        $(document).on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var mobile = $(this).data('mobilenumber'); // format: +91-8900908900
            var type = $(this).data('role');
            var userstatus = $(this).data('userstatus');
            var tl_id = $(this).data('tl_id');

            $('.id').val(id);
            $('.name').val(name);
            $('.email').val(email);
            $('.type').val(type);
            $('.userstatus').val(userstatus);
             $('.tl_id').val(tl_id);

            // Extract only digits after +91-
            let numberOnly = mobile.replace('+91-', '');
            $('#edit-mobile-visible').val(numberOnly);
            $('#edit-mobile-hidden').val(mobile); // Initial full number
        });

        // Update hidden input live as user types
        $(document).on('input', '#edit-mobile-visible', function () {
            let digits = $(this).val().replace(/\D/g, '').slice(0, 10);
            $(this).val(digits);
            $('#edit-mobile-hidden').val('+91-' + digits);
        });
    </script>


    <script>
        $(document).on('click', '.pass_edit', function () {
            var id = $(this).data('id');

            $('.id').val(id);
        })
    </script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {
                const input = this.closest('.auth-pass-inputgroup').querySelector('.passworded');
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('mdi-eye-outline');
                    icon.classList.add('mdi-eye-off-outline');
                } else {
                    input.type = 'password';
                    icon.classList.remove('mdi-eye-off-outline');
                    icon.classList.add('mdi-eye-outline');
                }
            });
        });
    </script>

<script>
        $(document).ready(function() {
            // Handle role change in Add User modal
            $('.role-select').on('change', function() {
                var selectedRole = $(this).val();
                var modal = $(this).closest('.modal');
                var tlDropdown = modal.find('select[name="tl_id"]');

                if (selectedRole) {
                    $.ajax({
                        url: '{{ route("users.getByRole") }}',
                        type: 'GET',
                        data: { role: selectedRole },
                        success: function(response) {
                            tlDropdown.empty();
                            tlDropdown.append('<option value="">No Team Leader</option>');

                            if (response.users && response.users.length > 0) {
                                response.users.forEach(function(user) {
                                    tlDropdown.append('<option value="' + user.id + '">' + user.name + ' (' + user.role + ')</option>');
                                });
                            }
                        },
                        error: function() {
                            console.error('Error fetching users by role');
                        }
                    });
                } else {
                    // Reset to default if no role selected
                    tlDropdown.empty();
                    tlDropdown.append('<option value="" selected>No Team Leader</option>');
                }
            });

            // Handle role change in Edit User modal
            $('.type').on('change', function() {
                var selectedRole = $(this).val();
                var modal = $(this).closest('.modal');
                var tlDropdown = modal.find('select[name="tl_id"]');

                if (selectedRole) {
                    $.ajax({
                        url: '{{ route("users.getByRole") }}',
                        type: 'GET',
                        data: { role: selectedRole },
                        success: function(response) {
                            tlDropdown.empty();
                            tlDropdown.append('<option value="">No Team Leader</option>');

                            if (response.users && response.users.length > 0) {
                                response.users.forEach(function(user) {
                                    tlDropdown.append('<option value="' + user.id + '">' + user.name + ' (' + user.role + ')</option>');
                                });
                            }
                        },
                        error: function() {
                            console.error('Error fetching users by role');
                        }
                    });
                } else {
                    // Reset to default if no role selected
                    tlDropdown.empty();
                    tlDropdown.append('<option value="" selected>No Team Leader</option>');
                }
            });
        });
    </script>

@endsection
