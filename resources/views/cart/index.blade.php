@extends('layouts.app')

@section('content')
  <div class="container" style="padding-top:120px; max-width:800px;">
    <h2 class="mb-4">Your Cart</h2>
    @if(empty($cartItems))
      <p class="text-muted">Your cart is empty. <a href="{{ url('/') }}#products">Shop now</a></p>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($cartItems as $item)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img src="{{ $item['img'] ?? '' }}" alt="{{ $item['name'] }}"
                         style="width:48px;height:48px;object-fit:cover;border-radius:8px;"
                         onerror="this.src='{{ asset('asset/210A0226.png') }}'">
                    <span>{{ $item['name'] }}</span>
                  </div>
                </td>
                <td>₹{{ number_format($item['price'], 2) }}</td>
                <td>{{ $item['qty'] }}</td>
                <td class="text-end">₹{{ number_format($item['price'] * $item['qty'], 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Total</th>
              <th class="text-end">
                ₹{{ number_format(array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cartItems)), 2) }}
              </th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="mt-4">
        <a href="{{ route('checkout.show') }}" class="btn btn-primary-engix btn-lg">
          <i class="fa-solid fa-lock me-2"></i>Proceed to Checkout
        </a>
      </div>
    @endif
  </div>
@endsection
