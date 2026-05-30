@extends('admin.index')
@section('title', 'Dashboard')

@section('content')

<style>
  .thumb-wrap {
    width: 88px; height: 58px;
    border-radius: 6px; overflow: hidden;
    position: relative; flex-shrink: 0;
    cursor: pointer; background: #0f0f1a;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    transition: box-shadow 0.18s ease;
  }
  .thumb-wrap:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.35); }
  .thumb-wrap video { width:100%;height:100%;object-fit:cover;display:block;pointer-events:none; }
  .thumb-overlay {
    position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
    background:rgba(0,0,0,0.28);transition:background 0.18s ease;
  }
  .thumb-wrap:hover .thumb-overlay { background:rgba(0,0,0,0.48); }
  .play-icon { font-size:24px;color:#fff;filter:drop-shadow(0 1px 3px rgba(0,0,0,0.6));transition:transform 0.18s ease; }
  .thumb-wrap:hover .play-icon { transform:scale(1.18); }

  .file-name { font-size:13px;font-weight:600;color:#2d3748;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;line-height:1.3; }
  .file-meta { font-size:11px;color:#a0aec0;margin-top:2px; }

  #dataTable tbody tr { transition:background 0.12s ease; }
  #dataTable tbody tr:hover { background:#f7faff !important; }
  #dataTable td { vertical-align:middle !important;padding:10px 12px !important; }
  #dataTable th { padding:10px 12px !important;font-size:12px;letter-spacing:0.05em; }

  .btn-action {
    padding: 5px 12px; font-size: 12px; font-weight: 600;
    border-radius: 5px; border: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 4px;
  }
  .btn-edit   { background:#3a86ff;color:#fff; }
  .btn-edit:hover   { background:#2563eb;color:#fff; }
  .btn-delete { background:#ff4757;color:#fff; }
  .btn-delete:hover { background:#c0392b;color:#fff; }

  .table-card-header {
    background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 100%);
    border-radius:8px 8px 0 0;padding:16px 20px;color:#fff;
  }
  .table-card-header h5 { color:#fff;font-weight:700;margin:0;font-size:16px; }
  .btn-add {
    background:rgba(255,255,255,0.20);border:1.5px solid rgba(255,255,255,0.45);
    color:#fff;font-size:13px;font-weight:600;border-radius:6px;padding:7px 16px;
    transition:background 0.18s ease;text-decoration:none;
    display:inline-flex;align-items:center;gap:4px;
  }
  .btn-add:hover { background:rgba(255,255,255,0.32);color:#fff; }

  .stat-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px; }
  .stat-value { font-size:1.6rem;font-weight:700;line-height:1; }
</style>

@if(session('success'))
<script>
  const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
  Toast.fire({ icon:'success', title:"{{ session('success') }}" });
</script>
@elseif(session('error'))
<script>
  Swal.fire({ title:'Error!', text:"{{ session('error') }}", icon:'error' });
</script>
@endif

{{-- ── Stat Cards ── --}}
@if(Auth()->user()->role === 'master_admin')
<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;overflow:hidden;">
      <div class="card-body p-0">
        <div class="d-flex h-100">
          <div style="width:6px;background:#4e73df;flex-shrink:0;border-radius:10px 0 0 10px;"></div>
          <div class="d-flex align-items-center justify-content-between flex-grow-1 px-4 py-3">
            <div>
              <div class="stat-label text-primary mb-1">Total Slides</div>
              <div class="stat-value">{{ $slideCount }}</div>
            </div>
            <div style="width:48px;height:48px;border-radius:12px;background:#eef2ff;display:flex;align-items:center;justify-content:center;">
              <i class="mdi mdi-filmstrip text-primary" style="font-size:22px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;overflow:hidden;">
      <div class="card-body p-0">
        <div class="d-flex h-100">
          <div style="width:6px;background:#1cc88a;flex-shrink:0;border-radius:10px 0 0 10px;"></div>
          <div class="d-flex align-items-center justify-content-between flex-grow-1 px-4 py-3">
            <div>
              <div class="stat-label text-success mb-1">Total Users</div>
              <div class="stat-value">{{ $userCount }}</div>
            </div>
            <div style="width:48px;height:48px;border-radius:12px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;">
              <i class="mdi mdi-account-multiple text-success" style="font-size:22px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;overflow:hidden;">
      <div class="card-body p-0">
        <div class="d-flex h-100">
          <div style="width:6px;background:#f6c23e;flex-shrink:0;border-radius:10px 0 0 10px;"></div>
          <div class="d-flex align-items-center justify-content-between flex-grow-1 px-4 py-3">
            <div>
              <div class="stat-label text-warning mb-1">Pending</div>
              <div class="stat-value">{{ $slidesPending }}</div>
            </div>
            <div style="width:48px;height:48px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center;">
              <i class="mdi mdi-clock-outline text-warning" style="font-size:22px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100" style="border-radius:10px;overflow:hidden;">
      <div class="card-body p-0">
        <div class="d-flex h-100">
          <div style="width:6px;background:#36b9cc;flex-shrink:0;border-radius:10px 0 0 10px;"></div>
          <div class="d-flex align-items-center justify-content-between flex-grow-1 px-4 py-3">
            <div>
              <div class="stat-label text-info mb-1">Published</div>
              <div class="stat-value">{{ $slidesPublish }}</div>
            </div>
            <div style="width:48px;height:48px;border-radius:12px;background:#ecfeff;display:flex;align-items:center;justify-content:center;">
              <i class="mdi mdi-check-decagram text-info" style="font-size:22px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ── Slide Table ── --}}
<div class="card shadow-sm" style="border:none;border-radius:8px;overflow:hidden;">
  <div class="table-card-header d-flex justify-content-between align-items-center">
    <h5><i class="mdi mdi-filmstrip mr-2"></i>Slides</h5>
    <a href="{{ route('admin.addSlide') }}" class="btn-add">
      <i class="mdi mdi-plus mr-1"></i>Add Slide
    </a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 bg-white" id="dataTable">
        <thead style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
          <tr>
            <th style="width:44px" class="text-center">#</th>
            <th style="width:110px">Preview</th>
            <th>Title</th>
            <th class="text-center" style="width:120px">Status</th>
            <th class="text-center" style="width:150px">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($slides as $index => $slide)
          <tr>
            <td class="text-center text-muted">{{ $index + 1 }}</td>

            <td>
              <div class="thumb-wrap"
                data-video-src="{{ asset('image_upload/' . $slide->file) }}"
                data-video-title="{{ pathinfo($slide->file, PATHINFO_FILENAME) }}">
                <video preload="metadata" muted>
                  <source src="{{ asset('image_upload/' . $slide->file) }}#t=0.5" type="video/mp4">
                </video>
                <div class="thumb-overlay">
                  <i class="mdi mdi-play-circle play-icon"></i>
                </div>
              </div>
            </td>

            <td>
              <div class="file-name" title="{{ $slide->file }}">
                {{ pathinfo($slide->file, PATHINFO_FILENAME) }}
              </div>
              <div class="file-meta">
                <span class="text-uppercase">.{{ pathinfo($slide->file, PATHINFO_EXTENSION) }}</span>
                @if(Auth()->user()->role === 'master_admin' && $slide->department)
                  &middot; <i class="mdi mdi-office-building-outline"></i> {{ $slide->department }}
                @endif
              </div>
            </td>

            <td class="text-center">
              <span class="badge-pill-status
                @if($slide->status === 'pending') s-pending
                @elseif($slide->status === 'published') s-published
                @else s-rejected @endif">
                {{ ucfirst($slide->status) }}
              </span>
            </td>

            <td class="text-center" style="white-space:nowrap;">
              <a class="btn btn-action btn-edit mr-1"
                href="{{ route('slide.edit', ['slide' => $slide]) }}">
                <i class="mdi mdi-pencil mr-1"></i>Edit
              </a>
              <button type="button" class="btn btn-action btn-delete"
                data-delete-url="{{ route('deleteSlide.destroy', ['slide' => $slide]) }}"
                data-delete-name="{{ pathinfo($slide->file, PATHINFO_FILENAME) }}">
                <i class="mdi mdi-delete mr-1"></i>Delete
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ── Shared Preview Modal (outside table) ── --}}
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
      <div class="modal-header" style="background:#1e293b;padding:14px 20px;border:none;">
        <div class="d-flex align-items-center" style="min-width:0;">
          <i class="mdi mdi-filmstrip mr-2" style="color:#60a5fa;font-size:18px;flex-shrink:0;"></i>
          <span id="previewModalTitleText" style="color:#f1f5f9;font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:380px;"></span>
        </div>
        <button id="closePreviewModal" type="button"
          style="background:rgba(255,255,255,0.10);border:none;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#94a3b8;font-size:18px;flex-shrink:0;margin-left:12px;transition:background 0.18s,color 0.18s;"
          onmouseover="this.style.background='rgba(255,255,255,0.22)';this.style.color='#fff'"
          onmouseout="this.style.background='rgba(255,255,255,0.10)';this.style.color='#94a3b8'">
          <i class="mdi mdi-close"></i>
        </button>
      </div>
      <div class="modal-body p-0" style="background:#000;">
        <video id="previewVideo" style="width:100%;display:block;max-height:70vh;" controls>
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </div>
</div>

{{-- ── Shared Delete Modal (outside table) ── --}}
<form method="post" id="deleteSlideForm" action="">
  @csrf @method('delete')
  <div class="modal fade" id="deleteSlideModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
      <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
        <div class="modal-header" style="background:#fff;border-bottom:1px solid #f1f5f9;padding:16px 20px;">
          <div class="d-flex align-items-center">
            <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
              <i class="mdi mdi-delete" style="color:#dc2626;font-size:18px;"></i>
            </div>
            <div>
              <div style="font-weight:700;font-size:14px;color:#1e293b;">Delete Slide</div>
              <div style="font-size:12px;color:#94a3b8;">This action cannot be undone</div>
            </div>
          </div>
          <button id="closeDeleteModal" type="button"
            style="background:none;border:none;border-radius:6px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#94a3b8;font-size:16px;margin-left:auto;transition:background 0.18s;"
            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
            <i class="mdi mdi-close"></i>
          </button>
        </div>
        <div class="modal-body" style="padding:20px;font-size:14px;color:#475569;">
          Are you sure you want to delete <strong id="deleteFileName" style="color:#1e293b;"></strong>?
        </div>
        <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:12px 20px;gap:8px;justify-content:flex-end;">
          <button type="button" id="cancelDeleteBtn" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="mdi mdi-delete mr-1"></i>Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
  $('#dataTable').DataTable({ responsive: true, pageLength: 10 });
});

// ── Preview: click thumbnail ──
$(document).on('click', '.thumb-wrap', function () {
  var src   = $(this).data('video-src');
  var title = $(this).data('video-title');
  $('#previewModalTitleText').text(title);
  var video = document.getElementById('previewVideo');
  video.src = src;
  video.load();
  $('#previewModal').modal('show');
});

$('#previewModal').on('shown.bs.modal', function () {
  var video = document.getElementById('previewVideo');
  var p = video.play();
  if (p !== undefined) { p.catch(function () {}); }
});

// Close preview modal
$('#closePreviewModal').on('click', function () {
  $('#previewModal').modal('hide');
});

$('#previewModal').on('hidden.bs.modal', function () {
  var video = document.getElementById('previewVideo');
  video.pause();
  video.removeAttribute('src');
  video.load();
});

// ── Delete: click delete button ──
$(document).on('click', '.btn-delete', function () {
  var url  = $(this).data('delete-url');
  var name = $(this).data('delete-name');
  $('#deleteSlideForm').attr('action', url);
  $('#deleteFileName').text(name);
  $('#deleteSlideModal').modal('show');
});

// Close delete modal
$('#closeDeleteModal, #cancelDeleteBtn').on('click', function () {
  $('#deleteSlideModal').modal('hide');
});
</script>
@endsection
