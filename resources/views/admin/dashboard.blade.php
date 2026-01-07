@extends('layout.app')

@section('title', 'Dashboard — Corporate')

@section('content')

{{-- ========================================================== --}}
{{--                AUTO LIGHT / DARK THEME STYLE               --}}
{{-- ========================================================== --}}
<style>
    /* Background */
    .corp-bg {
        background: linear-gradient(180deg, #fdfdfd 0%, #eef2f9 100%);
    }
    @media (prefers-color-scheme: dark) {
        .corp-bg {
            background: linear-gradient(180deg, #0b1326 0%, #091324 100%);
        }
    }

    /* Card */
    .card-corporate {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 1rem;
        transition: 0.25s ease;
    }
    .card-corporate:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }

    @media (prefers-color-scheme: dark) {
        .card-corporate {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(100, 116, 139, 0.25);
            backdrop-filter: blur(14px);
        }
        .card-corporate:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.45);
        }
    }

    /* Icon Badge */
    .stat-icon {
        background: #e8f2ff;
        color: #2563eb;
    }
    @media (prefers-color-scheme: dark) {
        .stat-icon {
            background: rgba(100, 116, 139, 0.20);
            color: #60a5fa;
        }
    }

    /* Divider */
    .corp-divider {
        border-color: rgba(0,0,0,0.08);
    }
    @media (prefers-color-scheme: dark) {
        .corp-divider {
            border-color: rgba(100,116,139,0.20);
        }
    }

    /* Text color */
    .text-title { color: #1e293b; }
    .text-sub { color: #64748b; }

    @media (prefers-color-scheme: dark) {
        .text-title { color: #f1f5f9; }
        .text-sub   { color: #cbd5e1; }
    }
</style>

{{-- ========================================================== --}}
{{--                       MAIN WRAPPER                        --}}
{{-- ========================================================== --}}
<div class="min-h-screen corp-bg py-10">
    <div class="container mx-auto px-5 lg:px-6">



        {{-- ========================== HEADER ========================== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-12">

            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-title">Dashboard</h1>
                <p class="mt-1 text-sm text-sub">Selamat datang kembali, Admin</p>

            </div>

            <div class="flex items-center gap-4">

                {{-- Search --}}
                <div class="hidden sm:flex items-center card-corporate px-3 py-2 rounded-xl shadow-sm">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                    <input type="search"
                           placeholder="Search…"
                           class="ml-2 outline-none bg-transparent text-sm text-title dark:text-slate-200 w-36 focus:w-48 transition-all"/>
                </div>

                {{-- Mini profile --}}
                <div class="flex items-center gap-3">
                    <div class="text-right leading-tight">
                        <p class="text-xs text-sub">Admin</p>
                        <p class="text-sm font-semibold text-title">You</p>
                    </div>

                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow">
                        A
                    </div>
                </div>

            </div>
        </div>



        {{-- ========================== STATS ========================== --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-5">

          @php
    $stats = [
        [
            'label' => 'Total Pelanggan',
            'value' => $totalUsers,
            'icon'  => 'fa-users',
            'color' => 'text-sky-600 bg-sky-100 dark:text-sky-400 dark:bg-sky-500/20'
        ],
        [
            'label' => 'Total Trainer',
            'value' => $totalTrainers,
            'icon'  => 'fa-dumbbell',
            'color' => 'text-rose-600 bg-rose-100 dark:text-rose-400 dark:bg-rose-500/20'
        ],
        [
            'label' => 'Total Kelas',
            'value' => $totalClasses,
            'icon'  => 'fa-book-open',
            'color' => 'text-indigo-600 bg-indigo-100 dark:text-indigo-400 dark:bg-indigo-500/20'
        ],
        [
            'label' => 'Jadwal Minggu Ini',
            'value' => $totalSchedules,
            'icon'  => 'fa-calendar-week',
            'color' => 'text-emerald-600 bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/20'
        ],
        [
            'label' => 'Diskon Aktif',
            'value' => $activeDiscounts,
            'icon'  => 'fa-tags',
            'color' => 'text-amber-600 bg-amber-100 dark:text-amber-400 dark:bg-amber-500/20'
        ],
        [
            'label' => 'Total Reservasi',
            'value' => $totalReservations,
            'icon'  => 'fa-clipboard-list',
            'color' => 'text-violet-600 bg-violet-100 dark:text-violet-400 dark:bg-violet-500/20'
        ],
    ];
@endphp



            @foreach ($stats as $stat)
            <div class="card-corporate p-4 rounded-2xl">
                <div class="flex items-center gap-4">

                  <div class="p-3 rounded-xl text-xl flex items-center justify-center {{ $stat['color'] }}">
                        <i class="fa-solid {{ $stat['icon'] }}"></i>
                   </div>



                    <div>
                        <p class="text-xs text-sub">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-title">{{ $stat['value'] }}</p>
                    </div>

                </div>
            </div>
            @endforeach

        </div>



        {{-- ========================== CHARTS ========================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">

            <div class="card-corporate p-6">
                <h2 class="text-lg font-semibold text-title mb-4">Grafik Reservasi Per Bulan</h2>
                <div id="reservationsBar" class="h-72 w-full"></div>
            </div>

            <div class="card-corporate p-6">
                <h2 class="text-lg font-semibold text-title mb-4">Distribusi Pengguna Baru</h2>
                <div id="usersDonut" class="h-72 w-full"></div>
            </div>

        </div>



        {{-- ========================== LISTS ========================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">



            {{-- Latest Users --}}
            <div class="card-corporate p-6">
                <h3 class="text-lg font-semibold text-title mb-4">🆕 Pengguna Terbaru</h3>

                <ul class="divide-y corp-divider">
                    @forelse ($latestUsers as $user)
                        <li class="py-3 flex items-center justify-between">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-500 to-indigo-600 
                                            flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="text-title font-semibold">{{ $user->name }}</p>
                                    <p class="text-xs text-sub">{{ $user->role }}</p>
                                </div>

                            </div>

                            <p class="text-xs text-sub">
                                {{ $user->created_at->diffForHumans() }}
                            </p>

                        </li>
                    @empty
                        <li class="py-4 text-center text-sub italic">Belum ada pengguna baru</li>
                    @endforelse
                </ul>
            </div>



            {{-- Latest Discounts --}}
            <div class="card-corporate p-6">
                <h3 class="text-lg font-semibold text-title mb-4">💸 Diskon Terbaru</h3>

                <ul class="divide-y corp-divider">
                    @forelse ($latestDiscounts as $diskon)
                        <li class="py-3 flex items-center justify-between">

                            <div>
                                <p class="text-title font-semibold">{{ $diskon->nama_diskon }}</p>
                                <p class="text-xs text-sub">
                                    Berlaku sampai: 
                                    {{ \Carbon\Carbon::parse($diskon->expired_at)->format('d M Y') }}
                                </p>
                            </div>

                            <p class="text-emerald-600 dark:text-emerald-400 font-bold">
                                {{ $diskon->diskon_persen }}%
                            </p>

                        </li>
                    @empty
                        <li class="py-4 text-center text-sub italic">Belum ada diskon terbaru</li>
                    @endforelse
                </ul>

            </div>

        </div>


    </div>
</div>



{{-- ===============  CHART DATA INJECT  =============== --}}
<script>
    window.dashboardData = {
        reservationsData: @json(array_values($reservationsPerMonth)),
        trainerData: @json(array_values($trainerPerMonth)),
        pelangganData: @json(array_values($pelangganPerMonth)),
    };
</script>

@vite('resources/js/dashboard.js')

@endsection
