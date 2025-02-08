// Form Validation
function validateForm() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            if (input.hasAttribute('required') && !input.value) {
                input.classList.add('invalid');
                input.classList.remove('valid');
            } else {
                input.classList.add('valid');
                input.classList.remove('invalid');
            }
        });
    });
}

// Dynamic Status Updates
function initializeStatusUpdates() {
    const statusSelects = document.querySelectorAll('select[name="status"]');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', async (e) => {
            const form = e.target.closest('form');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const complaintCard = form.closest('.complaint-card');
                    complaintCard.querySelector('.status').textContent = 'Status: ' + select.value;
                    
                    complaintCard.classList.add('update-flash');
                    setTimeout(() => {
                        complaintCard.classList.remove('update-flash');
                    }, 1000);
                }
            } catch (error) {
                console.error('Error updating status:', error);
            }
        });
    });
}

// Smooth Transitions and Animations
function initializeAnimations() {
    const cards = document.querySelectorAll('.complaint-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Form submission animation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.classList.add('submitting');
        });
    });
}

function autoCompleteEmail() {
    var emailField = document.getElementById('email');
    var email = emailField.value;

    if (email.includes('@') && !email.endsWith('@college.edu')) {
        var parts = email.split('@');
        emailField.value = parts[0] + '@college.edu';
    }
}


document.addEventListener('DOMContentLoaded', () => {
    validateForm();
    initializeStatusUpdates();
    initializeAnimations();
});