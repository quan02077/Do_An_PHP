<script>
    (function () {
        const draftBtn = document.getElementById('ai-draft-btn');
        const draftSpinner = document.getElementById('ai-draft-spinner');
        const draftBtnLabel = document.getElementById('ai-draft-btn-label');
        const draftInput = document.getElementById('ai-draft-input');
        const draftMessage = document.getElementById('ai-draft-message');
        const addEventForm = document.getElementById('add-event-form');

        if (!draftBtn || !draftInput || !draftMessage || !addEventForm) {
            return;
        }

        const aiDraftUrl = "{{ route('admin.event.ai-draft') }}";
        const fieldMap = {
            ten_su_kien: 'new-ev-title',
            danh_muc_id: 'new-ev-category',
            so_luong_toi_da: 'new-ev-capacity',
            thoi_gian_bat_dau: 'new-ev-start',
            thoi_gian_ket_thuc: 'new-ev-end',
            dia_diem: 'new-ev-location',
            gia_ve: 'new-ev-price',
            mo_ta: 'new-ev-desc',
        };

        function showDraftMessage(type, text) {
            draftMessage.classList.remove('d-none', 'alert-danger', 'alert-success');
            draftMessage.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
            draftMessage.textContent = text;
        }

        function clearDraftMessage() {
            draftMessage.classList.add('d-none');
            draftMessage.classList.remove('alert-danger', 'alert-success');
            draftMessage.textContent = '';
        }

        function highlightFilledField(el) {
            el.classList.add('border-primary');
            const clearHighlight = () => {
                el.classList.remove('border-primary');
                el.removeEventListener('input', clearHighlight);
                el.removeEventListener('change', clearHighlight);
            };
            el.addEventListener('input', clearHighlight);
            el.addEventListener('change', clearHighlight);
        }

        function setButtonLoading(loading) {
            draftBtn.disabled = loading;
            if (draftSpinner) {
                draftSpinner.classList.toggle('d-none', !loading);
            }
            if (draftBtnLabel) {
                draftBtnLabel.textContent = loading ? 'Đang soạn...' : 'Soạn bằng AI';
            }
        }

        draftBtn.addEventListener('click', async function () {
            clearDraftMessage();

            const moTaNhanh = (draftInput.value || '').trim();
            if (!moTaNhanh) {
                showDraftMessage('danger', 'Vui lòng nhập mô tả nhanh về sự kiện trước khi gọi AI.');
                draftInput.focus();
                return;
            }

            const csrfInput = addEventForm.querySelector('input[name="_token"]');
            const csrfToken = csrfInput ? csrfInput.value : '';

            setButtonLoading(true);

            try {
                const response = await fetch(aiDraftUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ mo_ta_nhanh: moTaNhanh }),
                });

                let payload = null;
                try {
                    payload = await response.json();
                } catch (_) {
                    payload = null;
                }

                if (!response.ok || !payload || payload.success !== true) {
                    let errorText = (payload && payload.message) ? payload.message : 'Không thể soạn bản nháp từ AI. Vui lòng thử lại.';
                    if (payload && payload.errors && payload.errors.mo_ta_nhanh && payload.errors.mo_ta_nhanh.length > 0) {
                        errorText = payload.errors.mo_ta_nhanh[0];
                    }
                    showDraftMessage('danger', errorText);
                    return;
                }

                const data = payload.data || {};
                let filledCount = 0;

                Object.entries(fieldMap).forEach(([key, elementId]) => {
                    const value = data[key];
                    if (value === null || value === undefined) {
                        return;
                    }

                    const el = document.getElementById(elementId);
                    if (!el) {
                        return;
                    }

                    if (el.tagName === 'SELECT') {
                        const stringVal = String(value);
                        const hasOption = Array.from(el.options).some((opt) => opt.value === stringVal);
                        if (!hasOption) {
                            return;
                        }
                        el.value = stringVal;
                    } else {
                        el.value = String(value);
                    }

                    highlightFilledField(el);
                    filledCount++;
                });

                const startEl = document.getElementById('new-ev-start');
                const endEl = document.getElementById('new-ev-end');
                if (startEl && endEl && data.thoi_gian_bat_dau) {
                    endEl.min = startEl.value;
                }

                const successMsg = filledCount > 0
                    ? 'AI đã điền nháp thông tin sự kiện. Vui lòng kiểm tra kỹ trước khi lưu.'
                    : 'AI chưa trích xuất được trường phù hợp từ mô tả này. Bạn hãy thử mô tả chi tiết hơn.';
                showDraftMessage('success', successMsg);
            } catch (_) {
                showDraftMessage('danger', 'Lỗi kết nối mạng khi gọi dịch vụ AI. Vui lòng thử lại.');
            } finally {
                setButtonLoading(false);
            }
        });
    })();
</script>
