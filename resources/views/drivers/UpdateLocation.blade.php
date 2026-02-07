@extends('layouts.app') {{-- assuming your layout is saved as layouts/app.blade.php --}}

@section('content')
    <h1>Update Your Current Location</h1>

    <button onclick="updateLocation()" class="btn btn-primary">Update Location</button>

    <div id="status" style="margin-top: 20px; color: green;"></div>
    <div id="error" style="margin-top: 20px; color: red;"></div>
@endsection

@section('script')
<script>
    function updateLocation() {
        const statusDiv = document.getElementById('status');
        const errorDiv = document.getElementById('error');
        statusDiv.innerText = '';
        errorDiv.innerText = '';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                fetch("{{ route('driver.location.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        statusDiv.innerText = data.message;
                    } else {
                        errorDiv.innerText = 'Unexpected server response.';
                    }
                })
                .catch(error => {
                    errorDiv.innerText = 'Error sending location: ' + error.message;
                });

            }, function(error) {
                errorDiv.innerText = 'Geolocation error: ' + error.message;
            });
        } else {
            errorDiv.innerText = 'Geolocation is not supported by this browser.';
        }
    }
</script>
@endsection
