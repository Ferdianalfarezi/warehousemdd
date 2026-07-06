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

            <!-- Username -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                <input type="text" id="createUsername" name="username" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Enter username">
                <span class="text-red-500 text-sm error-message" id="error-create-username"></span>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <input type="password" id="createPassword" name="password" required
                        class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter password">

                    <button type="button"
                            onclick="togglePassword('createPassword', 'createPasswordIcon')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    </button>
                </div>

                <span class="text-red-500 text-sm error-message" id="error-create-password"></span>
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <input type="password" id="createPasswordConfirmation" name="password_confirmation" required
                        class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Confirm password">

                    <button type="button"
                            onclick="togglePassword('createPasswordConfirmation', 'createPasswordConfirmIcon')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    </button>
                </div>

                <span class="text-red-500 text-sm error-message" id="error-create-password_confirmation"></span>
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

            <!-- Jabatan & Line - hanya muncul kalau Role = Operator -->
            <div id="createOperatorFields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-200 pt-4">
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

                <!-- Line: custom checkbox-dropdown (gak pakai select2) -->
                <div class="relative" id="createLinesWrapper">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Line <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-400" id="createLineHint"></span>
                    </label>
                    <button type="button" onclick="toggleLineDropdown('create')"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 text-left flex items-center justify-between focus:border-black focus:ring-2 focus:ring-black transition bg-white">
                        <span id="createLinesLabel" class="text-gray-400 truncate">Select Line</span>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="createLinesDropdown"
                        class="hidden absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                        <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                            <input type="text" oninput="filterLineOptions('create', this.value)"
                                onclick="event.stopPropagation()"
                                placeholder="Cari line..."
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div id="createLinesOptionsList" class="max-h-48 overflow-y-auto">
                            @forelse($lines as $line)
                                <label class="create-line-option flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm"
                                    data-search="{{ strtolower($line->nama_line . ' ' . $line->mesin) }}">
                                    <input type="checkbox" name="lines[]" value="{{ $line->id }}"
                                        class="create-line-checkbox mr-2" onchange="handleLineCheckboxChange('create', this)">
                                    <span>{{ $line->nama_line }}{{ $line->mesin ? ' — ' . $line->mesin : '' }}</span>
                                </label>
                            @empty
                                <p class="px-4 py-3 text-sm text-gray-400">Belum ada data Line</p>
                            @endforelse
                            <p id="createLinesNoMatch" class="hidden px-4 py-3 text-sm text-gray-400">Tidak ditemukan</p>
                        </div>
                    </div>
                    <span class="text-red-500 text-sm error-message" id="error-create-lines"></span>
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