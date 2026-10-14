@extends('layouts.app')

@section('title', $package->name)

@section('content')
    <section class="gym-container py-10 sm:py-14">
        <a class="font-semibold text-gym-blue underline underline-offset-4 hover:text-gym-ink focus-visible:outline-2 focus-visible:outline-gym-blue" href="{{ route('packages.index') }}">← Tất cả gói tập</a>
        <div class="mx-auto mt-5 max-w-4xl">
            @php($isAnnual = $package->duration_months >= 12)
            <article class="overflow-hidden rounded-3xl border border-slate-100 shadow-[0_20px_60px_rgba(20,48,68,0.12)]">
                <div class="p-6 sm:p-10 {{ $isAnnual ? 'bg-slate-900 text-white' : 'bg-white' }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div><p class="text-sm font-bold uppercase tracking-[0.18em] text-gym-blue">The Gym · Gói hội viên</p><h1 class="gym-title mt-2 {{ $isAnnual ? 'text-white' : '' }}">{{ $package->name }}</h1></div>
                        <span class="rounded-full bg-gym-mint px-4 py-2 text-sm font-bold text-slate-900">{{ $package->duration_months }} tháng</span>
                    </div>
                    <p class="mt-6 text-3xl font-extrabold tabular-nums sm:text-4xl">{{ number_format($package->price, 0, ',', '.') }} VNĐ <span class="text-base font-medium">/ toàn kỳ</span></p>
                    <p class="mt-4 leading-7">{{ $package->description ?: 'Tập luyện không giới hạn trong thời hạn gói.' }}</p>
                    <div class="mt-6 rounded-2xl border border-current/20 p-4 text-sm leading-6">
                        Thời hạn tính theo tháng lịch. Gói có hiệu lực từ 00:00 ngày bắt đầu đến trước 00:00 ngày hết hạn; ngày tập cuối hiển thị riêng để bạn dễ theo dõi.
                    </div>

                    @auth
                        @if (auth()->user()->role === 'customer')
                            <form class="mt-6 grid gap-4 md:grid-cols-3 md:items-end" method="POST" action="{{ route('orders.store') }}" data-membership-purchase data-duration-months="{{ $package->duration_months }}">
                                @csrf
                                <p class="font-semibold md:col-span-3" data-last-training-day>Ngày tập cuối nếu bắt đầu {{ $suggestedStartDate->format('d/m/Y') }}: {{ $lastTrainingDay->format('d/m/Y') }}</p>
                                <input type="hidden" name="gym_package_id" value="{{ $package->id }}">
                                <div><label class="gym-label {{ $isAnnual ? 'text-white' : '' }}" for="detail-start-date">Ngày bắt đầu mong muốn</label><input class="gym-input" id="detail-start-date" name="start_date" type="date" data-start-date autocomplete="off" min="{{ now(config('app.timezone'))->toDateString() }}" value="{{ old('start_date', $suggestedStartDate->toDateString()) }}" required></div>
                                <div><label class="gym-label {{ $isAnnual ? 'text-white' : '' }}" for="detail-payment">Dự kiến thanh toán</label><select class="gym-input" id="detail-payment" name="payment_method" required><option value="bank_transfer">Chuyển khoản</option><option value="cash">Tiền mặt tại quầy</option></select></div>
                                <button class="gym-btn-primary w-full" type="submit">Tạo đơn đăng ký</button>
                            </form>
                        @else
                            <p class="mt-6 font-semibold">Ngày tập cuối dự kiến nếu bắt đầu hôm nay: {{ $lastTrainingDay->format('d/m/Y') }}</p>
                        @endif
                    @endauth
                    @guest
                        <form class="mt-6 grid gap-4 sm:grid-cols-2 sm:items-end" method="GET" action="{{ route('login') }}" data-membership-purchase data-duration-months="{{ $package->duration_months }}">
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <p class="font-semibold sm:col-span-2" data-last-training-day>Ngày tập cuối nếu bắt đầu {{ $suggestedStartDate->format('d/m/Y') }}: {{ $lastTrainingDay->format('d/m/Y') }}</p>
                            <div><label class="gym-label {{ $isAnnual ? 'text-white' : '' }}" for="guest-start-date">Ngày bắt đầu dự kiến</label><input class="gym-input" id="guest-start-date" name="start_date" type="date" data-start-date autocomplete="off" min="{{ now(config('app.timezone'))->toDateString() }}" value="{{ $suggestedStartDate->toDateString() }}" required></div>
                            <button class="gym-btn-primary w-full" type="submit">Đăng nhập để tham gia</button>
                        </form>
                    @endguest
                </div>
            </article>
        </div>
    </section>
@endsection
