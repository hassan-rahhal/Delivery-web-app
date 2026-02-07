@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">My Shift</h2>
      <form method="POST" action="{{ route('driver.CreateShift', $driver->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="mode">Shift Mode</label>
                <select id="mode" name="mode" class="form-control" onchange="toggleAvailabilityFields()">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                </select>
            </div>

            {{-- Daily Input: start and end time --}}
            <div id="daily-input" class="form-group mb-3">
                <label>Today's Shift</label>
                <div class="d-flex gap-2">
                    <input type="time" name="daily[start]" class="form-control" placeholder="Start time" />
                    <input type="time" name="daily[end]" class="form-control" placeholder="End time" />
                </div>
            </div>

            {{-- Weekly Inputs: start and end per day --}}
            <div id="weekly-inputs" class="mb-3 d-none">
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="form-group mb-2">
                        <label>{{ $day }}</label>
                        <div class="d-flex gap-2">
                            <input type="time" name="weekly[{{ $day }}][start]" class="form-control" placeholder="Start time" />
                            <input type="time" name="weekly[{{ $day }}][end]" class="form-control" placeholder="End time" />
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Shift</button>
        </form>
    </div>

    <script>
        function toggleAvailabilityFields() {
            const mode = document.getElementById('mode').value;
            document.getElementById('daily-input').classList.toggle('d-none', mode !== 'daily');
            document.getElementById('weekly-inputs').classList.toggle('d-none', mode !== 'weekly');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            toggleAvailabilityFields();

            // Show popup on form submit
            const form = document.getElementById('availabilityForm');
            form.addEventListener('submit', function (event) {
                // Create and display popup
                const alertBox = document.createElement('div');
                alertBox.className = 'alert alert-success position-fixed top-0 end-0 m-3';
                alertBox.innerText = 'Your availability has been updated!';
                document.body.appendChild(alertBox);
                setTimeout(() => alertBox.remove(), 3000);
            });
        });
    </script>
@endsection