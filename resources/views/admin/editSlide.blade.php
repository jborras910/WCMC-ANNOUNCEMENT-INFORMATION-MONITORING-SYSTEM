@extends('admin.index')
@section('title', 'Edit Slide')

@section('content')

@php $extension = pathinfo($slide->file, PATHINFO_EXTENSION); @endphp

@if($errors->any())
<script>
  @foreach($errors->all() as $error)
    swal({ title:"Error!", text:"{{ $error }}", icon:"error" });
  @endforeach
</script>
@endif

<div class="page-card">
  <div class="page-card-header">
    <h5><i class="mdi mdi-pencil mr-2 text-primary"></i>Edit Slide</h5>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
      <i class="mdi mdi-arrow-left mr-1"></i>Back
    </a>
  </div>

  <div class="page-card-body">

    @if(in_array($extension, ['mp4','avi','mov','wmv']))

    <div class="form-section-title">Current Video</div>
    <video id="videoPreview" controls
      style="width:100%;max-height:320px;border-radius:8px;background:#0f0f1a;display:block;margin-bottom:24px;">
      <source src="{{ asset('image_upload/' . $slide->file) }}" type="video/mp4">
      Your browser does not support the video tag.
    </video>

    <form method="post" action="{{ route('slide.updateVideo', ['slide' => $slide]) }}" enctype="multipart/form-data">
      @csrf @method('put')
      <input type="hidden" name="user_add_name" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
      <input type="hidden" name="user_add_email" value="{{ Auth()->user()->email }}">
      <input type="hidden" name="user_add_activity" value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }} edited a slide">

      <div class="form-section-title">Replace Video</div>
      <div class="form-group">
        <label>Upload New Video <span class="text-muted" style="text-transform:none;font-weight:400;">(MP4 only, max 100MB)</span></label>
        <input type="file" name="new_file_name" class="form-control" accept="video/mp4,.mp4" required
          onchange="previewNewVideo(event)">
        <input type="hidden" name="current_file" value="{{ $slide->file }}">
      </div>

      <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
        <button type="submit" class="btn btn-primary">
          <i class="mdi mdi-upload"></i> Update Slide
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
          <i class="mdi mdi-arrow-left"></i> Cancel
        </a>
      </div>
    </form>

    @elseif(in_array($extension, ['jpg','jpeg','png','gif']))

    <form method="post" action="{{ route('slide.update', ['slide' => $slide]) }}" enctype="multipart/form-data">
      @csrf @method('put')
      <div class="form-section-title">Current Image</div>
      <img src="{{ asset('image_upload/' . $slide->file) }}"
        style="width:100%;max-height:300px;object-fit:contain;border-radius:8px;margin-bottom:24px;background:#f8fafc;">

      <div class="form-section-title">Edit Details</div>
      <div class="form-group">
        <label>Replace Image</label>
        <input type="file" name="new_file_name" class="form-control" accept="image/*">
        <input type="hidden" name="current_file" value="{{ $slide->file }}">
      </div>
      <div class="form-group">
        <label>Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ $slide->title }}" required>
      </div>
      <div class="form-group">
        <label>Description <span class="text-danger">*</span></label>
        <textarea name="description" class="form-control" rows="3" required>{{ $slide->description }}</textarea>
      </div>

      <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary mr-2">
          <i class="mdi mdi-content-save mr-1"></i>Save Changes
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>

    @else
    <div class="text-center py-5 text-muted">
      <i class="mdi mdi-file-document-outline" style="font-size:48px;"></i>
      <p class="mt-2">Document preview not available.</p>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    @endif

  </div>
</div>

@endsection

@section('scripts')
<script>
  function previewNewVideo(event) {
    var file = event.target.files[0];
    if (file.size > 100 * 1024 * 1024) {
      Swal.fire({ title:'File too large!', text:'Maximum is 100MB.', icon:'error' });
      event.target.value = '';
      return;
    }
    var video = document.getElementById('videoPreview');
    var source = document.createElement('source');
    source.setAttribute('src', URL.createObjectURL(file));
    source.setAttribute('type', 'video/mp4');
    video.innerHTML = '';
    video.appendChild(source);
    video.load();
  }
</script>
@endsection
