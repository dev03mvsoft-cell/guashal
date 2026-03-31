<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    nature: '#2c4c3b',
                    saffron: '#FF6A00',
                    gold: '#FFD700',
                    secondary: '#fdfaf7',
                },
                borderRadius: {
                    'premium': '2rem',
                }
            }
        }
    }
</script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        font-family: 'Outfit', sans-serif;
        background: #faf8f6;
        color: #2c4c3b;
    }

    .glass {
        background: rgba(255, 255, 255, 0.84);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .sidebar-active {
        background: rgba(255, 255, 255, 0.08);
        color: #FFD700;
        font-weight: bold;
    }

    .premium-gradient {
        background: linear-gradient(135deg, #FF6A00 0%, #FFB100 100%);
    }
</style>

<!-- Global Admin Utilities -->
<script>
    // 🛡️ Guarded Delete & Action Confirmation
    function confirmAction(e, title = 'Are you sure?', text = "You won't be able to revert this!") {
        e.preventDefault();
        const form = e.target.closest('form');
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF6A00',
            cancelButtonColor: '#2c4c3b',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            borderRadius: '2rem',
            customClass: {
                popup: 'rounded-[2rem] shadow-2xl border border-nature/5',
                confirmButton: 'rounded-xl px-8 py-3 uppercase tracking-widest text-[12px] font-bold shadow-lg shadow-saffron/20'
            }
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
        return false;
    }

    // ✅ Multi-Select System
    function toggleSelectAll(masterCheckbox, checkboxClass) {
        document.querySelectorAll('.' + checkboxClass).forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
        updateBulkButtonVisibility();
    }

    function updateBulkButtonVisibility() {
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const count = document.querySelectorAll('.multi-select-item:checked').length;
        if (bulkBtn) {
            bulkBtn.style.display = count > 0 ? 'inline-flex' : 'none';
            const countLabel = document.getElementById('selected-count');
            if (countLabel) countLabel.innerText = count;
        }
    }

    // Success/Error Notification Handler
    window.notify = function(type, message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type, // 'success', 'error', 'info', 'warning'
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#ffffff',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        });
    }
</script>