<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Add New User</h2>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4">
            @csrf

            <!-- Avatar Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Avatar (Optional)</label>
                <input type="file" id="createAvatar" name="avatar" accept="image/*" onchange="previewImage(event, 'createPreview')"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                <span class="text-red-500 text-sm error-message" id="error-create-avatar"></span>

                <!-- Image Preview -->
                <div id="createPreviewContainer" class="mt-3 hidden">
                    <img id="createPreview" class="w-32 h-32 rounded-full object-cover border-2 border-gray-300">
                </div>
            </div>

            <!-- Nama -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                <input type="text" id="createNama" name="nama" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Enter nama lengkap">
                <span class="text-red-500 text-sm error-message" id="error-create-nama"></span>
            </div>

            <!-- NIK -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                <input type="text" id="createNik" name="nik" required maxlength="10" inputmode="numeric" pattern="[0-9]*"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Enter NIK (maks. 10 digit)">
                <span class="text-red-500 text-sm error-message" id="error-create-nik"></span>
                <p class="text-xs text-gray-500 mt-1">NIK dipakai untuk login, harus unik</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select id="createRoleId" name="role_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->nama) }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-role_id"></span>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select id="createStatus" name="status" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-status"></span>
                </div>
            </div>

            <!-- Jabatan - hanya muncul kalau Role = Operator -->
            <div id="createOperatorFields" class="hidden border-t border-gray-200 pt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                    <select id="createJabatan" name="jabatan"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Jabatan</option>
                        <option value="Leader">Leader</option>
                        <option value="Asst Leader">Asst Leader</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-jabatan"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeCreateModal()"
                    class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Add User
                </button>
            </div>
        </form>
    </div>
</div>