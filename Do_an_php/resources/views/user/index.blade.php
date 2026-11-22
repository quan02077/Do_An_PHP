@extends('layouts.app')

@section('title', 'QQQ — Khám phá & Đặt vé sự kiện Dễ dàng, Nhanh chóng')

@section('content')
@php
$categories = $categories ?? collect();
$events = $events ?? collect();
@endphp

<section class="bg-white border-bottom py-4 py-sm-5 w-100">
  <div class="container-xl">
    <div class="mx-auto text-center" style="max-width: 720px;">
      <h1 class="display-6 fw-bold tracking-tight text-dark mb-3">
        Khám phá &amp; Đặt vé sự kiện <br class="d-none d-sm-inline" />
        <span class="text-secondary fw-normal d-block d-sm-inline mt-1 mt-sm-0">Dễ dàng, Nhanh chóng</span>
      </h1>
      <div class="mx-auto position-relative" style="max-width: 560px;">
        <form method="GET" action="{{ route('Home.index') }}" id="event-search-form">
          @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
          @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
          <div class="input-group shadow-sm rounded-3 overflow-hidden">
            <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
              <i class="fa-solid fa-magnifying-glass text-secondary"></i>
            </span>
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" aria-label="Tìm kiếm sự kiện" placeholder="Tìm theo tên sự kiện, địa điểm, chủ đề..." class="form-control border-start-0 ps-1 py-2.5 fs-6" />
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="container-xl py-4 py-sm-5">
  <div class="mb-4">
    <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 flex-nowrap scrollbar-none" id="category-pills-container">
      <a
        href="{{ route('Home.index', request()->only('search', 'status')) }}"
        class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium text-nowrap flex-shrink-0 {{ !request('category') ? 'btn-dark shadow-sm' : 'btn-outline-secondary' }}">
        Tất cả
      </a>

      @foreach ($categories as $cat)
      <a
        href="{{ route('Home.index', array_merge(request()->only('search', 'status'), ['category' => $cat->id])) }}"
        class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium text-nowrap flex-shrink-0 {{ request('category') == $cat->id ? 'btn-dark shadow-sm' : 'btn-outline-secondary' }}">
        {{ $cat->ten_danh_muc }}
      </a>
      @endforeach
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pt-3 border-top mt-2">
      <!-- Status filter wrapper: ngang và cuộn mượt không vỡ layout trên mobile -->
      <div class="overflow-x-auto w-100 pb-1 scrollbar-none" style="-webkit-overflow-scrolling: touch;">
        <div class="btn-group btn-group-sm rounded-pill p-1 bg-white border shadow-sm flex-nowrap d-inline-flex" role="group">
          <a
            href="{{ route('Home.index', request()->only('search', 'category')) }}"
            class="status-filter-tab btn btn-sm rounded-pill px-3 text-nowrap flex-shrink-0 {{ !request('status') ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
            Tất cả
          </a>
          <a
            href="{{ route('Home.index', array_merge(request()->only('search', 'category'), ['status' => 'sap_dien_ra'])) }}"
            class="status-filter-tab btn btn-sm rounded-pill px-3 text-nowrap flex-shrink-0 {{ request('status') === 'sap_dien_ra' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
            Sắp diễn ra
          </a>
          <a
            href="{{ route('Home.index', array_merge(request()->only('search', 'category'), ['status' => 'dang_dien_ra'])) }}"
            class="status-filter-tab btn btn-sm rounded-pill px-3 text-nowrap flex-shrink-0 {{ request('status') === 'dang_dien_ra' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
            Đang diễn ra
          </a>
          <a
            href="{{ route('Home.index', array_merge(request()->only('search', 'category'), ['status' => 'da_ket_thuc'])) }}"
            class="status-filter-tab btn btn-sm rounded-pill px-3 text-nowrap flex-shrink-0 {{ request('status') === 'da_ket_thuc' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
            Đã kết thúc
          </a>
          <a
            href="{{ route('Home.index', array_merge(request()->only('search', 'category'), ['status' => 'yeu_thich'])) }}"
            class="status-filter-tab btn btn-sm rounded-pill px-3 text-nowrap flex-shrink-0 {{ request('status') === 'yeu_thich' ? 'btn-dark active' : 'btn-outline-secondary border-0' }} d-flex align-items-center gap-1">
            <i class="fa-solid fa-heart text-danger"></i>
            <span>Yêu thích</span>
          </a>
        </div>
      </div>

      <div id="events-count-label" class="small fw-semibold text-secondary flex-shrink-0 align-self-start align-self-sm-center text-nowrap">
        Hiển thị {{ $events->count() }} / {{ $events->total() }} sự kiện
      </div>
      </div>
    </div>
  </div>

  <div id="events-grid-container" class="row g-4">
    @forelse ($events as $event)
        @include('user.card_event', ['event' => $event, 'isFav' => in_array($event->id, $userFavIds ?? [])])
    @empty
    <div class="col-12 text-center py-5 px-3 bg-white rounded-4 border shadow-sm my-3">
      <div class="mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-magnifying-glass fs-4 text-muted"></i>
      </div>
      <h3 class="fs-5 fw-bold text-dark mb-1">Không tìm thấy sự kiện nào</h3>
      <p class="small text-secondary mb-3">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bớt bộ lọc.</p>
      <div>
        <a href="{{ url('/') }}" class="btn btn-sm btn-dark px-3 py-2 fw-medium rounded-pill">
          Đặt lại bộ lọc
        </a>
      </div>
    </div>
    @endforelse
  </div>
  <div id="events-pagination">
    @if($events->hasPages())
      <div class="d-flex justify-content-center mt-4">{{ $events->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
</section>

@endsection

@push('scripts')
<script>
(() => {
  const form = document.getElementById('event-search-form');
  const input = document.getElementById('search-input');
  if (!form || !input) return;

  let timer;
  let controller;
  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(async () => {
      controller?.abort();
      controller = new AbortController();
      const url = new URL(form.action);
      url.search = new URLSearchParams(new FormData(form)).toString();
      try {
        const response = await fetch(url, {signal: controller.signal, headers: {'X-Requested-With': 'XMLHttpRequest'}});
        if (!response.ok) return;
        const documentResult = new DOMParser().parseFromString(await response.text(), 'text/html');
        for (const id of ['events-grid-container', 'events-count-label', 'events-pagination']) {
          const current = document.getElementById(id);
          const updated = documentResult.getElementById(id);
          if (current && updated) current.innerHTML = updated.innerHTML;
        }
        history.replaceState({}, '', url);
      } catch (error) {
        if (error.name !== 'AbortError') console.error('Không thể tìm sự kiện:', error);
      }
    }, 300);
  });
})();
</script>
@endpush
