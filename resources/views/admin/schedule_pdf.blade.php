<h2>Laporan Jadwal Trainer</h2>

<p>
Trainer : {{ $shift->trainer->name }} <br>
Hari : {{ $shift->day }} <br>
Shift : {{ $shift->shift_start }} - {{ $shift->shift_end }}
</p>

<table border="1" width="100%">
<thead>
<tr>
<th>No</th>
<th>Kelas</th>
<th>Jam</th>
<th>Fokus</th>
</tr>
</thead>

<tbody>

@foreach($shift->schedules as $key => $schedule)

<tr>
<td>{{ $key+1 }}</td>
<td>{{ $schedule->kelas->nama_kelas }}</td>
<td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
<td>{{ $schedule->class_focus ?? '-' }}</td>
</tr>

@endforeach

</tbody>
</table>