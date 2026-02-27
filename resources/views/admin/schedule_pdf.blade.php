<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Jadwal Trainer</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background: #f2f2f2;
            text-align: center;
        }

        .no {
            width: 40px;
            text-align: center;
        }

        .jam {
            width: 150px;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>LAPORAN JADWAL TRAINER</h2>

    <div class="info">
        <strong>Trainer :</strong> {{ $shift->trainer->name }} <br>
        <strong>Hari :</strong> {{ $shift->day }} <br>
        <strong>Shift :</strong> {{ $shift->shift_start }} - {{ $shift->shift_end }}
    </div>

    <table>

        <thead>
            <tr>
                <th class="no">No</th>
                <th>Kelas</th>
                <th class="jam">Jam</th>
                <th>Fokus</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($shift->schedules as $key => $schedule)
                <tr>
                    <td class="no">{{ $key + 1 }}</td>
                    <td>{{ $schedule->kelas->nama_kelas }}</td>
                    <td class="jam">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                    <td>{{ $schedule->class_focus ?? '-' }}</td>
                </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
