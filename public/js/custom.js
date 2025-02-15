// Konfirmasi untuk aksi penting
function confirmAction(message, callback) {
    Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5e72e4',
        cancelButtonColor: '#f5365c',
        confirmButtonText: 'Ya, lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

// Toast notifikasi
function showNotification(message, type = 'success') {
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    toast.fire({
        icon: type,
        title: message
    });
}

// Inisialisasi komponen-komponen UI
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popover
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // DataTables dengan bahasa Indonesia
    if ($.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            pageLength: 10,
            responsive: true
        });
    }

    // Select2 dengan bahasa Indonesia
    if ($.fn.select2) {
        $.fn.select2.defaults.set('language', 'id');
    }
});

// Validasi form
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
            
            // Tambah pesan error jika belum ada
            if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Bidang ini wajib diisi';
                field.parentNode.appendChild(feedback);
            }
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Preview file upload
function previewFile(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById(previewElement);
            if (preview) {
                if (input.files[0].type.startsWith('image/')) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-fluid">`;
                } else {
                    preview.innerHTML = `
                        <div class="document-preview">
                            <i class="fas fa-file-alt fa-3x"></i>
                            <p class="mt-2">${input.files[0].name}</p>
                        </div>
                    `;
                }
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Animasi loading
function showLoading() {
    Swal.fire({
        title: 'Mohon tunggu...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoading() {
    Swal.close();
}

// Format angka ke format Indonesia
function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Format tanggal ke format Indonesia
function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Handle error dari AJAX
function handleAjaxError(error) {
    let message = 'Terjadi kesalahan. Silakan coba lagi.';
    
    if (error.responseJSON && error.responseJSON.message) {
        message = error.responseJSON.message;
    }
    
    showNotification(message, 'error');
}

// Fungsi untuk scroll halus ke elemen
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Export tabel ke Excel
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
    XLSX.writeFile(wb, `${filename}.xlsx`);
}

// Export tabel ke PDF
function exportToPDF(tableId, filename) {
    const element = document.getElementById(tableId);
    if (!element) return;

    html2pdf()
        .set({
            margin: 1,
            filename: `${filename}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        })
        .from(element)
        .save();
}
