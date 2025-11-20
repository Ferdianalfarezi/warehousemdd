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