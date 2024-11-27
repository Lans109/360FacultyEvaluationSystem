document.addEventListener("DOMContentLoaded", function () {
  // Select all forms with the name "editForm"
  const forms = document.querySelectorAll('form[name="editForm"]'); 

  forms.forEach((form) => {
    // Get the unique form ID from its name (e.g., "editForm{faculty_id}")
    const formName = form.getAttribute('name');  
    const modalId = `editModal${formName.replace('editForm', '')}`; // Create the modal ID using the form's name
    const editModal = document.getElementById(modalId); // Get the modal element by ID
    const editConfirmationModal = document.getElementById("editConfirmationModal");
    const confirmEditButton = document.getElementById("confirmEditButton");
    const cancelEditButton = document.getElementById("cancelEditButton");

    // When the form is submitted
    form.addEventListener("submit", function (event) {
      event.preventDefault(); // Stop the form from submitting right away
      editModal.style.display = "none"; // Hide the edit form
      editConfirmationModal.style.display = "block"; // Show the confirmation modal

      // If the user confirms, submit the form
      confirmEditButton.onclick = function () {
        editConfirmationModal.style.display = "none"; // Hide the confirmation modal
        form.submit(); // Submit the form
      };

      // If the user cancels, go back to the edit form
      cancelEditButton.onclick = function () {
        editConfirmationModal.style.display = "none"; // Hide the confirmation modal
        editModal.style.display = "block"; // Show the edit form again
      };
    });

    // Close the confirmation modal if the cancel button or close icon is clicked
    const cancelButtons = editConfirmationModal.querySelectorAll(".cancel-btn, .close");
    cancelButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        editConfirmationModal.style.display = "none"; // Hide the confirmation modal
      });
    });
  });

  // Attach the click event listener to delete forms
  const deleteForms = document.querySelectorAll('form[name="deleteForm"]');

  deleteForms.forEach((form) => {
    const deleteConfirmationModal = document.getElementById("deleteConfirmationModal"); // Get the delete confirmation modal
    const confirmDeleteButton = document.getElementById("confirmDeleteButton"); // Get the "confirm delete" button
    const cancelDeleteButton = document.getElementById("cancelDeleteButton"); // Get the "cancel delete" button
  
    // When the form is submitted
    form.addEventListener('submit', function (event) {
      event.preventDefault();  // Stop the form from submitting immediately
      deleteConfirmationModal.style.display = "block";  // Show the delete confirmation modal
  
      // If the user confirms, submit the form
      confirmDeleteButton.onclick = function () {
        deleteConfirmationModal.style.display = "none"; // Hide the delete confirmation modal
        form.submit();  // Submit the form
      };
  
      // If the user cancels, hide the modal without submitting
      cancelDeleteButton.onclick = function () {
        deleteConfirmationModal.style.display = "none";  // Hide the delete confirmation modal
      };
    });
  
    // Close the delete confirmation modal if the cancel button or close icon is clicked
    const cancelButtons = deleteConfirmationModal.querySelectorAll(".cancel-btn, .close");
    cancelButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        deleteConfirmationModal.style.display = "none"; // Hide the delete confirmation modal
      });
    });
  });
  

  // Close modals if the user clicks outside of them or on the cancel button
  window.addEventListener("click", function (event) {
    const deleteConfirmationModal = document.getElementById("deleteConfirmationModal");
    const editConfirmationModal = document.getElementById("editConfirmationModal");
    const successModal = document.getElementById("successModal");

    // Check if the click happened outside any of the modals
    if (event.target === deleteConfirmationModal) {
      deleteConfirmationModal.style.display = "none"; // Hide the delete confirmation modal
    } else if (event.target === editConfirmationModal) {
      editConfirmationModal.style.display = "none"; // Hide the edit confirmation modal
    } else if (event.target === successModal) {
      successModal.style.display = "none"; // Hide the success modal
    }
  });

  // Expose the openDeleteConfirmationModal function for external use
  window.openDeleteConfirmationModal = openDeleteConfirmationModal;
});
