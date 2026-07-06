<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editUserId" name="id">

            <!-- Current Avatar Preview -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Avatar</label>
                <div class="flex items-center space-x-4">
                    <img id="editPreview" src="" alt="Current Avatar"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                    <span id="noImageText" class="text-gray-500 text-sm">No avatar uploaded</span>
                </div>
            </div>

            <!-- Change Avatar -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Change Avatar (Optional)</label>
                <input type="file" id="editAvatar" name="avatar" accept="image/*" onchange="previewEditImage(event)"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                <span class="text-red-500 text-sm error-message" id="error-edit-avatar"></span>
                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current avatar</p>
            </div>

            <!-- Username -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                <input type="text" id="editUsername" name="username" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Enter username">
                <span class="text-red-500 text-sm error-message" id="error-edit-username"></span>
            </div>

            <!-- New Password (Optional) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    New Password (Optional)
                </label>

                <div class="relative">
                    <input type="password" id="editPassword" name="password"
                        class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Leave empty to keep current password">

                    <button type="button"
                            onclick="togglePassword('editPassword', 'editPasswordIcon')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    </button>
                </div>

                <span class="text-red-500 text-sm error-message" id="error-edit-password"></span>
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters if changing</p>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Confirm New Password
                </label>

                <div class="relative">
                    <input type="password" id="editPasswordConfirmation" name="password_confirmation"
                        class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Confirm new password">

                    <button type="button"
                            onclick="togglePassword('editPasswordConfirmation', 'editPasswordConfirmIcon')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    </button>
                </div>

                <span class="text-red-500 text-sm error-message" id="error-edit-password_confirmation"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select id="editRoleId" name="role_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->nama) }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-edit-role_id"></span>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select id="editStatus" name="status" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-edit-status"></span>
                </div>
            </div>

            <!-- Jabatan & Line - hanya muncul kalau Role = Operator -->
            <div id="editOperatorFields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-200 pt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                    <select id="editJabatan" name="jabatan"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Jabatan</option>
                        <option value="Leader">Leader</option>
                        <option value="Asst Leader">Asst Leader</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-edit-jabatan"></span>
                </div>

                <!-- Line: custom checkbox-dropdown (gak pakai select2) -->
                <div class="relative" id="editLinesWrapper">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Line <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-400" id="editLineHint"></span>
                    </label>
                    <button type="button" onclick="toggleLineDropdown('edit')"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 text-left flex items-center justify-between focus:border-black focus:ring-2 focus:ring-black transition bg-white">
                        <span id="editLinesLabel" class="text-gray-400 truncate">Select Line</span>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="editLinesDropdown"
                        class="hidden absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                        <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                            <input type="text" oninput="filterLineOptions('edit', this.value)"
                                onclick="event.stopPropagation()"
                                placeholder="Cari line..."
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div id="editLinesOptionsList" class="max-h-48 overflow-y-auto">
                            @forelse($lines as $line)
                                <label class="edit-line-option flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm"
                                    data-search="{{ strtolower($line->nama_line . ' ' . $line->mesin) }}">
                                    <input type="checkbox" name="lines[]" value="{{ $line->id }}"
                                        class="edit-line-checkbox mr-2" onchange="handleLineCheckboxChange('edit', this)">
                                    <span>{{ $line->nama_line }}{{ $line->mesin ? ' — ' . $line->mesin : '' }}</span>
                                </label>
                            @empty
                                <p class="px-4 py-3 text-sm text-gray-400">Belum ada data Line</p>
                            @endforelse
                            <p id="editLinesNoMatch" class="hidden px-4 py-3 text-sm text-gray-400">Tidak ditemukan</p>
                        </div>
                    </div>
                    <span class="text-red-500 text-sm error-message" id="error-edit-lines"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeEditModal()"
                    class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>