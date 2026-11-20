@extends('layouts.app')

@section('title', 'Sự kiện yêu thích — QQQ')

@section('content')
<div class="container-xl py-4">

  <!-- Banner thông tin tài khoản -->
  @if(isset($user) && $user)
  <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3 min-w-0">
      <div class="rounded-3 bg-danger-subtle text-danger fw-bold d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 fs-3" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-heart"></i>
      </div>
      <div class="min-w-0">
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
          <h1 class="fs-5 fw-bold tracking-tight text-dark mb-0 text-break">{{ $user->ho_ten }}</h1>
          <span class="badge rounded-pill {{ $user->vai_tro === 'admin' ? 'bg-danger' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
            {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Thành viên' }}
          </span>
        </div>
        <div class="d-flex flex-wrap gap-2 small text-muted">
          <span class="text-break">{{ $user->email }}</span>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="{{ route('Dashboard.myTicket') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small">
        Xem vé của tôi
      </a>
      <a href="{{ route('Home.index') }}" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium small">
        Khám phá thêm
      </a>
    </div>
  </div>
  @endif

  <!-- Header & Đếm số lượng -->
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
      <h2 class="fs-4 fw-bold text-dark mb-0">Sự kiện yêu thích</h2>
    </div>
    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-semibold flex-shrink-0">
      Đã lưu: <span id="favs-count">{{ count($favorites) }}</span> sự kiện
    </span>
  </div>

  <div class="row g-4" id="favs-grid">
    @forelse ($favorites as $fav)
      @if ($fav->suKien)
        @include('user.card_event', ['event' => $fav->suKien, 'isFav' => true])
      @endif
    @empty
    <div class="col-12 text-center py-5 bg-white rounded-4 border shadow-sm p-4" id="empty-state">
      <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
        <i class="fa-regular fa-heart fs-3"></i>
      </div>
      <h3 class="fs-6 fw-bold text-dark mb-1">Tài khoản này chưa lưu sự kiện nào</h3>
      <p class="small text-muted mb-3">Bấm vào biểu tượng Trái tim trên bất kỳ sự kiện nào để lưu lại danh sách yêu thích trong CSDL.</p>
      <a href="{{ route('Home.index') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 fw-medium">
        Khám phá sự kiện ngay
      </a>
    </div>
    @endforelse
  </div>
</div>
@endsection