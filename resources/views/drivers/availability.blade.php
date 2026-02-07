@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">My Availability</h2>

    <form method="POST" action="{{ route('driver.updateAvailability', $driver->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="shift_id">Select a Shift</label>
            <select name="shift_id" id="shift_id" class="form-control" required>
                <option value="">-- Choose a shift --</option>
                @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}">
                        {{ $shift->day }}: {{ \Carbon\Carbon::parse($shift->starting_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Availability</button>
    </form>
</div>
@endsection
