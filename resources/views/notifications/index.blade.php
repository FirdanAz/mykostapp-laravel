@extends('layouts.app')
@section('title','Notifikasi')
@section('page-title','Notifikasi')
@section('breadcrumb') <span class="mx-1">/</span> Notifikasi @endsection

@section('content')
<div class="max-w-2xl space-y-4">

<div class="flex items-center justify-between">
    <p class="text-sm text-slate-500">
        <span class="font-semibold text-slate-700">{{ $notifications->total() }}</span> notifikasi
    </p>
    @if(auth()->user()->unreadNotifications()->count())
    <button onclick="markAllRead()"
            class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Tandai Semua Dibaca
    </button>
    @endif
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    @forelse($notifications as $notif)
    <a href="{{ route('notifications.read', $notif) }}"
       class="flex items-start gap-4 px-6 py-4 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors
              {{ $notif->isUnread() ? 'bg-blue-50/40' : '' }}">
        {{-- Icon --}}
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-xl
                    {{ $notif->isUnread() ? 'bg-blue-100' : 'bg-slate-100' }}">
            {{ $notif->icon }}
        </div>
        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-semibold {{ $notif->isUnread() ? 'text-slate-900' : 'text-slate-700' }}">
                    {{ $notif->title }}
                </p>
                <span class="text-xs text-slate-400 flex-shrink-0 mt-0.5">
                    {{ $notif->created_at->diffForHumans() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
        </div>
        {{-- Unread dot --}}
        @if($notif->isUnread())
        <div class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-2"></div>
        @endif
    </a>
    @empty
    <div class="p-16 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <p class="font-semibold text-slate-600">Tidak ada notifikasi</p>
        <p class="text-sm text-slate-400 mt-1">Semua aktivitas terkini akan muncul di sini</p>
    </div>
    @endforelse
</div>

<div class="flex justify-center">
    {{ $notifications->links() }}
</div>

</div>
@endsection

@push('scripts')
<script>
async function markAllRead() {
    await fetch('{{ route('notifications.mark-all-read') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    });
    window.location.reload();
}
</script>
@endpush
