@extends('layouts.app')

@section('content')
  <main class="auth-page">
    <section class="section py-5" style="padding-top: 120px;">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="card p-4 shadow-sm">
              <h3 class="mb-4">Login to Engix Care</h3>
              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4 form-check">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary-engix w-100">Login</button>
              </form>
              <p class="text-center mt-3">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
