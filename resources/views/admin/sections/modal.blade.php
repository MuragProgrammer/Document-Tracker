<div id="sectionModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 id="sectionModalTitle">Add Section</h2>

        <form id="sectionForm" method="POST">
            @csrf
            <input type="hidden" id="section_id" name="section_id">

            <!-- Section Name -->
            <div class="form-group">
                <label>Section Name <span class="required">*</span></label>
                <input type="text" id="modal_section_name" name="section_name" placeholder="Enter section name" required>
                <div class="input-feedback"></div>
            </div>

            <!-- Department -->
            <div class="form-group">
                <label>Department <span class="required">*</span></label>
                <select id="modal_department_id" name="department_id" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                    @endforeach
                </select>
                <div class="input-feedback-department"></div>
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
                <button type="submit" class="btn-submit" id="sectionModalSubmitBtn">Save</button>
                <button type="button" class="btn-cancel modal-close">Cancel</button>
            </div>
        </form>
    </div>
</div>
