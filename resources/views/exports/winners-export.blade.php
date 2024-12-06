<table align="left">
    <thead>
        <tr style="background-color: #29007B; color: white">
            <th style="background-color: #29007B; color: white;">DARBC ID</th>
            <th style="background-color: #29007B; color: white;">Member Name</th>
            <th style="background-color: #29007B; color: white;">Prize</th>
            <th style="background-color: #29007B; color: white;">Event</th>
            <th style="background-color: #29007B; color: white;">Date Won</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($winners as $winner)
        <tr>
            <td align="left" width="40">{{ $winner->member->darbc_id ?? 'N/A' }}</td>
            <td align="left" width="40">{{ $winner->member->full_name ?? 'N/A' }}</td>
            <td align="left" width="40">{{ $winner->prize->name ?? 'N/A' }}</td>
            <td align="left" width="40">{{ $winner->prize->event->name ?? 'N/A' }}</td>
            <td align="left" width="40">{{ $winner->created_at ? $winner->created_at->format('m/d/Y') : 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
