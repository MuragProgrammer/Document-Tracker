<div id="userModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 id="userModalTitle">Add User</h2>

        <form id="userForm" method="POST">
            @csrf
            <input type="hidden" id="user_id" name="user_id">

            <!-- BASIC INFO -->
            <h2 class="section-title">Basic Information</h2>
            <div class="divider"></div>

            <div class="form-row">
                <div class="form-group">
                    <label>Full Name <span>*</span></label>
                    <input type="text" id="modal_full_name" name="full_name" placeholder="Enter full name" required>
                    <div class="input-feedback"></div>
                </div>

                <div class="form-group">
                    <label>Username <span>*</span></label>
                    <input type="text" id="modal_username" name="username" placeholder="Enter username" required>
                    <div class="input-feedback"></div>
                </div>
            </div>

            <div class="form-group full">
                <label>Password</label>
                <input type="password" id="modal_password" name="password" placeholder="Leave blank to keep current password">
            </div>

            <!-- ASSIGNMENT -->
            <h2 class="section-title">Assignment</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>Section <span>*</span></label>
                    <select id="modal_section_id" name="section_id" required>
                        <option value="">- Select Section -</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->section_id }}" data-department-name="{{ $section->department->department_name ?? '' }}">
                                {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Department</label>
                    <input type="text" id="modal_department_name" name="department_name" readonly>
                </div>

                <div class="form-group">
                    <label>Position <span>*</span></label>
                    <select id="modal_position_id" name="position_id" required>
                        <option value="">- Select Position -</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->position_id }}">{{ $position->position_title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- ACCESS CONTROL -->
            <h2 class="section-title">Access Control</h2>
            <p class="positions-note">Set the status of this user.</p>

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

            <!-- ROLE -->
            <div class="form-group role-group">
                <label>Role <span>*</span></label>
                <select id="modal_role" name="role" required>
                    <option value="ADMIN">Admin</option>
                    <option value="CHIEF">Chief</option>
                    <option value="DIVISION-HEAD">Division Head</option>
                    <option value="SECTION-HEAD">Section Head</option>
                    <option value="EMPLOYEE">Employee</option>
                </select>
            </div>

            <!-- ACTIONS -->
            <div class="form-actions">
                <button type="submit" class="btn btn-submit" id="userModalSubmitBtn">Save</button>
                <button type="button" class="btn btn-cancel modal-close">Cancel</button>
            </div>

        </form>
    </div>
</div>
