@extends('layouts.app')

@section('title', 'Gói hội viên')

@section('content')
    <section class="gym-container py-10 sm:py-14">
        <header class="mb-8 max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-gym-blue">Lựa chọn của bạn</p>
            <h1 class="gym-title mt-2">Gói hội viên</h1>
            <p class="mt-3 text-slate-600">Xem giá trọn kỳ, chọn ngày bắt đầu và kiểm tra ngày tập cuối dự kiến trước khi đăng ký.</p>
        </header>
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($packages as $package)
                @php($isAnnual = $package->duration_months >= 12)
                <article class="gym-card flex flex-col {{ $isAnnual ? 'bg-slate-900 text-white' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-xl font-extrabold uppercase">{{ $package->name }}</h2>
                        <span class="rounded-md bg-gym-mint px-2 py-1 text-xs font-bold uppercase text-slate-900">{{ $package->duration_months }} tháng</span>
                    </div>
                    <p class="mt-5 text-2xl font-extrabold tabular-nums">{{ number_format($package->price, 0, ',', '.') }} VNĐ <span class="text-sm font-medium">/ toàn kỳ</span></p>
                    <p class="mt-3 flex-1 border-y border-current/20 py-4 text-sm leading-6">{{ $package->description ?: 'Tập luyện không giới hạn trong thời hạn gói.' }}</p>
                    <p class="mt-4 text-sm font-semibold">Ngày tập cuối nếu bắt đầu {{ $suggestedStartDate->format('d/m/Y') }}: {{ $lastTrainingDays[$package->id] }}</p>
                    <a class="gym-btn-primary mt-4 w-full" href="{{ route('packages.show', $package) }}">Xem chi tiết và đăng ký</a>
                </article>
            @empty
                <p class="rounded-xl bg-slate-50 p-6 text-slate-600 sm:col-span-2 xl:col-span-3">Hiện chưa có gói tập mở bán.</p>
            @endforelse
        </div>
    </section>
@endsection
