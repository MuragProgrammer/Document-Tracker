<div id="positionModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 id="positionModalTitle">Add Position</h2>

        <form id="positionForm" method="POST">
            @csrf
            <input type="hidden" id="position_id" name="position_id">

            <!-- Position Title -->
            <div class="form-group">
                <label>Position Title <span class="required">*</span></label>
                <input type="text" id="modal_position_title" name="position_title" placeholder="Enter position title" required>
                <div class="input-feedback"></div>
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
                <button type="submit" class="btn btn-submit" id="positionModalSubmitBtn">Save</button>
                <button type="button" class="btn btn-cancel modal-close">Cancel</button>
            </div>
        </form>
    </div>
</div>
