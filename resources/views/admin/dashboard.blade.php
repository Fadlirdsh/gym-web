@extends('layout.app')

@section('title', 'Dashboard — Corporate')

@section('content')

{{-- ========================= --}}
{{--     FIXED STYLE THEME    --}}
{{-- ========================= --}}
<style>

  /* === GLOBAL BACKGROUND === */
  .corp-bg {
    background: linear-gradient(180deg, #fdfdfd 0%, #eef2f9 100%);
  }

  @media (prefers-color-scheme: dark) {
    .corp-bg {
      background: linear-gradient(180deg, #071029 0%, #041227 100%);
    }
  }


  /* === CARD LIGHT MODE === */
  .card-corporate {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  /* === CARD DARK MODE === */
  .card-corporate-dark {
    background: rgba(30,41,59,0.95);
    border: 1px solid rgba(148,163,184,0.25);
    backdrop-filter: blur(10px);
    box-shadow: 0 5px 25px rgba(0,0,0,0.4);
  }


  /* === STAT ICON === */
  .stat-icon {
    background: #e8f2ff;
    color: #2563eb;
  }

  @media (prefers-color-scheme: dark) {
    .stat-icon {
      background: rgba(148,163,184,0.15);
      color: #60a5fa;
    }
  }

  /* === LIST DIVIDER === */
  .corp-divider {
    border-color: rgba(0,0,0,0.1);
  }

  @media (prefers-color-scheme: dark) {
    .corp-divider {
      border-color: rgba(148,163,184,0.15);
    }
  }

  /* === TEXT COLOR FIX === */
  .text-title {
    color: #1e293b;
  }
  @media (prefers-color-scheme: dark) {
    .text-title {
      color: #f1f5f9;
    }
  }

  .text-sub {
    color: #475569;
  }
  @media (prefers-color-scheme: dark) {
    .text-sub {
      color: #cbd5e1;
    }
  }

</style>


{{-- ========================= --}}
{{--       MAIN WRAPPER       --}}
{{-- ========================= --}}
<div class="min-h-screen corp-bg py-8">
  <div class="container mx-auto px-6">


    {{-- ========================= --}}
    {{--          HEADER          --}}
    {{-- ========================= --}}
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-title">
          Dashboard
        </h1>
        <p class="mt-1 text-sm text-sub">
          Selamat datang kembali, Admin 👋
        </p>
      </div>

      <div class="flex items-center gap-4">

        {{-- Search Bar --}}
        <div class="hidden sm:flex items-center card-corporate dark:card-corporate-dark px-3 py-2 rounded-lg shadow-md">
          <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>
          <input type="search"
            placeholder="Search..."
            class="ml-2 outline-none bg-transparent text-sm text-title dark:text-slate-200" />
        </div>

        {{-- Profile --}}
        <div class="flex items-center gap-3">
          <div class="text-right">
            <p class="text-xs text-sub">Admin</p>
            <p class="text-sm font-semibold text-title dark:text-slate-100">You</p>
          </div>

          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-600 to-indigo-600 
                      flex items-center justify-center text-white font-bold shadow-md">
            A
          </div>
        </div>
      </div>
    </div>



    {{-- ========================= --}}
    {{--        MAIN CONTENT       --}}
    {{-- ========================= --}}
    <div class="space-y-8">



      {{-- ========================= --}}
      {{--       STATISTICS CARD     --}}
      {{-- ========================= --}}
      <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-6">

        @php
          $stats = [
            ['label' => 'Total Pelanggan', 'value' => $totalUsers, 'icon' => '👥'],
            ['label' => 'Total Trainer', 'value' => $totalTrainers, 'icon' => '💪'],
            ['label' => 'Total Kelas', 'value' => $totalClasses, 'icon' => '📘'],
            ['label' => 'Jadwal Minggu Ini', 'value' => $totalSchedules, 'icon' => '📅'],
            ['label' => 'Diskon Aktif', 'value' => $activeDiscounts, 'icon' => '🏷️'],
            ['label' => 'Total Reservasi', 'value' => $totalReservations, 'icon' => '📝'],
          ];
        @endphp

        @foreach ($stats as $stat)
        <div class="rounded-2xl p-4 card-corporate dark:card-corporate-dark transition hover:shadow-lg">

          <div class="flex items-center gap-3">
            <div class="stat-icon p-3 rounded-xl text-lg">
              {{ $stat['icon'] }}
            </div>

            <div>
              <p class="text-xs text-sub dark:text-slate-300">{{ $stat['label'] }}</p>
              <p class="text-2xl font-bold text-title dark:text-white">{{ $stat['value'] }}</p>
            </div>
          </div>

        </div>
        @endforeach

      </div>



      {{-- ========================= --}}
      {{--       CHART SECTION       --}}
      {{-- ========================= --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- BAR CHART --}}
        <div class="rounded-2xl p-6 card-corporate dark:card-corporate-dark">
          <h2 class="text-lg font-semibold text-title dark:text-white mb-4">
            Grafik Reservasi Per Bulan
          </h2>
          <div id="reservationsBar" class="h-64"></div>
        </div>

        {{-- DONUT CHART --}}
        <div class="rounded-2xl p-6 card-corporate dark:card-corporate-dark">
          <h2 class="text-lg font-semibold text-title dark:text-white mb-4">
            Distribusi Pengguna Baru
          </h2>
          <div id="usersDonut" class="h-64"></div>
        </div>

      </div>



      {{-- ========================= --}}
      {{--     LATEST USERS & DISCOUNT --}}
      {{-- ========================= --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Latest Users --}}
        <div class="rounded-2xl p-6 card-corporate dark:card-corporate-dark">
          <h3 class="text-lg font-semibold text-title dark:text-white mb-4">
            🆕 Pengguna Terbaru
          </h3>

          <ul class="divide-y corp-divider">
            @forelse ($latestUsers as $user)
            <li class="py-3 flex items-center justify-between">

              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-500 to-indigo-600 
                            flex items-center justify-center text-white font-bold">
                  {{ strtoupper(substr($user->name,0,1)) }}
                </div>

                <div>
                  <p class="text-title dark:text-slate-100 font-semibold">{{ $user->name }}</p>
                  <p class="text-xs text-sub dark:text-slate-400">{{ $user->role }}</p>
                </div>
              </div>

              <p class="text-xs text-sub dark:text-slate-400">
                {{ $user->created_at->diffForHumans() }}
              </p>

            </li>
            @empty
            <li class="py-4 text-center text-sub dark:text-slate-400 italic">
              Belum ada pengguna baru
            </li>
            @endforelse
          </ul>
        </div>


        {{-- Latest Discounts --}}
        <div class="rounded-2xl p-6 card-corporate dark:card-corporate-dark">
          <h3 class="text-lg font-semibold text-title dark:text-white mb-4">
            💸 Diskon Terbaru
          </h3>

          <ul class="divide-y corp-divider">
            @forelse ($latestDiscounts as $diskon)
            <li class="py-3 flex items-center justify-between">

              <div>
                <p class="text-title dark:text-white font-semibold">
                  {{ $diskon->nama_diskon }}
                </p>
                <p class="text-xs text-sub dark:text-slate-400">
                  Berlaku sampai: {{ \Carbon\Carbon::parse($diskon->expired_at)->format('d M Y') }}
                </p>
              </div>

              <p class="text-emerald-600 dark:text-emerald-400 font-bold">
                {{ $diskon->diskon_persen }}%
              </p>

            </li>
            @empty
            <li class="py-4 text-center text-sub dark:text-slate-400 italic">
              Belum ada diskon terbaru
            </li>
            @endforelse
          </ul>

        </div>

      </div>

    </div>

  </div>
</div>



{{-- ========================= --}}
{{--      CHART DATA INJECT    --}}
{{-- ========================= --}}
<script>
  window.dashboardData = {
    reservationsData: @json(array_values($reservationsPerMonth)),
    trainerData: @json(array_values($trainerPerMonth)),
    pelangganData: @json(array_values($pelangganPerMonth)),
  };
</script>

@vite('resources/js/dashboard.js')

@endsection
