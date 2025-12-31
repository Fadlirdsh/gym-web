@extends('layout.app')

@section('title', 'Scan QR')

@section('content')
    <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4">
            <div class="relative aspect-square bg-black rounded overflow-hidden">
                <div id="reader" class="absolute inset-0"></div>
                <div class="scan-frame"></div>
                <div class="scan-line"></div>
            </div>

            <input id="manualToken" type="text" placeholder="Paste token manual"
                class="w-full mt-4 px-3 py-2 rounded-lg border
           border-slate-300 dark:border-slate-600
           bg-white dark:bg-slate-800
           text-sm" />

            <button id="btnManualSubmit"
                class="mt-3 w-full px-4 py-2 rounded-lg
           bg-indigo-600 text-white
           hover:bg-indigo-700 text-sm font-semibold">
                Konfirmasi Hadir
            </button>



            <div class="text-center mt-4">
                <span id="scanResult" class="scan-status">
                    Arahkan QR ke kamera
                </span>
            </div>
            <div class="text-center mt-6">
                <button id="btnCloseScan"
                    class="inline-flex items-center gap-2 px-4 py-2
           rounded-lg text-sm font-semibold
           bg-red-500 text-white
           hover:bg-red-600
           focus:outline-none focus:ring-2 focus:ring-red-400
           dark:bg-red-600 dark:hover:bg-red-700">
                    <i class="fa-solid fa-xmark"></i>
                    Tutup
                </button>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5Qr = null;
        let isStopped = false;

        document.addEventListener("DOMContentLoaded", () => {
            html5Qr = new Html5Qrcode("reader");

            html5Qr.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: 250
                },
                onScanSuccess,
                () => {}
            );

            document.getElementById('btnCloseScan').addEventListener('click', closeScanner);
        });

        function onScanSuccess(text) {
            console.log("SCAN TERBACA:", text);
            if (isStopped) return;
            isStopped = true;

            html5Qr.stop();

            document.getElementById('scanResult').innerText = 'Memproses...';

            const url = new URL(text);
            const token = url.searchParams.get('token');

            fetch("{{ route('attendance.scan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        token
                    })
                })
                .then(res => res.json())
                .then(res => {
                    document.getElementById('scanResult').innerText = res.message;
                });
        }

        function closeScanner() {
            if (html5Qr && !isStopped) {
                isStopped = true;
                html5Qr.stop().finally(redirectHome);
            } else {
                redirectHome();
            }
        }

        function redirectHome() {
            window.location.href = "{{ url('/admin/home') }}";
        }


        document.getElementById('btnManualSubmit').addEventListener('click', () => {
            const token = document.getElementById('manualToken').value.trim();

            if (!token) {
                document.getElementById('scanResult').innerText = 'Token kosong';
                return;
            }

            document.getElementById('scanResult').innerText = 'Memproses...';

            fetch("{{ route('attendance.scan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        token
                    })
                })
                .then(async (res) => {
                    let data;
                    try {
                        data = await res.json();
                    } catch (e) {
                        throw new Error("Response bukan JSON");
                    }

                    if (!res.ok) {
                        throw new Error(data.message || "Request gagal");
                    }

                    return data;
                })
                .then(data => {
                    document.getElementById('scanResult').innerText = data.message;
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('scanResult').innerText = err.message;
                });

        });
    </script>
@endpush
