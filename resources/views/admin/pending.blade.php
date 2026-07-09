@extends('admin.index')
@section('title', 'Pending Slides')

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
  #dataTable td { vertical-align:middle !important;padding:10px 12px !important; }
  #dataTable th { padding:10px 12px !important;font-size:12px;letter-spacing:0.05em; }
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

<div class="card shadow-sm" style="border:none;border-radius:8px;overflow:hidden;">
  <div class="d-flex justify-content-between align-items-center px-4 py-3"
    style="background:linear-gradient(135deg,#7b4f00 0%,#f6a623 100%);border-radius:8px 8px 0 0;">
    <h5 class="mb-0 font-weight-bold text-white">
      <i class="mdi mdi-clock-outline mr-2"></i>Pending Slides
    </h5>
    <span class="badge badge-light" style="font-size:13px;padding:6px 14px;">
      {{ $slides->count() }} pending
    </span>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 bg-white" id="dataTable">
        <thead style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
          <tr>
            <th style="width:44px" class="text-center">#</th>
            <th style="width:110px">Preview</th>
            <th>Title</th>
            <th>Department</th>
            <th class="text-center" style="width:110px">Status</th>
            <th class="text-center" style="width:180px">Action</th>
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
              </div>
            </td>

            <td>{{ $slide->department ?? '—' }}</td>

            <td class="text-center">
              <span class="badge-pill-status s-pending">Pending</span>
            </td>

            <td class="text-center" style="white-space:nowrap;">
              <button type="button" class="btn btn-sm btn-success text-white mr-1 btn-approve"
                data-approve-url="{{ route('slide.publishFile', ['slide' => $slide]) }}"
                data-slide-name="{{ pathinfo($slide->file, PATHINFO_FILENAME) }}"
                data-department="{{ $slide->department ?? '—' }}">
                <i class="mdi mdi-check mr-1"></i>Approve
              </button>
              <button type="button" class="btn btn-sm btn-danger text-white btn-reject"
                data-reject-url="{{ route('slide.reject', ['slide' => $slide]) }}"
                data-slide-name="{{ pathinfo($slide->file, PATHINFO_FILENAME) }}"
                data-department="{{ $slide->department ?? '—' }}">
                <i class="mdi mdi-close mr-1"></i>Reject
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
          <span id="previewModalTitle" style="color:#f1f5f9;font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:380px;"></span>
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

{{-- ── Shared Approve Modal (outside table) ── --}}
<form method="post" id="approveForm" action="">
  @csrf @method('put')
  <input type="hidden" name="user_add_name" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
  <input type="hidden" name="user_add_email" value="{{ Auth()->user()->email }}">
  <input type="hidden" name="user_add_activity" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }} published a slide">
  <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-success">
            <i class="mdi mdi-check-circle mr-1"></i>Approve Slide
          </h6>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          Publish <strong id="approveFileName"></strong>?
          <p class="small text-muted mt-1 mb-0">Department: <span id="approveDept"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm text-white" id="approveSubmitBtn">
            <i class="mdi mdi-check mr-1"></i>Publish
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- ── Shared Reject Modal (outside table) ── --}}
<form method="post" id="rejectForm" action="">
  @csrf @method('put')
  <input type="hidden" name="user_add_name" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
  <input type="hidden" name="user_add_email" value="{{ Auth()->user()->email }}">
  <input type="hidden" name="user_add_activity" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }} rejected a slide">
  <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-danger">
            <i class="mdi mdi-close-circle mr-1"></i>Reject Slide
          </h6>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          Reject <strong id="rejectFileName"></strong>?
          <p class="small text-muted mt-1 mb-0">Department: <span id="rejectDept"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm text-white" id="rejectSubmitBtn">
            <i class="mdi mdi-close mr-1"></i>Reject
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
  $('#dataTable').DataTable({ responsive: true });
});

// ── Preview ──
$(document).on('click', '.thumb-wrap', function () {
  var src   = $(this).data('video-src');
  var title = $(this).data('video-title');
  $('#previewModalTitle').text(title);
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

$('#previewModal').on('hidden.bs.modal', function () {
  var video = document.getElementById('previewVideo');
  video.pause();
  video.removeAttribute('src');
  video.load();
});

// ── Approve ──
$(document).on('click', '.btn-approve', function () {
  var url  = $(this).data('approve-url');
  var name = $(this).data('slide-name');
  var dept = $(this).data('department');
  $('#approveForm').attr('action', url);
  $('#approveFileName').text(name);
  $('#approveDept').text(dept);
  $('#approveModal').modal('show');
});

// ── Reject ──
$(document).on('click', '.btn-reject', function () {
  var url  = $(this).data('reject-url');
  var name = $(this).data('slide-name');
  var dept = $(this).data('department');
  $('#rejectForm').attr('action', url);
  $('#rejectFileName').text(name);
  $('#rejectDept').text(dept);
  $('#rejectModal').modal('show');
});

// Show a spinner on the modal's submit button so staff get feedback instead
// of wondering if their click registered, and can't double-submit.
function showButtonSpinner(button, label) {
  button.prop('disabled', true);
  button.html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> ' + label);
}

$('#approveForm').on('submit', function () {
  showButtonSpinner($('#approveSubmitBtn'), 'Publishing…');
});

$('#rejectForm').on('submit', function () {
  showButtonSpinner($('#rejectSubmitBtn'), 'Rejecting…');
});
</script>
@endsection
