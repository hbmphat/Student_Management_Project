<div class="modal fade" id="shiftsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-clock"></i> Quản lý Ca học</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3">THÊM CA HỌC MỚI</h6>
                        <form id="form-add-shift">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tên Ca (VD: Ca Sáng, Ca 1)</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Giờ bắt đầu</label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Giờ kết thúc</label>
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold"
                                        id="btn-save-shift">Thêm Ca</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <h6 class="fw-bold text-secondary mb-2">DANH SÁCH CA HỌC HIỆN TẠI</h6>
                <div class="table-responsive bg-white rounded shadow-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Ca học</th>
                                <th>Khung giờ</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="shift-list-body">
                            @forelse($shifts as $index => $shift)
                                <tr id="shift-row-{{ $shift->id }}">
                                    <td class="fw-bold text-dark">{{ $shift->name }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark fs-6">
                                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteShift({{ $shift->id }})">
                                            <i class="fas fa-trash">Xoá</i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Chưa có ca học nào được tạo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
