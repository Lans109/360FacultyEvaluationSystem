<script type="text/javascript" src="../../../frontend/layout/confirmation.js" defer></script>

<!-- Confirmation Modal for editing -->
<div class="editConfirmationModal" id="editConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="editConfirmationModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="text-content">
                <div class="modal-header">
                    <span class="close" class="cancel-btn" id="closeEditButton">
                        <img src="../../../frontend/assets/icons/close2.svg" alt="Delete">
                    </span>                                            
                </div>
                <div class="modal-body">
                    <img src="../../../frontend/assets/icons/danger.svg" alt="Close">
                    <h3>Save Changes?</h3>
                    <p>Are you sure you want to save these changes? This action will overwrite the current data.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" id="cancelEditButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="save-btn" id="confirmEditButton">Yes, Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Delete Confirmation -->
<div class="deleteConfirmationModal" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="DeleteConfirmationModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="text-content">
                <div class="modal-header">
                    <span class="close" class="cancel-btn" id="closeEditButton">
                        <img src="../../../frontend/assets/icons/close2.svg" alt="Delete">
                    </span>                                            
                </div>
                <div class="modal-body">
                    <img src="../../../frontend/assets/icons/close3.svg" alt="Close">
                    <h3>Are you sure?</h3>
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" id="cancelDeleteButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="save-btn" id="confirmDeleteButton">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Updating Evaluation -->
<div class="editEvaluationConfirmationModal" id="editEvaluationConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="editEvaluationConfirmationModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="text-content">
                <div class="modal-header">
                    <span class="close" class="cancel-btn" id="closeEditButton">
                        <img src="../../../frontend/assets/icons/close2.svg" alt="Close">
                    </span>
                </div>
                <div class="modal-body">
                    <img src="../../../frontend/assets/icons/danger.svg" alt="Warning">
                    <h3>Update Evaluation Details</h3>
                    <p>
                        You are about to update the details of the evaluation period. 
                        This action will overwrite the existing data and may affect ongoing processes. 
                        Do you want to proceed?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" id="cancelUpdateButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="save-btn" id="confirmUpdateButton">Yes, Update Details</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Disseminating Evaluations -->
<div class="disseminateEvaluationConfirmationModal" id="disseminateEvaluationConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="disseminateEvaluationConfirmationModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="text-content">
                <div class="modal-header">
                    <span class="close" class="cancel-btn" id="closeEditButton">
                        <img src="../../../frontend/assets/icons/close2.svg" alt="Close">
                    </span>
                </div>
                <div class="modal-body">
                    <img src="../../../frontend/assets/icons/danger.svg" alt="Warning">
                    <h3>Disseminate Evaluation</h3>
                    <p>
                        You are about to disseminate the evaluations for this period. 
                        Once disseminated, the surveys will be made available to the designated participants. 
                        This action cannot be undone. Do you want to proceed?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" id="cancelDisseminateButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="save-btn" id="confirmDisseminateButton">Yes, Disseminate</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Starting New Evaluation  -->
<div class="startNewEvaluationConfirmationModal" id="startNewEvaluationConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="startNewEvaluationConfirmationModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="text-content">
                <div class="modal-header">
                    <span class="close" id="closeStartEvaluationButton">
                        <img src="../../../frontend/assets/icons/close2.svg" alt="Close">
                    </span>
                </div>
                <div class="modal-body">
                    <img src="../../../frontend/assets/icons/danger.svg" alt="Warning">
                    <h3>Are you sure?</h3>
                    <p>
                        You are about to start a new evaluation for this period. 
                        Once started, the evaluation will be available to the designated participants. 
                        This action cannot be undone, and you will no longer be able to return to the previous evaluation.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" id="cancelStartEvaluationButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="save-btn" id="confirmStartEvaluationButton">Yes, Start Evaluation</button>
                </div>
            </div>
        </div>
    </div>
</div>

