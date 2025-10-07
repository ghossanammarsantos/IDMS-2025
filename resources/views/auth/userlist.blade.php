@extends('admin.layouts.app', [
    'activePage' => 'Master',
])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">User List</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($users as $user)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $user->name }}</td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>{{ $user->role->role_name }}</td>
                                                            <td>
                                                                <!-- Contoh Tombol Aksi -->
                                                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
