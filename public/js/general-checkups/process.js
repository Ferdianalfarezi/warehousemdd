// Global variables
let selectedStandardId = null;

// Update duration display
function initializeDurationUpdate(mulaiPerbaikan) {
    if (!mulaiPerbaikan) return;

    function updateDuration() {
        const mulaiPerbaikanDate = new Date(mulaiPerbaikan);
        const now = new Date();
        const diffMs = now - mulaiPerbaikanDate;
        const diffMins = Math.floor(diffMs / 60000);
        
        const hours = Math.floor(diffMins / 60);
        const mins = diffMins % 60;
        
        const durationDisplay = document.getElementById('durationDisplay');
        if (durationDisplay) {
            durationDisplay.textContent = 
                hours > 0 ? `⏱️ ${hours} jam ${mins} menit` : `⏱️ ${mins} menit`;
        }
    }

    updateDuration();
    setInterval(updateDuration, 60000); // Update every minute
}

// Set Status
function setStatus(standardId, status) {
    // Update hidden input
    const statusInput = document.querySelector(`input[data-status-input="${standardId}"]`);
    statusInput.value = status;

    // Update button styles
    const buttons = document.querySelectorAll(`button[data-standard-id="${standardId}"]`);
    buttons.forEach(btn => {
        const btnStatus = btn.getAttribute('data-status');
        if (btnStatus === status) {
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            if (status === 'ok') {
                btn.classList.add('bg-green-500', 'text-white');
            } else {
                btn.classList.add('bg-red-500', 'text-white');
            }
        } else {
            btn.classList.add('bg-gray-100', 'text-gray-600');
            btn.classList.remove('bg-green-500', 'bg-red-500', 'text-white');
        }
    });

    // Show/hide NG actions
    const ngSection = document.querySelector(`div[data-ng-section="${standardId}"]`);
    if (status === 'ng') {
        ngSection.classList.remove('hidden');
    } else {
        ngSection.classList.add('hidden');
    }

    // Update action buttons
    updateActionButtons();
}

// Show action form based on selected type
function showActionForm(standardId) {
    const actionType = document.getElementById(`actionTypeSelect-${standardId}`).value;
    
    // Hide all forms
    document.getElementById(`partForm-${standardId}`).classList.add('hidden');
    document.getElementById(`inhouseForm-${standardId}`).classList.add('hidden');
    document.getElementById(`outhouseForm-${standardId}`).classList.add('hidden');
    
    // Show selected form
    if (actionType === 'part') {
        document.getElementById(`partForm-${standardId}`).classList.remove('hidden');
    } else if (actionType === 'inhouse') {
        document.getElementById(`inhouseForm-${standardId}`).classList.remove('hidden');
    } else if (actionType === 'outhouse') {
        document.getElementById(`outhouseForm-${standardId}`).classList.remove('hidden');
    }
}

// Update action buttons based on status
function updateActionButtons() {
    const statusInputs = document.querySelectorAll('input[data-status-input]');
    let hasNGNotClosed = false;
    let allFilled = true;

    statusInputs.forEach(input => {
        const standardId = input.getAttribute('data-status-input');
        const status = input.value;

        // Check if filled
        if (!status) {
            allFilled = false;
            return;
        }

        // Check if NG
        if (status === 'ng') {
            // Check action status from hidden input
            const actionStatusInput = document.querySelector(`input[data-ng-action-status="${standardId}"]`);
            
            if (actionStatusInput) {
                const actionStatus = actionStatusInput.value;
                // If action status is NOT 'closed', then it's not complete yet
                if (actionStatus !== 'closed') {
                    hasNGNotClosed = true;
                }
            } else {
                // No action selected/completed yet
                hasNGNotClosed = true;
            }
        }
    });

    const saveTempBtn = document.getElementById('saveTempBtn');
    const finishBtn = document.getElementById('finishBtn');

    // Show "Selesai" only if:
    // 1. All parameters are filled
    // 2. No NG that is not closed yet
    if (allFilled && !hasNGNotClosed) {
        saveTempBtn.classList.add('hidden');
        finishBtn.classList.remove('hidden');
    } else {
        saveTempBtn.classList.remove('hidden');
        finishBtn.classList.add('hidden');
    }
}

// Submit Inhouse Request
async function submitInhouseRequest(checkupId, standardId) {
    const problem = document.getElementById(`inhouseProblem-${standardId}`).value;
    const proses = document.getElementById(`inhouseProses-${standardId}`).value;
    const mesin = document.getElementById(`inhouseMesin-${standardId}`).value;

    if (!problem || !proses || !mesin) {
        Swal.fire('Peringatan!', 'Semua field harus diisi!', 'warning');
        return;
    }

    // Get checkup_detail_id
    const detailId = await getOrCreateDetailId(checkupId, standardId);

    if (!detailId) {
        Swal.fire('Error!', 'Gagal mendapatkan detail ID!', 'error');
        return;
    }

    try {
        const response = await fetch('/inhouse-requests/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                general_checkup_id: checkupId,
                checkup_detail_id: detailId,
                problem: problem,
                proses_dilakukan: proses,
                mesin: mesin
            })
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal mengajukan permintaan inhouse!', 'error');
    }
}

// Submit Outhouse Request
async function submitOuthouseRequest(checkupId, standardId) {
    const problem = document.getElementById(`outhouseProblem-${standardId}`).value;
    const mesin = document.getElementById(`outhouseMesin-${standardId}`).value;
    const supplier = document.getElementById(`outhouseSupplier-${standardId}`).value;

    if (!problem || !mesin || !supplier) {
        Swal.fire('Peringatan!', 'Semua field harus diisi!', 'warning');
        return;
    }

    // Get checkup_detail_id
    const detailId = await getOrCreateDetailId(checkupId, standardId);

    if (!detailId) {
        Swal.fire('Error!', 'Gagal mendapatkan detail ID!', 'error');
        return;
    }

    try {
        const response = await fetch('/outhouse-requests/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                general_checkup_id: checkupId,
                checkup_detail_id: detailId,
                problem: problem,
                mesin: mesin,
                supplier: supplier
            })
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal mengajukan permintaan outhouse!', 'error');
    }
}

// Helper function to get or create checkup detail ID
async function getOrCreateDetailId(checkupId, standardId) {
    // First save current status to create detail if not exists
    const formData = new FormData(document.getElementById('checkupForm'));
    const data = {};
    data.details = [];

    const statusInput = document.querySelector(`input[data-status-input="${standardId}"]`);
    const status = statusInput.value;
    const catatan = formData.get(`details[${standardId}][catatan]`);

    data.details.push({
        check_indicator_standard_id: standardId,
        status: status,
        catatan: catatan
    });

    try {
        const response = await fetch(`/general-checkups/${checkupId}/save-checkup`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        
        if (result.success && result.detail_id) {
            return result.detail_id;
        }
        
        return null;
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

// Close Action
async function closeAction(actionType, detailId, standardId) {
    const result = await Swal.fire({
        title: 'Close Tindakan?',
        text: "Tindakan akan ditandai selesai",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Close!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    let url = '';
    if (actionType === 'part') {
        url = `/checkup-parts/${detailId}/close`;
    } else if (actionType === 'inhouse') {
        url = `/inhouse-requests/${detailId}/close`;
    } else if (actionType === 'outhouse') {
        url = `/outhouse-requests/${detailId}/close`;
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menutup tindakan!', 'error');
    }
}

// Save Temporary
async function saveTemporary() {
    const checkupId = window.checkupId; // Get from global variable
    const formData = new FormData(document.getElementById('checkupForm'));
    const data = {};
    data.details = [];
    data.catatan_umum = formData.get('catatan_umum');

    // Collect all details
    const statusInputs = document.querySelectorAll('input[data-status-input]');
    statusInputs.forEach(input => {
        const standardId = input.getAttribute('data-status-input');
        const status = input.value;
        
        if (status) {
            const catatan = formData.get(`details[${standardId}][catatan]`);
            data.details.push({
                check_indicator_standard_id: standardId,
                status: status,
                catatan: catatan
            });
        }
    });

    if (data.details.length === 0) {
        Swal.fire('Peringatan!', 'Minimal isi 1 checklist!', 'warning');
        return;
    }

    try {
        const response = await fetch(`/general-checkups/${checkupId}/save-checkup`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menyimpan checkup!', 'error');
    }
}

// Finish Checkup
async function finishCheckup() {
    const checkupId = window.checkupId; // Get from global variable
    
    const result = await Swal.fire({
        title: 'Selesaikan Checkup?',
        text: "Checkup akan diselesaikan dan dipindahkan ke history",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Selesai!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    const formData = new FormData(document.getElementById('checkupForm'));
    const data = {};
    data.details = [];
    data.catatan_umum = formData.get('catatan_umum');

    // Collect all details
    const statusInputs = document.querySelectorAll('input[data-status-input]');
    statusInputs.forEach(input => {
        const standardId = input.getAttribute('data-status-input');
        const status = input.value;
        
        if (status) {
            const catatan = formData.get(`details[${standardId}][catatan]`);
            data.details.push({
                check_indicator_standard_id: standardId,
                status: status,
                catatan: catatan
            });
        }
    });

    try {
        const response = await fetch(`/general-checkups/${checkupId}/finish-checkup`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
            window.location.href = result.redirect;
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menyelesaikan checkup!', 'error');
    }
}

// Open Add Part Modal
function openAddPartModal(checkupId, standardId) {
    selectedStandardId = standardId;
    loadAvailableParts();
    
    const modal = document.getElementById('addPartModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAddPartModal() {
    selectedStandardId = null;
    document.getElementById('addPartForm').reset();
    
    const modal = document.getElementById('addPartModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Load Available Parts
async function loadAvailableParts() {
    try {
        const response = await fetch('/checkup-parts/available');
        const result = await response.json();

        if (result.success) {
            const select = document.getElementById('partSelect');
            select.innerHTML = '<option value="">Pilih Part</option>';
            
            result.data.forEach(part => {
                const option = document.createElement('option');
                option.value = part.id;
                option.textContent = `${part.kode_part} - ${part.nama} (Stock: ${part.stock} ${part.satuan})`;
                option.dataset.stock = part.stock;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading parts:', error);
    }
}

// Add Part
async function addPart() {
    const checkupId = window.checkupId; // Get from global variable
    const partId = document.getElementById('partSelect').value;
    const quantity = document.getElementById('partQuantity').value;
    const catatan = document.getElementById('partCatatan').value;

    if (!partId || !quantity) {
        Swal.fire('Peringatan!', 'Part dan quantity harus diisi!', 'warning');
        return;
    }

    const selectedOption = document.getElementById('partSelect').selectedOptions[0];
    const availableStock = parseInt(selectedOption.dataset.stock);

    if (parseInt(quantity) > availableStock) {
        Swal.fire('Error!', `Stock tidak mencukupi! Available: ${availableStock}`, 'error');
        return;
    }

    // Get checkup_detail_id
    const detailId = await getOrCreateDetailId(checkupId, selectedStandardId);

    if (!detailId) {
        Swal.fire('Error!', 'Gagal mendapatkan detail ID!', 'error');
        return;
    }

    try {
        const response = await fetch('/checkup-parts/add', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                general_checkup_id: checkupId,
                checkup_detail_id: detailId,
                part_id: partId,
                quantity_used: quantity,
                catatan: catatan
            })
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });

            closeAddPartModal();
            
            // Reload page untuk update tampilan
            location.reload();
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menambahkan part!', 'error');
    }
}

// Remove Part
async function removePart(partReplacementId, standardId) {
    const result = await Swal.fire({
        title: 'Hapus Part?',
        text: "Part akan dihapus dari list (stock tidak terpengaruh)",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/checkup-parts/${partReplacementId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });

            // Reload page
            location.reload();
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menghapus part!', 'error');
    }
}

// Close Individual Part
async function closeIndividualPart(partReplacementId, standardId) {
    const result = await Swal.fire({
        title: 'Tandai Part Terpasang?',
        text: "Part akan ditandai sudah terpasang",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tandai Terpasang!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/checkup-parts/${partReplacementId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
            
            // Reload untuk update tampilan
            location.reload();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menutup part!', 'error');
    }
}

// Close All Parts
async function closeAllParts(checkupDetailId, standardId) {
    const result = await Swal.fire({
        title: 'Tandai Semua Part Terpasang?',
        text: "Semua part yang belum terpasang akan ditandai sebagai terpasang",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Close Semua!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/checkup-parts/close-all/${checkupDetailId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
            
            // Reload untuk update tampilan
            location.reload();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menutup semua part!', 'error');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateActionButtons();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddPartModal();
    }
});