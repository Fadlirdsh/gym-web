<!DOCTYPE html>
<html>

<head>
    <title>Jadwal Trainer</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            color: #4f46e5;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #4f46e5;
            color: #fff;
        }

        th,
        td {
            padding: 10px 8px;
            text-align: center;
            font-size: 12px;
        }

        tbody tr:nth-child(even) {
            background-color: #f3f4f6;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody tr:hover {
            background-color: #e0e7ff;
        }

        td {
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 11px;
            color: #fff;
        }

        .badge-aktif {
            background-color: #10b981;
        }

        .badge-nonaktif {
            background-color: #ef4444;
        }
    </style>
</head>

<body>
    <h2>Jadwal Trainer</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Kelas</th>
                <th>Trainer</th>
                <th>Fokus Kelas</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $shift)
                @foreach ($shift->schedules as $schedule)
                    <tr>
                        <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                        <td>{{ ucfirst($schedule->day) }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</td>
                        <td>{{ $schedule->kelas?->nama_kelas ?? '-' }}td>
                        <td>{{ $shift->trainer?->name ?? '-' }}</td>
                        <td>{{ $schedule->class_focus ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $schedule->is_active ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
