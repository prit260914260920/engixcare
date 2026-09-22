@extends('layouts.app')

@section('content')
  <main class="auth-page">
    <section class="section py-5" style="padding-top: 120px;">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="card p-4 shadow-sm">
              <h3 class="mb-4">Create your account</h3>
              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirm Password</label>
                  <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary-engix w-100">Register</button>
              </form>
              <p class="text-center mt-3">Already registered? <a href="{{ route('login') }}">Login</a></p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
