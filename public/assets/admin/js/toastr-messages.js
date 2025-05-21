// Function to display success messages
function showSuccessMessage(message) {
    toastr.success(message, 'Success');
}

// Function to display error messages
function showErrorMessage(message) {
    toastr.error(message, 'Error');
}

// Function to display warning messages
function showWarningMessage(message) {
    toastr.warning(message, 'Warning');
}

// Function to display info messages
function showInfoMessage(message) {
    toastr.info(message, 'Information');
}

// Handle Laravel flash messages
$(document).ready(function() {
    // Check for flash messages in the page
    if (typeof successMessage !== 'undefined' && successMessage) {
        showSuccessMessage(successMessage);
    }
    
    if (typeof errorMessage !== 'undefined' && errorMessage) {
        showErrorMessage(errorMessage);
    }
    
    if (typeof warningMessage !== 'undefined' && warningMessage) {
        showWarningMessage(warningMessage);
    }
    
    if (typeof infoMessage !== 'undefined' && infoMessage) {
        showInfoMessage(infoMessage);
    }
});
