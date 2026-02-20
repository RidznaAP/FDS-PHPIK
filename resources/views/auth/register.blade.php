@extends('layouts.guest')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="auth-card">
    <div class="auth-card-body">
        <h2 class="auth-title">Daftar Akun Baru</h2>
        <form action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf

            <div class="form-group">
                <label class="form-lbl">Nama Lengkap</label>
                <input type="text" name="name" class="form-input @error('name') error @enderror" placeholder="Nama Pegawai / Admin" value="{{ old('name') }}" required>
                @error('name')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-lbl">Alamat Email</label>
                <input type="email" name="email" class="form-input @error('email') error @enderror" placeholder="nama@instansi.go.id" value="{{ old('email') }}" required>
                @error('email')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-lbl">Password</label>
                <input type="password" name="password" class="form-input @error('password') error @enderror" placeholder="Minimal 8 karakter" required>
                @error('password')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-lbl">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn-submit">Buat Akun Baru</button>
        </form>
    </div>
    <div class="auth-card-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</div>
@endsection
