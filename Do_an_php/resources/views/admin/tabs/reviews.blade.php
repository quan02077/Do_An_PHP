<div id="adm-view-reviews" class="d-none">
    <form method="GET" action="{{ route('admin.index') }}" class="row g-2 mb-3">
        <input type="hidden" name="tab" value="reviews">
        <div class="col-md-6">
            <select name="review_event" class="form-select form-select-sm py-2" aria-label="Lọc đánh giá theo sự kiện">
                <option value="">Tất cả sự kiện</option>
                @foreach($allEventsForFilter ?? [] as $ev)
                    <option value="{{ $ev->id }}" {{ (string) request('review_event') === (string) $ev->id ? 'selected' : '' }}>
                        {{ $ev->ten_su_kien }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6 col-md-3">
            <select name="review_rating" class="form-select form-select-sm py-2" aria-label="Lọc theo số sao">
                <option value="">Tất cả số sao</option>
                @for($star = 5; $star >= 1; $star--)
                    <option value="{{ $star }}" {{ (string) request('review_rating') === (string) $star ? 'selected' : '' }}>
                        {{ $star }} sao
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-sm-6 col-md-3 d-flex gap-1">
            <button class="btn btn-sm btn-dark flex-grow-1 py-2" type="submit">Lọc</button>
            @if(request()->filled('review_event') || request()->filled('review_rating'))
                <a href="{{ route('admin.index', ['tab' => 'reviews']) }}" class="btn btn-sm btn-outline-secondary py-2" title="Xóa bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-secondary text-uppercase small">
                <tr>
                    <th class="py-3 px-3">Sự kiện</th>
                    <th class="py-3 px-3">Người đánh giá</th>
                    <th class="py-3 px-3 text-center">Số sao</th>
                    <th class="py-3 px-3">Nội dung</th>
                    <th class="py-3 px-3">Trạng thái</th>
                    <th class="py-3 px-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody id="admin-reviews-tbody">
                @forelse($reviews ?? [] as $review)
                @php
                    $isVisible = $review->trang_thai === 'hien';
                @endphp
                <tr>
                    <td class="py-3 px-3 fw-semibold text-dark text-truncate" style="max-width: 220px;">
                        {{ $review->suKien->ten_su_kien ?? 'Sự kiện #' . $review->su_kien_id }}
                    </td>
                    <td class="py-3 px-3 text-secondary">
                        <div>{{ $review->nguoiDung->ho_ten ?? 'Thành viên' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ optional($review->created_at)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="py-3 px-3 text-center text-nowrap">
                        <span class="text-warning fw-semibold">★ {{ $review->so_sao }}/5</span>
                    </td>
                    <td class="py-3 px-3 text-secondary text-break" style="max-width: 280px;">
                        {{ $review->noi_dung ?: '—' }}
                    </td>
                    <td class="py-3 px-3">
                        @if($isVisible)
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đang hiện</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Đã ẩn</span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-end">
                        <form method="POST" action="{{ route('admin.reviews.visibility', $review->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm {{ $isVisible ? 'btn-outline-secondary' : 'btn-outline-success' }} py-1 px-2 rounded-2">
                                <i class="fa-regular {{ $isVisible ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>{{ $isVisible ? 'Ẩn' : 'Hiện' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        Chưa có đánh giá nào phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($reviews) && $reviews->hasPages())
        <div class="mt-3">{{ $reviews->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
