@extends('admin.layout')

@section('content')
  <!-- Stats grid -->
  <section class="grid" style="grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 12px;">
    <div class="card">
      <p class="muted" style="margin:0 0 6px;">Total users</p>
      <p style="margin:0; font-weight:700; font-size:22px;">
        {{ $stats['users_total'] ?? '—' }}
      </p>
    </div>
    <div class="card">
      <p class="muted" style="margin:0 0 6px;">Active</p>
      <p style="margin:0; font-weight:700; font-size:22px; color:#065f46;">
        {{ $stats['users_active'] ?? '—' }}
      </p>
    </div>
    <div class="card">
      <p class="muted" style="margin:0 0 6px;">Inactive</p>
      <p style="margin:0; font-weight:700; font-size:22px; color:#9f1239;">
        {{ $stats['users_inactive'] ?? '—' }}
      </p>
    </div>
    <div class="card">
      <p class="muted" style="margin:0 0 6px;">Online (last 5 min)</p>
      <p style="margin:0; font-weight:700; font-size:22px; color:#1d4ed8;">
        {{ $stats['users_online'] ?? '—' }}
      </p>
    </div>
  </section>

  <!-- Responsive upgrade to 2/3 columns on larger screens -->
  <style>
    @media (min-width: 640px){ section.grid{ grid-template-columns:repeat(2, minmax(0,1fr)); } }
    @media (min-width: 1024px){ section.grid{ grid-template-columns:repeat(4, minmax(0,1fr)); } }
  </style>

  <!-- Recent activity + plans breakdown -->
  <section style="display:grid; grid-template-columns: 1fr; gap: 12px; margin-top:16px;">
    <div class="card" style="overflow:auto;">
      <h2 style="margin:0 0 10px; font-size:14px; font-weight:600;">Recent activity</h2>
      <div style="min-width:600px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
          <thead>
            <tr>
              <th style="text-align:left; padding:8px 10px; border-bottom:1px solid var(--border);">User</th>
              <th style="text-align:left; padding:8px 10px; border-bottom:1px solid var(--border);">Email</th>
              <th style="text-align:left; padding:8px 10px; border-bottom:1px solid var(--border);">Plan</th>
              <th style="text-align:left; padding:8px 10px; border-bottom:1px solid var(--border);">Status</th>
              <th style="text-align:left; padding:8px 10px; border-bottom:1px solid var(--border);">Last activity</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recent ?? [] as $u)
              @php $online = $u->last_activity_at && $u->last_activity_at >= now()->subMinutes(5); @endphp
              <tr>
                <td style="padding:8px 10px; border-top:1px solid var(--border);">{{ $u->name }}</td>
                <td style="padding:8px 10px; border-top:1px solid var(--border);">{{ $u->email }}</td>
                <td style="padding:8px 10px; border-top:1px solid var(--border); text-transform:uppercase;">{{ $u->plan ?? '—' }}</td>
                <td style="padding:8px 10px; border-top:1px solid var(--border);">
                  <span style="font-size:12px; padding:2px 8px; border-radius:999px; display:inline-block; background:{{ $u->is_active ? 'rgba(16,185,129,0.12)' : 'rgba(244,63,94,0.12)' }}; color:{{ $u->is_active ? '#065f46' : '#9f1239' }};">
                    {{ $u->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td style="padding:8px 10px; border-top:1px solid var(--border);">
                  <span style="color:{{ $online ? '#16a34a' : 'var(--muted)' }}">
                    {{ $online ? 'Online now' : optional($u->last_activity_at)->diffForHumans() ?? '—' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="padding:12px 10px; text-align:center; color:var(--muted); border-top:1px solid var(--border);">
                  No recent activity.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <h2 style="margin:0 0 10px; font-size:14px; font-weight:600;">Plans breakdown</h2>
      <ul style="list-style:none; margin:0; padding:0;">
        @forelse(($plans ?? []) as $p)
          <li style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-top:1px solid var(--border);">
            <span style="text-transform:uppercase;">{{ $p->plan ?? '—' }}</span>
            <span style="font-weight:600;">{{ $p->total }}</span>
          </li>
        @empty
          <li style="padding:8px 0; color:var(--muted);">No plan data.</li>
        @endforelse
      </ul>
    </div>
  </section>

  <style>
    @media (min-width: 1024px){
      section[style*="grid-template-columns: 1fr;"]{
        grid-template-columns: 2fr 1fr;
      }
    }
  </style>
@endsection