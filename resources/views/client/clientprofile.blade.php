@extends('layouts.app')

@section('content')
<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <form action="{{ route('client.uploadProfileImage', ['id' => $client->id]) }}" method="POST"
            enctype="multipart/form-data" id="uploadForm">
            @csrf
            <label for="profileImageInput" style="cursor: pointer;">
               <img src="{{ $client->profile_image ? asset('storage/' . $client->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . '+' . $client->last_name) . '&background=6b21a8&color=fff&size=120' }}" alt="ClientProfile" id="profileImagePreview">

            </label>
            <input type="file" name="profile_image" id="profileImageInput" accept="image/*" hidden
                onchange="document.getElementById('uploadForm').submit();">
        </form>

        <div>
            <h1>{{ $client->first_name }} {{ $client->last_name }}</h1>
            <p class="text-white text-opacity-80">Username: {{ $client->user_name }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tab-buttons">
        <button class="tab-btn active" data-tab="info">Info</button>
        <button class="tab-btn" data-tab="social">Social Media</button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <div class="tab-panel" id="info">
            <div class="card">
                <h2>Personal Information</h2>
                <form action="{{ route('client.update', ['id' => $client->id]) }}" method="POST" id="editProfileForm">
                    @csrf
                    @method('PUT')

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ $client->first_name }}" required>

                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ $client->last_name }}" required>

                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ $client->phone }}">

                    <label>Email</label>
                    <input type="email" name="email" value="{{ $client->email }}">

                    <label>Username</label>
                    <input type="text" name="user_name" value="{{ $client->user_name }}">

                    <button type="submit" class="edit-btn">Save Changes</button>
                    
                </form>
            </div>
        </div>

        <div class="tab-panel" id="social" hidden>
            <div class="card">
                <h2>Social Media Accounts</h2>
                <ul>
                    @forelse ($client->socialMediaAccounts ?? [] as $account)
                        <li>
                           {{ $account->provider->name ?? 'Unknown Provider' }}:
                            <a href="{{ $account->profile_url }}" target="_blank">{{ $account->account_name }}</a>
                        </li>
                    @empty
                        <li>No social media accounts found.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card">
                <h2>Add Social Media Account</h2>

                @if(session('success_social'))
                    <div class="alert alert-success">{{ session('success_social') }}</div>
                @endif

                <form action="{{ route('client.addSocialMediaAccount', ['id' => $client->id]) }}" method="POST">
                    @csrf
                    <label>Platform</label>
                    <select name="provider_id" required>
                        <option value="">-- Select Platform --</option>
                        @foreach(\App\Models\Social_Media_Provider::all() as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>

                    <label>Account Name</label>
                    <input type="text" name="account_name" required>

                    <label>Profile URL</label>
                    <input type="url" name="profile_url">

                    <button type="submit" class="edit-btn">Add Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-tab');

            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => panel.hidden = true);

            document.getElementById(tab).hidden = false;
            button.classList.add('active');
        });
    });
</script>
@endsection

@section('styles_Client')
<style>
    body {
        background-color: #f3f4f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .profile-container {
        max-width: 1000px;
        margin: 3rem auto;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(to right, #6b21a8, #9333ea);
        color: white;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-header img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
    }

    .profile-header h1 {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }

    .tab-buttons {
        display: flex;
        border-bottom: 2px solid #e5e7eb;
    }

    .tab-buttons button {
        flex: 1;
        padding: 1rem;
        font-weight: 600;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: all 0.3s;
    }

    .tab-buttons button.active {
        border-color: #6b21a8;
        color: #6b21a8;
        background-color: #f9f5ff;
    }

    .tab-content {
        padding: 2rem;
    }

    .card {
        background: #f9fafb;
        padding: 1.5rem;
        border-left: 5px solid #6b21a8;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }

    .card h2 {
        margin-bottom: 1rem;
        font-size: 1.25rem;
        color: #6b21a8;
    }

    .card p,
    .card li {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .edit-btn {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.75rem 1.5rem;
        background-color: #6b21a8;
        color: white;
        font-weight: 600;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .edit-btn:hover {
        background-color: #581c87;
    }

    [hidden] {
        display: none;
    }

    form input,
    form select {
        width: 100%;
        padding: 0.5rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 0.375rem;
    }

    form label {
        font-weight: 600;
        display: block;
        margin-top: 1rem;
        margin-bottom: 0.25rem;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
    }

    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }
</style>
@endsection
