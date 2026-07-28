@extends('admin.index')
@section('title', 'Add Slide')

@section('content')

<style>
  #videoPreview {
    display: none;
    width: 100%;
    max-height: 300px;
    margin-top: 12px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }
</style>

<div class="card shadow-sm">
  <div class="card-body">

    <!-- User Guide Modal -->
    <div class="modal fade" id="userGuideModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title"><i class="mdi mdi-information-outline mr-1 text-info"></i>How to convert to MP4</h6>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <ol class="mb-0">
              <li>Open your PowerPoint presentation</li>
              <li>Insert your images, documents, or PDF content</li>
              <li>Go to <strong>File → Export → Create a Video</strong></li>
              <li>Save as <strong>.mp4</strong> (MPEG-4)</li>
              <li>Upload the exported file here</li>
            </ol>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Got it</button>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 font-weight-bold">
        <i class="mdi mdi-plus-circle mr-1 text-primary"></i> Add New Slide
      </h5>
      <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#userGuideModal">
        <i class="mdi mdi-help-circle mr-1"></i>User Guide
      </button>
    </div>
    <hr>

    <form method="post" action="{{ route('addVideoslide.post') }}" enctype="multipart/form-data" id="addSlideForm">
      @if($errors->any())
        <script>
          @foreach($errors->all() as $error)
            swal({ title: "Error!", text: "{{ $error }}", icon: "error" });
          @endforeach
        </script>
      @endif
      @csrf

      <div class="form-group">
        <label class="font-weight-semibold">Upload Video <span class="text-muted small">(MP4 only, max 100MB)</span></label>
        <input name="file_name" type="file" class="form-control" accept="video/mp4,.mp4" required onchange="previewVideo(event)">
      </div>

      <video id="videoPreview" controls>
        Your browser does not support the video tag.
      </video>

      @if(isset($departments))
        <div class="form-group mt-3">
          <label>Department <span class="text-danger">*</span></label>
          <select name="department_id" class="form-control" required>
            <option value="">Select a department&hellip;</option>
            @foreach($departments as $department)
              <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
          </select>
        </div>
      @elseif(Auth()->user()->department)
        <div class="form-group mt-3 mb-0">
          <label>Department</label>
          <div>
            <span class="badge badge-secondary" style="font-size:12px;padding:6px 12px;">
              <i class="mdi mdi-domain mr-1"></i>{{ Auth()->user()->department->name }}
            </span>
            <small class="text-muted d-block mt-1">This slide will be uploaded under your department.</small>
          </div>
        </div>
      @else
        <div class="alert alert-danger mt-3 mb-0">
          Your account doesn't have a department assigned. Ask a master admin to set one
          on your profile before you can upload slides.
        </div>
      @endif

      <input type="hidden" name="added_by_email" value="{{ Auth()->user()->email }}">
      <input type="hidden" name="user_add_name" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
      <input type="hidden" name="user_add_email" value="{{ Auth()->user()->email }}">
      <input type="hidden" name="user_add_activity" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }} Added Slide">

      <div style="display:flex;align-items:center;gap:10px;margin-top:16px;">
        <button type="submit" class="btn btn-primary" id="addSlideBtn">
          <i class="mdi mdi-upload"></i> Upload Slide
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
          <i class="mdi mdi-arrow-left"></i> Back
        </a>
      </div>
    </form>

  </div>
</div>

@endsection

@section('scripts')
<script>
  function previewVideo(event) {
    var file = event.target.files[0];
    var maxSize = 100 * 1024 * 1024;

    if (file.size > maxSize) {
      Swal.fire({ title: 'File too large!', text: 'Maximum file size is 100MB.', icon: 'error' });
      event.target.value = '';
      return;
    }

    var video = document.getElementById('videoPreview');
    video.src = URL.createObjectURL(file);
    video.style.display = 'block';
    video.load();
  }

  // Video uploads can take a while — disable the button and show a spinner so
  // staff get feedback instead of wondering if their click registered (and
  // can't accidentally double-submit while it's still uploading).
  document.getElementById('addSlideForm').addEventListener('submit', function() {
    var btn = document.getElementById('addSlideBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Uploading…';
  });
</script>
@endsection
