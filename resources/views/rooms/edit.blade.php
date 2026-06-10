@extends('layouts.app')
@section('title','Edit Kamar '.$room->number)
@section('page-title','Edit Kamar')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('rooms.index') }}" class="hover:text-slate-600">Kamar</a> <span class="mx-1">/</span> Edit {{ $room->number }} @endsection

@section('content')
<div class="max-w-3xl">
<form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')
    @include('rooms._form', ['room' => $room])
    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            Simpan Perubahan
        </button>
        <a href="{{ route('rooms.show', $room) }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
            Batal
        </a>
    </div>
</form>
</div>
@endsection
