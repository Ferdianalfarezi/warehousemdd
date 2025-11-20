<!-- History Detail Modal -->
<div id="historyDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900">Detail History Checkup</h3>
            <button onclick="closeHistoryDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p class="text-gray-600">Detail history content will be implemented here</p>
            <!-- TODO: Add comprehensive history detail view -->
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end rounded-b-2xl">
            <button 
                onclick="closeHistoryDetailModal()"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function closeHistoryDetailModal() {
    const modal = document.getElementById('historyDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>