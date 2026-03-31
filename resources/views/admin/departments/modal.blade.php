{{-- Department Modal --}}
<div id="departmentModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 id="modalTitle">Add Department</h2>

        <form id="departmentForm" method="POST">
            @csrf
            <input type="hidden" id="department_id" name="department_id">

            <!-- Department Name -->
            <div class="form-group">
                <label>Department Name <span class="required">*</span></label>
                <input type="text" id="modal_department_name" name="department_name"
                    placeholder="Enter department name" required>
                <div class="input-feedback"></div>
            </div>

            <!-- Department Code -->
            <div class="form-group">
                <label>Department Code <span class="required">*</span></label>
                <input type="text" id="modal_department_code" name="department_code"
                    placeholder="Enter department code" required>
                <div class="input-feedback-code"></div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label>Status</label>
                <div class="status-toggle-wrapper">
                    <input type="hidden" name="is_active" value="0">
                    <label class="toggle-switch">
                        <input type="checkbox" id="modal_is_active" name="is_active" value="1">
                        <span class="slider"></span>
                    </label>
                    <span class="status-label disabled">Inactive</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="modalSubmitBtn">Save</button>
                <button type="button" class="btn-cancel modal-close">Cancel</button>
            </div>
        </form>
    </div>
</div>
