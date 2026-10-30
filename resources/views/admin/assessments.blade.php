@extends('layouts.app')
@section('title', 'Penilaian Staff')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px;max-width:1100px">
<div class="page-head">
    <p class="eyebrow">Admin</p>
    <h1 class="section-title">Penilaian Tindakan Staff</h1>
    <p class="muted" style="margin-top:12px">Berikan rating dan catatan penilaian untuk performa staff.</p>
</div>

<div style="margin:30px 0;padding:20px;background:var(--sand);border-radius:10px;display:flex;align-items:center;justify-content:space-between;gap:16px">
    <p style="font:600 16px Manrope,sans-serif;margin:0;color:var(--forest-dark)">Filter periode penilaian</p>
    <div>
        <input id="filter-month-global" type="month" value="{{ $month }}" onchange="window.location.href='{{ route('admin.assessments') }}?month='+this.value" style="padding:10px 14px;border:1px solid var(--line);border-radius:6px;font-size:15px;font-weight:600">
    </div>
</div>

<section style="padding:30px 0">
    <h2 style="font:700 21px Manrope,sans-serif;margin:0 0 8px">Staff aktif - {{ \Carbon\Carbon::create($year, $monthNum)->locale('id')->isoFormat('MMMM YYYY') }}</h2>
    <p class="small-note" style="margin:0 0 20px">Berikan penilaian untuk bulan ini</p>
    
    @if($staffUsers->isEmpty())
        <div class="panel empty" style="padding:40px 24px">
            <p style="margin:0;color:var(--muted)">Belum ada staff untuk dinilai.</p>
        </div>
    @else
        <div class="stack">
            @foreach($staffUsers as $staff)
                <div class="panel" style="padding:0;border:1px solid var(--line);border-radius:8px;overflow:hidden">
                    <div style="padding:18px 24px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line)">
                        <div style="flex:1">
                            <p style="font:600 16px Manrope,sans-serif;margin:0 0 4px;color:var(--forest-dark)">{{ $staff->name }}</p>
                            <p class="small-note" style="margin:0">{{ $staff->email }}</p>
                            @if($staff->current_rating)
                                <p style="margin:6px 0 0;font-size:13px;font-weight:600;color:var(--forest)">
                                    Rating bulan ini: 
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="color:{{ $i <= $staff->current_rating ? '#f59e0b' : '#d1d5db' }}">★</span>
                                    @endfor
                                    ({{ $staff->current_rating }}/5)
                                </p>
                            @else
                                <p style="margin:6px 0 0;font-size:13px;color:var(--muted)">Belum ada penilaian bulan ini</p>
                            @endif
                        </div>
                        <button type="button" onclick="toggleStaffAssessment('assessment-{{ $staff->id }}')" class="button secondary small">Nilai</button>
                    </div>
                    
                    <form id="assessment-{{ $staff->id }}" class="form-grid" method="post" action="{{ route('admin.staff.assess', $staff) }}" style="display:none;margin:0;border:none;box-shadow:none;border-top:1px solid var(--line);border-radius:0;padding:24px;gap:16px">
                        @csrf
                        <div class="field">
                            <label for="rating-{{ $staff->id }}">Rating kinerja (1-5)</label>
                            <select id="rating-{{ $staff->id }}" name="rating" required style="padding:8px 12px;border:1px solid var(--line);border-radius:4px">
                                <option value="">Pilih rating</option>
                                <option value="1">1 - Sangat kurang</option>
                                <option value="2">2 - Kurang</option>
                                <option value="3">3 - Cukup</option>
                                <option value="4">4 - Baik</option>
                                <option value="5">5 - Sangat baik</option>
                            </select>
                        </div>
                        <div class="field" style="grid-column:1/-1">
                            <label for="notes-{{ $staff->id }}">Catatan penilaian</label>
                            <textarea id="notes-{{ $staff->id }}" name="assessment_notes" maxlength="1000" style="padding:8px 12px;border:1px solid var(--line);border-radius:4px;width:100%;min-height:80px" placeholder="Tulis penilaian, feedback, atau catatan performa staff..."></textarea>
                        </div>
                        <div style="display:flex;gap:8px">
                            <button class="button secondary small" type="submit">Simpan penilaian</button>
                            <button type="button" onclick="toggleStaffAssessment('assessment-{{ $staff->id }}')" class="button secondary small" style="background:#e7e5e4;color:var(--ink)">Batal</button>
                        </div>
                    </form>
                    
                    @if($staff->assessments_this_month->isNotEmpty())
                        <div style="padding:18px 24px;background:var(--sand)">
                            <p style="font:600 13px Manrope,sans-serif;margin:0 0 12px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px">Riwayat penilaian bulan ini ({{ $staff->assessments_this_month->count() }})</p>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                @foreach($staff->assessments_this_month as $assessment)
                                    <div style="padding:8px 12px;background:white;border-radius:4px;font-size:12px">
                                        <div style="display:flex;justify-content:space-between;align-items:center">
                                            <div>
                                                <p style="margin:0 0 4px;font-weight:600;color:var(--forest-dark)">{{ $assessment->created_at->format('d/m/Y H:i') }}</p>
                                                <p style="margin:0;color:var(--muted)">{{ $assessment->details }}</p>
                                            </div>
                                            <div style="display:flex;gap:6px">
                                                <button type="button" onclick="toggleEditAssessment('edit-{{ $assessment->id }}')" style="padding:4px 8px;border:none;background:#10b981;color:white;border-radius:4px;font-size:11px;cursor:pointer">Edit</button>
                                                <form method="post" action="{{ route('admin.assessment.delete', $assessment) }}" onsubmit="return confirm('Hapus penilaian ini?')" style="margin:0">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" style="padding:4px 8px;border:none;background:#dc2626;color:white;border-radius:4px;font-size:11px;cursor:pointer">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                        <form id="edit-{{ $assessment->id }}" method="post" action="{{ route('admin.assessment.update', $assessment) }}" style="display:none;margin-top:8px;padding:8px;background:#f3f4f6;border-radius:4px">
                                            @csrf @method('PUT')
                                            <div style="display:flex;gap:8px;align-items:center">
                                                <select name="rating" required style="padding:4px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:12px">
                                                    <option value="">Pilih rating</option>
                                                    <option value="1">1 - Sangat kurang</option>
                                                    <option value="2">2 - Kurang</option>
                                                    <option value="3">3 - Cukup</option>
                                                    <option value="4">4 - Baik</option>
                                                    <option value="5">5 - Sangat baik</option>
                                                </select>
                                                <textarea name="assessment_notes" maxlength="1000" style="padding:4px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:12px;min-width:200px;min-height:32px"></textarea>
                                                <button type="submit" style="padding:4px 8px;border:none;background:#10b981;color:white;border-radius:4px;font-size:11px;cursor:pointer">Simpan</button>
                                                <button type="button" onclick="toggleEditAssessment('edit-{{ $assessment->id }}')" style="padding:4px 8px;border:none;background:#9ca3af;color:white;border-radius:4px;font-size:11px;cursor:pointer">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>



</div>

@push('scripts')
<script>
function toggleStaffAssessment(formId) {
    const form = document.getElementById(formId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function toggleEditAssessment(formId) {
    const form = document.getElementById(formId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>
@endpush
@endsection
