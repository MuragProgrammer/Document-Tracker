<div id="dynamicModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 id="dynamicModalTitle"></h2>
        <form id="dynamicModalForm" method="POST">
            @csrf
            <div id="dynamicModalFields"></div> <!-- Fields injected here -->
            <div class="form-actions">
                <button type="submit" id="dynamicModalSubmitBtn" class="btn-submit">Save</button>
                <button type="button" class="btn-cancel" id="dynamicModalCancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>
