@extends('layouts.app')
@section('title','Tambah Kamar')
@section('page-title','Tambah Kamar')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('rooms.index') }}" class="hover:text-slate-600">Kamar</a> <span class="mx-1">/</span> Tambah @endsection

@section('content')
<div class="max-w-3xl">
<form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @include('rooms._form', ['room' => null])
    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            Simpan Kamar
        </button>
        <a href="{{ route('rooms.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
            Batal
        </a>
    </div>
</form>
</div>
@endsection
