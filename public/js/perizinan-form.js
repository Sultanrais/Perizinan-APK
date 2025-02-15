document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;
    const form = document.getElementById('perizinanForm');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Cek apakah ada error dari server
    const serverErrors = document.querySelectorAll('.is-invalid');
    if (serverErrors.length > 0) {
        // Temukan step yang memiliki error
        let stepWithError = 1;
        serverErrors.forEach(error => {
            const step = error.closest('.form-step');
            if (step) {
                stepWithError = parseInt(step.dataset.step);
            }
        });
        currentStep = stepWithError;
        updateSteps();
        // Scroll ke error pertama
        serverErrors[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Update progress bar dan tampilan step
    function updateSteps() {
        // Update progress steps
        document.querySelectorAll('.progress-step').forEach(step => {
            const stepNum = parseInt(step.dataset.step);
            if (stepNum === currentStep) {
                step.classList.add('active');
                step.classList.remove('complete');
            } else if (stepNum < currentStep) {
                step.classList.add('complete');
                step.classList.remove('active');
            } else {
                step.classList.remove('active', 'complete');
            }
        });

        // Update form steps visibility
        document.querySelectorAll('.form-step').forEach(step => {
            const stepNum = parseInt(step.dataset.step);
            if (stepNum === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        // Update buttons
        prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
        if (currentStep === totalSteps) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        }

        // Scroll to top of the form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Validasi form per step
    function validateStep(step) {
        let isValid = true;
        const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        
        // Reset semua validasi
        currentStepElement.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Validasi required fields pada step saat ini
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value) {
                isValid = false;
                field.classList.add('is-invalid');
                
                // Tambahkan pesan error jika belum ada
                if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Field ini wajib diisi';
                    field.parentNode.insertBefore(feedback, field.nextSibling);
                }
            }
        });

        // Validasi khusus untuk setiap step
        switch(step) {
            case 1:
                // Validasi NIK
                const nikField = currentStepElement.querySelector('[name="nik"]');
                if (nikField && nikField.value && nikField.value.length !== 16) {
                    isValid = false;
                    nikField.classList.add('is-invalid');
                    const feedback = nikField.nextElementSibling?.classList.contains('invalid-feedback') 
                        ? nikField.nextElementSibling 
                        : document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'NIK harus 16 digit';
                    if (!nikField.nextElementSibling?.classList.contains('invalid-feedback')) {
                        nikField.parentNode.insertBefore(feedback, nikField.nextSibling);
                    }
                }
                break;

            case 3:
                // Validasi file
                const fileInputs = currentStepElement.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    if (input.hasAttribute('required') && !input.files.length) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        
                        // Tambahkan pesan error jika belum ada
                        if (!input.nextElementSibling?.classList.contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.classList.add('invalid-feedback');
                            feedback.textContent = 'File wajib diunggah';
                            input.parentNode.insertBefore(feedback, input.nextSibling);
                        }
                    }
                });
                break;

            case 4:
                // Validasi checkbox konfirmasi
                const konfirmasi = currentStepElement.querySelector('#konfirmasi');
                if (konfirmasi && !konfirmasi.checked) {
                    isValid = false;
                    konfirmasi.classList.add('is-invalid');
                    
                    // Tambahkan pesan error jika belum ada
                    if (!konfirmasi.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback');
                        feedback.textContent = 'Anda harus menyetujui pernyataan ini';
                        konfirmasi.parentNode.insertBefore(feedback, konfirmasi.nextSibling);
                    }
                }
                break;
        }

        // Jika ada error, scroll ke error pertama
        if (!isValid) {
            const firstError = currentStepElement.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        return isValid;
    }

    // Event listener untuk next button
    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (validateStep(currentStep)) {
            currentStep++;
            updateSteps();
        }
    });

    // Event listener untuk previous button
    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        currentStep--;
        updateSteps();
    });

    // Event listener untuk form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi semua step sebelum submit
        let isValid = true;
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                currentStep = i;
                updateSteps();
                isValid = false;
                break;
            }
        }
        
        if (isValid) {
            // Disable tombol submit untuk mencegah double submit
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
            
            // Submit form
            this.submit();
        }
    });

    // Initialize
    updateSteps();
});
