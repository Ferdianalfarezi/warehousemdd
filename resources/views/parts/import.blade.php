<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" onclick="closeImportModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Import Parts</h2>
                <p class="text-sm text-gray-600 mt-0.5">Upload Excel file to import multiple parts</p>
            </div>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">

            <!-- Download Template -->
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center text-sm">
                    <span class="bg-black text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">1</span>
                    Download Template Excel
                </h3>
                <a href="{{ route('parts.download.template') }}" 
                   class="inline-flex items-center space-x-2 bg-green-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-green-700 transition text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Download Template</span>
                </a>
            </div>

            <!-- Upload Form -->
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center text-sm">
                    <span class="bg-black text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">2</span>
                    Upload File Excel
                </h3>
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Select Excel File <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       id="importFileInput"
                                       name="file" 
                                       accept=".xlsx,.xls"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition text-sm"
                                       required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Format: .xlsx atau .xls (Max: 5MB)</p>
                            <span class="text-red-500 text-sm error-message" id="error-import-file"></span>
                        </div>

                        <!-- File Preview -->
                        <div id="filePreview" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900" id="fileName"></p>
                                        <p class="text-xs text-gray-500" id="fileSize"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" 
                                id="importButton"
                                class="w-full bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span>Start Import</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results (Hidden by default) -->
            <div id="importResultsCard" class="hidden bg-white border border-gray-200 rounded-xl p-4">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center text-sm">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Import Results
                </h3>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-blue-600 font-semibold mb-1">Total Rows</p>
                        <p class="text-2xl font-bold text-blue-900" id="totalRows">0</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-green-600 font-semibold mb-1">Success</p>
                        <p class="text-2xl font-bold text-green-900" id="successRows">0</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-red-600 font-semibold mb-1">Failed</p>
                        <p class="text-2xl font-bold text-red-900" id="failedRows">0</p>
                    </div>
                </div>
                
                <div id="errorsContainer" class="hidden">
                    <h4 class="font-semibold text-gray-900 mb-2 text-sm">Errors & Warnings:</h4>
                    <div id="errorsList" class="bg-red-50 border border-red-200 rounded-lg p-3 max-h-48 overflow-y-auto text-xs">
                        <!-- Errors will be displayed here -->
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>

<style>
/* CSS khusus untuk import modal backdrop blur */
#importModal {
    backdrop-filter: blur(4px);
    background-color: rgba(0, 0, 0, 0.5);
    transition: opacity 0.3s ease-in-out;
}

/* Fade in hanya opacity, tanpa scale */
#importModal.modal-fade-in {
    opacity: 1;
}

/* Hidden state */
#importModal:not(.modal-fade-in) {
    opacity: 0;
}
</style>

<script>
// File input preview
document.getElementById('importFileInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        document.getElementById('filePreview').classList.remove('hidden');
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function clearFile() {
    document.getElementById('importFileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

function openImportModal() {
    const modal = document.getElementById('importModal');
    document.getElementById('importForm').reset();
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('importResultsCard').classList.add('hidden');
    document.getElementById('error-import-file').textContent = '';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Trigger fade in animation (hanya opacity)
    setTimeout(() => {
        modal.classList.add('modal-fade-in');
    }, 10);
}

function closeImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.remove('modal-fade-in');
    
    // Delay untuk animasi fade out
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// Submit Import Form
document.getElementById('importForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = document.getElementById('importButton');
    const originalButtonContent = submitButton.innerHTML;
    
    // Disable button & show loading
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Importing...</span>
    `;
    
    // Clear previous errors
    document.getElementById('error-import-file').textContent = '';
    document.getElementById('importResultsCard').classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("parts.import") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show results
            document.getElementById('totalRows').textContent = data.data.total;
            document.getElementById('successRows').textContent = data.data.success;
            document.getElementById('failedRows').textContent = data.data.failed;
            document.getElementById('importResultsCard').classList.remove('hidden');
            
            // Show errors if any
            if (data.data.errors.length > 0) {
                const errorsList = document.getElementById('errorsList');
                errorsList.innerHTML = data.data.errors.map(error => 
                    `<p class="text-xs text-red-700 mb-1">• ${error}</p>`
                ).join('');
                document.getElementById('errorsContainer').classList.remove('hidden');
            } else {
                document.getElementById('errorsContainer').classList.add('hidden');
            }
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Import Completed!',
                html: `
                    <div class="text-left">
                        <p class="mb-1"><strong>Total:</strong> ${data.data.total} rows</p>
                        <p class="mb-1 text-green-600"><strong>Success:</strong> ${data.data.success} rows</p>
                        <p class="text-red-600"><strong>Failed:</strong> ${data.data.failed} rows</p>
                    </div>
                `,
                confirmButtonColor: '#000',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload page otomatis setelah klik OK
                if (data.data.success > 0) {
                    location.reload();
                }
            });
            
            // Clear file input
            document.getElementById('importFileInput').value = '';
            document.getElementById('filePreview').classList.add('hidden');
            
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorElement = document.getElementById(`error-import-${key}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[key][0];
                    }
                });
            }
            Swal.fire({
                icon: 'error',
                title: 'Import Failed!',
                text: data.message || 'Something went wrong!',
                confirmButtonColor: '#000'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Something went wrong!',
            confirmButtonColor: '#000'
        });
    } finally {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonContent;
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImportModal();
    }
});
</script>