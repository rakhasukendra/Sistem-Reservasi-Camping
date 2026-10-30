@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:48px">
    <div class="page-head">
        <p class="eyebrow">Ringkasan operasional</p>
        <h1 class="section-title">Dashboard</h1>
        <p class="muted" style="margin-top:8px">Grafik reservasi dan pemakaian stok alat.</p>
    </div>

    <section class="analytics-section">
        <div class="row mobile-stack">
            <div>
                <h2 class="analytics-title">Tren reservasi dan pembatalan</h2>
                <p class="small-note">Jumlah dan pendapatan reservasi yang disetujui berdasarkan tanggal sewa.</p>
            </div>
            <form method="get" action="{{ route('staff.analytics') }}">
                <label class="small-note" for="period">Periode</label>
                <select id="period" name="period" onchange="this.form.submit()" style="min-height:44px;padding:7px;border:1px solid var(--line);border-radius:5px">
                    <option value="month" @selected($period === 'month')>Bulan ini</option>
                    <option value="year" @selected($period === 'year')>Tahun ini</option>
                </select>
            </form>
        </div>
        @if ($daily->isEmpty())
            <div class="panel empty">Belum ada reservasi pada periode ini. Grafik akan terisi setelah reservasi dibuat.</div>
        @else
            <div class="panel" style="padding:30px">
                <canvas id="reservationChart" style="max-height:400px"></canvas>
            </div>
        @endif
    </section>

    <section class="analytics-section">
        <h2 class="analytics-title">Pemakaian stok alat selama periode</h2>
        <p class="small-note">Persentase unit-hari yang dipesan dibagi stok siap sewa dan lama periode.</p>
        @if ($utilization->isEmpty())
            <div class="panel empty">Belum ada alat di katalog untuk ditampilkan.</div>
        @else
            <div class="panel" style="padding:30px">
                <canvas id="utilizationChart" style="max-height:400px"></canvas>
            </div>
        @endif
    </section>

    @if (auth()->user()->role === 'admin')
        <section class="analytics-section">
            <h2 class="analytics-title">Riwayat audit</h2>
            <p class="small-note">30 tindakan terakhir yang tercatat di sistem.</p>
            @if ($logs->isEmpty())
                <div class="panel empty">Belum ada catatan tindakan.</div>
            @else
                <div class="table-wrap panel" style="overflow-x:auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pelaku</th>
                                <th>Tindakan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td style="white-space:nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->actor?->email ?? '—' }}</td>
                                    <td style="font-family:monospace;font-size:12px;color:var(--muted)">{{ $log->action }}</td>
                                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis">{{ $log->details }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<style>
.analytics-section{padding:40px 0;border-top:2px solid var(--line)}
.analytics-title{font:700 24px Manrope,sans-serif;margin:0;color:var(--forest-dark)}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
if (typeof Chart === 'undefined') { return; }
@if(!$daily->isEmpty())
const reservationCtx = document.getElementById('reservationChart').getContext('2d');
const totalReservations = [@foreach($daily as $row){{ $row->booking_count }},@endforeach].reduce((a, b) => a + b, 0);
const totalCancellations = [@foreach($daily as $row){{ $row->cancellation_count }},@endforeach].reduce((a, b) => a + b, 0);
new Chart(reservationCtx, {
    type: 'pie',
    data: {
        labels: ['Reservasi', 'Pembatalan'],
        datasets: [{
            data: [totalReservations, totalCancellations],
            backgroundColor: [
                'rgba(74, 154, 101, 0.8)',
                'rgba(220, 38, 38, 0.8)'
            ],
            borderColor: [
                'rgba(47, 82, 51, 1)',
                'rgba(185, 28, 28, 1)'
            ],
            borderWidth: 2,
            hoverBackgroundColor: [
                'rgba(74, 154, 101, 1)',
                'rgba(220, 38, 38, 1)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: {
                        family: 'DM Sans',
                        size: 13,
                        weight: '600'
                    },
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(31, 41, 55, 0.95)',
                titleFont: {
                    family: 'Manrope',
                    size: 14,
                    weight: '700'
                },
                bodyFont: {
                    family: 'DM Sans',
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
@endif

@if(!$utilization->isEmpty())
const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
new Chart(utilizationCtx, {
    type: 'bar',
    data: {
        labels: [@foreach($utilization as $row)'{{ $row["item"]->name }}',@endforeach],
        datasets: [{
            label: 'Pemakaian Stok (%)',
            data: [@foreach($utilization as $row){{ $row["percent"] }},@endforeach],
            backgroundColor: [
                @foreach($utilization as $row)
                '{{ $row["percent"] >= 80 ? "rgba(220, 38, 38, 0.8)" : ($row["percent"] >= 50 ? "rgba(251, 146, 60, 0.8)" : "rgba(74, 154, 101, 0.8)") }}',
                @endforeach
            ],
            borderColor: [
                @foreach($utilization as $row)
                '{{ $row["percent"] >= 80 ? "rgba(185, 28, 28, 1)" : ($row["percent"] >= 50 ? "rgba(234, 88, 12, 1)" : "rgba(47, 82, 51, 1)") }}',
                @endforeach
            ],
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(31, 41, 55, 0.95)',
                titleFont: {
                    family: 'Manrope',
                    size: 14,
                    weight: '700'
                },
                bodyFont: {
                    family: 'DM Sans',
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    afterBody: function(context) {
                        const idx = context[0].dataIndex;
                        const unitDays = [@foreach($utilization as $row){{ $row["unit_days"] }},@endforeach];
                        return '\nUnit-hari dipesan: ' + unitDays[idx];
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    font: {
                        family: 'DM Sans',
                        size: 12
                    },
                    callback: function(value) {
                        return value + '%';
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            y: {
                ticks: {
                    font: {
                        family: 'DM Sans',
                        size: 12,
                        weight: '600'
                    }
                },
                grid: {
                    display: false
                }
            }
        }
    }
});
@endif
});
</script>
@endpush
