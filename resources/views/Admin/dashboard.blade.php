@extends('Admin.layout')

@section('title', 'Dashboard - Admin Panel')

@section('content')

<style>
  .page-header{
    margin-bottom: 32px;
  }
  .page-title{
    font-size:28px;
    font-weight: 700;
    margin: 0;
    letter-spacing:-0.02em;
  }
  .page-subtitle{
    color: var(--muted);
    margin-top:6px;
    font-size:15px;
  }

  /* Stats Grid */
  .stats-grid{
    display: grid;
    grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));
    gap:20px;
    margin-bottom:32px;
  }
  .stat-card{
    background:var(--panel);
    border:1px solid var(--border);
    border-radius:12px;
    padding:20px;
    box-shadow:0 1px 2px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
    transition: all 0.2s ease;
    position:relative;
    overflow:hidden;
  }
  .stat-card:hover{
    transform:translateY(-2px);
    box-shadow:0 2px 4px rgba(0,0,0,0.06), 0 12px 32px rgba(0,0,0,0.1);
  }
  .stat-card::before{
    content:'';
    position:absolute;
    top:0; right:0;
    width:80px; height:80px;
    background:radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius:50%;
    pointer-events:none;
  }
  .stat-content{
    position:relative;
    z-index:2;
  }
  .stat-label{
    color:var(--muted);
    font-size:14px;
    font-weight:500;
    margin-bottom:8px;
    display:flex;
    align-items:center;
    gap:6px;
  }
  .stat-label i{
    font-size: 16px;
    opacity:0.8;
  }
  .stat-number{
    font-size:32px;
    font-weight: 700;
    letter-spacing:-0.02em;
    color:var(--text);
  }
  .stat-change{
    font-size:13px;
    color:var(--success);
    margin-top:8px;
    display:flex;
    align-items:center;
    gap:4px;
  }

  /* Charts Section */
  .charts-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(350px, 1fr));
    gap:20px;
    margin-bottom:32px;
  }
  .chart-card{
    background:var(--panel);
    border:1px solid var(--border);
    border-radius:12px;
    padding:24px;
    box-shadow:0 1px 2px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
  }
  .chart-title{
    font-size:16px;
    font-weight: 600;
    margin: 0 0 16px 0;
    color:var(--text);
    display:flex;
    align-items:center;
    gap:8px;
  }
  .chart-title i{
    font-size:18px;
    color:var(--primary);
  }

  /* Tables Section */
  .tables-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(450px, 1fr));
    gap:20px;
    margin-bottom:32px;
  }
  .table-card{
    background:var(--panel);
    border:1px solid var(--border);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 1px 2px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
  }
  .table-header{
    padding:20px 24px;
    border-bottom:1px solid var(--border);
    background:linear-gradient(135deg, rgba(79,70,229,0.08) 0%, rgba(59,130,246,0.04) 100%);
    display:flex;
    align-items:center;
    gap:10px;
  }
  .table-header h3{
    margin:0;
    font-size:16px;
    font-weight:600;
    color:var(--text);
  }
  .table-header i{
    font-size:18px;
    color:var(--primary);
  }

  /* Table Styles */
  table{
    width:100%;
    border-collapse:collapse;
  }
  table thead{
    background:rgba(79,70,229,0.04);
  }
  table th{
    padding:12px 16px;
    text-align:left;
    font-size:13px;
    font-weight: 600;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:0.05em;
  }
  table td{
    padding:14px 16px;
    border-bottom:1px solid rgba(0,0,0,0.02);
    font-size:14px;
  }
  table tbody tr{
    transition:background 0.2s ease;
  }
  table tbody tr:hover{
    background:rgba(79,70,229,0.04);
  }
  table tbody tr: last-child td{
    border-bottom:none;
  }

  /* Badges */
  .badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:6px 12px;
    border-radius:6px;
    font-size: 12px;
    font-weight:600;
  }
  .badge-success{
    background:rgba(16,185,129,0.12);
    color:var(--success);
  }
  .badge-danger{
    background: rgba(239,68,68,0.12);
    color:var(--danger);
  }
  .badge-warning{
    background:rgba(245,158,11,0.12);
    color:var(--warning);
  }
  .badge-info{
    background: rgba(59,130,246,0.12);
    color:var(--info);
  }

  /* Empty State */
  .empty-state{
    padding:40px;
    text-align: center;
    color:var(--muted);
  }
  .empty-state i{
    font-size:48px;
    opacity:0.3;
    margin-bottom:12px;
  }

  @media (max-width:768px){
    .stats-grid{
      grid-template-columns:1fr;
    }
    .charts-grid{
      grid-template-columns:1fr;
    }
    .tables-grid{
      grid-template-columns: 1fr;
    }
    .page-title{
      font-size:24px;
    }
  }
</style>

<!-- Page Header -->
<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
  <p class="page-subtitle">Welcome back!  Here's what's happening with your system today. </p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
  <!-- Total Users -->
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-label">
        <i class="fas fa-users"></i> Total Users
      </div>
      <div class="stat-number">{{ $stats['total_users'] }}</div>
      <div class="stat-change">
        <i class="fas fa-arrow-up"></i> All-time
      </div>
    </div>
  </div>

  <!-- Active Users -->
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-label">
        <i class="fas fa-check-circle" style="color:var(--success);"></i> Active Users
      </div>
      <div class="stat-number">{{ $stats['active_users'] }}</div>
      <div class="stat-change" style="color:var(--success);">
        <i class="fas fa-arrow-up"></i> {{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}%
      </div>
    </div>
  </div>

  <!-- Inactive Users -->
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-label">
        <i class="fas fa-ban" style="color:var(--danger);"></i> Inactive Users
      </div>
      <div class="stat-number">{{ $stats['inactive_users'] }}</div>
      <div class="stat-change" style="color: var(--danger);">
        <i class="fas fa-arrow-down"></i> {{ round(($stats['inactive_users'] / max($stats['total_users'], 1)) * 100) }}%
      </div>
    </div>
  </div>

  <!-- Total Businesses -->
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-label">
        <i class="fas fa-building"></i> Businesses
      </div>
      <div class="stat-number">{{ $stats['total_businesses'] }}</div>
      <div class="stat-change">
        <i class="fas fa-arrow-up"></i> Growing
      </div>
    </div>
  </div>

  <!-- Total Admins -->
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-label">
        <i class="fas fa-user-shield"></i> Admin Users
      </div>
      <div class="stat-number">{{ $stats['total_admins'] }}</div>
      <div class="stat-change">
        <i class="fas fa-info-circle"></i> {{ $stats['admins_active'] }} Active
      </div>
    </div>
  </div>

  <!-- 2FA Enabled -->
  <div class="stat-card">
    <div class="stat-content">
      <div class="stat-label">
        <i class="fas fa-shield-alt" style="color:var(--success);"></i> 2FA Enabled
      </div>
      <div class="stat-number">{{ $twoFactorStats['users_2fa_enabled'] + $twoFactorStats['admins_2fa_enabled'] }}</div>
      <div class="stat-change" style="color:var(--success);">
        <i class="fas fa-check"></i> Secure
      </div>
    </div>
  </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
  <!-- Users Growth Chart -->
  <div class="chart-card">
    <h3 class="chart-title">
      <i class="fas fa-chart-line"></i> Users Growth (30 Days)
    </h3>
    <div style="height:300px;">
      <canvas id="usersGrowthChart"></canvas>
    </div>
  </div>

  <!-- Users by Role Chart -->
  <div class="chart-card">
    <h3 class="chart-title">
      <i class="fas fa-users-cog"></i> Distribution by Role
    </h3>
    <div style="height: 300px;">
      <canvas id="usersByRoleChart"></canvas>
    </div>
  </div>
</div>

<!-- Business Status Chart -->
<div class="chart-card" style="margin-bottom:32px;">
  <h3 class="chart-title">
    <i class="fas fa-chart-bar"></i> Business Status Overview
  </h3>
  <div style="height:300px;">
    <canvas id="businessStatusChart"></canvas>
  </div>
</div>

<!-- Tables Section -->
<div class="tables-grid">
  <!-- Recent Users Table -->
  <div class="table-card">
    <div class="table-header">
      <i class="fas fa-user-tie"></i>
      <h3>Recent Users</h3>
    </div>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentUsers as $user)
        <tr>
          <td>
            <strong>{{ $user->name }}</strong>
          </td>
          <td>
            <small style="color:var(--muted);">{{ $user->email }}</small>
          </td>
          <td>
            <span class="badge badge-info">
              <i class="fas fa-tag"></i>
              {{ $user->role->name ??  '-' }}
            </span>
          </td>
          <td>
            @if($user->is_active)
              <span class="badge badge-success">
                <i class="fas fa-circle"></i> Active
              </span>
            @else
              <span class="badge badge-danger">
                <i class="fas fa-circle"></i> Inactive
              </span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4">
            <div class="empty-state">
              <i class="fas fa-inbox"></i>
              <p>No users found</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Recent Businesses Table -->
  <div class="table-card">
    <div class="table-header">
      <i class="fas fa-building"></i>
      <h3>Recent Businesses</h3>
    </div>
    <table>
      <thead>
        <tr>
          <th>Business Name</th>
          <th>Owner</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentBusinesses as $business)
        <tr>
          <td>
            <strong>{{ $business->name }}</strong>
          </td>
          <td>
            <small style="color:var(--muted);">{{ $business->owner->name ?? '-' }}</small>
          </td>
          <td>
            @if($business->is_active)
              <span class="badge badge-success">
                <i class="fas fa-circle"></i> Active
              </span>
            @else
              <span class="badge badge-danger">
                <i class="fas fa-circle"></i> Inactive
              </span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="3">
            <div class="empty-state">
              <i class="fas fa-inbox"></i>
              <p>No businesses found</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Admin Activity Table -->
<div class="table-card">
  <div class="table-header">
    <i class="fas fa-user-secret"></i>
    <h3>Admin Activity</h3>
  </div>
  <table>
    <thead>
      <tr>
        <th>Admin Name</th>
        <th>Email</th>
        <th>Last Login</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($adminActivity as $adm)
      <tr>
        <td>
          <strong>{{ $adm->name }}</strong>
        </td>
        <td>
          <small style="color:var(--muted);">{{ $adm->email }}</small>
        </td>
        <td>
          <small style="color:var(--muted);">
            {{ $adm->last_login_at ? $adm->last_login_at->format('M d, Y g:i A') : 'Never' }}
          </small>
        </td>
        <td>
          @if($adm->is_active)
            <span class="badge badge-success">
              <i class="fas fa-circle"></i> Active
            </span>
          @else
            <span class="badge badge-danger">
              <i class="fas fa-circle"></i> Inactive
            </span>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4">
          <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No admin activity</p>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection

@section('scripts')
<script>
  // Users Growth Chart
  const usersGrowthCtx = document.getElementById('usersGrowthChart').getContext('2d');
  new Chart(usersGrowthCtx, {
    type: 'line',
    data: {
      labels: @json($usersGrowth->pluck('date')),
      datasets: [{
        label: 'New Users',
        data: @json($usersGrowth->pluck('count')),
        borderColor: '#4f46e5',
        backgroundColor:  'rgba(79, 70, 229, 0.08)',
        tension: 0.4,
        fill: true,
        pointRadius: 4,
        pointBackgroundColor: '#4f46e5',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y:  { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { color: 'rgba(0,0,0,0.05)' } }
      }
    }
  });

  // Users by Role Chart
  const usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
  new Chart(usersByRoleCtx, {
    type: 'doughnut',
    data: {
      labels: @json($usersByRole->pluck('role')),
      datasets: [{
        data: @json($usersByRole->pluck('count')),
        backgroundColor: [
          '#4f46e5', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'
        ],
        borderColor: 'var(--panel)',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 15 } }
      }
    }
  });

  // Business Status Chart
  const businessStatusCtx = document.getElementById('businessStatusChart').getContext('2d');
  new Chart(businessStatusCtx, {
    type: 'bar',
    data: {
      labels: @json($businessStatus->pluck('status')),
      datasets: [{
        label: 'Businesses',
        data: @json($businessStatus->pluck('count')),
        backgroundColor: ['#10b981', '#ef4444'],
        borderRadius: 8,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { display: false } }
      }
    }
  });
</script>
@endsection