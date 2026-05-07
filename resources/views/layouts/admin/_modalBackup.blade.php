@if (Auth::user()->role === 'admin')
	<div class="modal fade" id="backupModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content border-0 shadow">
				<div class="modal-header bg-dark text-white">
					<h5 class="modal-title"><i class="fas fa-cloud-download-alt"></i> Xác thực sao lưu dữ liệu</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
						aria-label="Close"></button>
				</div>
				<form id="backupForm">
					<div class="modal-body">
						<p class="text-muted">Nhập mật khẩu quản trị để xác nhận tạo bản sao lưu dữ liệu.</p>
						<div class="mb-3">
							<label class="form-label fw-bold">Mật khẩu của bạn</label>
							<input type="password" class="form-control" id="backup_confirm_password" required
								placeholder="Nhập mật khẩu...">
							<div class="invalid-feedback fw-bold" id="backup_password_error"></div>
						</div>
					</div>
					<div class="modal-footer bg-light d-flex justify-content-end align-items-center">
						{{-- <span class="text-muted small">Backup sẽ được tạo và tải xuống sau khi xác thực.</span> --}}
						<div>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
							<button type="submit" class="btn btn-warning fw-bold text-dark" id="btnBackupConfirm">
								<i class="fas fa-cloud-download-alt me-1"></i> Xác nhận Backup
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endif
