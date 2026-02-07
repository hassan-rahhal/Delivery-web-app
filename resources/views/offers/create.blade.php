@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Submit Offer for Request #{{ $request->id }}</h2>

    <form action="{{ route('driver.offers.create') }}" method="POST">
        @csrf

        <!-- Hidden input for request ID -->
        <input type="hidden" name="request_id" value="{{ $request->id }}">

        <div class="mb-3">
            <label>Price:</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
<div class="mb-3">
    <label for="currency">Currency:</label>
    <select name="currency" id="currency" class="form-control" required>
        <option value="" disabled selected>Select currency</option>
        <option value="USD">USD - US Dollar</option>
        <option value="EUR">EUR - Euro</option>
       
        <option value="LBP">LBP - Lebanese Pound</option>
   
        <!-- Add more currencies as needed -->
    </select>
</div>


        <div class="mb-3">
            <label>Payment Method:</label>
            <select name="payment_method" class="form-select" required>
                <option value="cash_on_delivery">Cash on Delivery</option>
                <option value="visa">Visa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Send Offer</button>
    </form>
</div>
@endsection