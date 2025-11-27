<!-- Add Section Modal -->
<div id="addSectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Section</h3>
            <button onclick="window.closeAddSectionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="addSectionForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number (optional)</label>
                <input type="number" name="order_no" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeAddSectionModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editSectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Section</h3>
            <button onclick="window.closeEditSectionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="editSectionForm">
            @csrf
            <input type="hidden" id="editSectionId" name="section_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="editSectionName" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                <input type="number" id="editSectionOrderNo" name="order_no" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeEditSectionModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Subsection Modal -->
<div id="addSubsectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Subsection</h3>
            <button onclick="window.closeAddSubsectionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="addSubsectionForm">
            @csrf
            <input type="hidden" id="addSubsectionSectionId" name="section_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number (optional)</label>
                <input type="number" name="order_no" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeAddSubsectionModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Subsection Modal -->
<div id="editSubsectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Subsection</h3>
            <button onclick="window.closeEditSubsectionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="editSubsectionForm">
            @csrf
            <input type="hidden" id="editSubsectionId" name="subsection_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="editSubsectionName" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                <input type="number" id="editSubsectionOrderNo" name="order_no" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeEditSubsectionModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Question</h3>
            <button onclick="window.closeAddQuestionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="addQuestionForm">
            @csrf
            <input type="hidden" id="addQuestionSubsectionId" name="subsection_id">
            <input type="hidden" id="addQuestionSectionId" name="section_id">
            <input type="hidden" id="addQuestionToSection" value="0">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                <textarea name="question_text" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Image (optional)</label>
                <input type="file" name="question_image" accept="image/png,image/jpeg,image/jpg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">PNG or JPG, max 2MB</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="addQuestionType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" onchange="window.toggleTrueFalseOptions('add')">
                    <option value="MCQ">MCQ</option>
                    <option value="TRUE_FALSE">True/False</option>
                </select>
            </div>
            <div id="addQuestionTrueFalseOptions" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correct Answer</label>
                <select name="correct_answer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="True">True</option>
                    <option value="False">False</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number (optional)</label>
                <input type="number" name="order_no" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeAddQuestionModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="button" id="addQuestionCreateAndAddAnother" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Create & Add Another</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Question Modal -->
<div id="editQuestionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Question</h3>
            <button onclick="window.closeEditQuestionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="editQuestionForm">
            @csrf
            <input type="hidden" id="editQuestionId" name="question_id">
            <input type="hidden" id="editQuestionHasImage" value="0">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                <textarea id="editQuestionText" name="question_text" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <div id="editQuestionImagePreview" class="hidden mb-2">
                    <img id="editQuestionImageThumbnail" src="" alt="Question Image" class="w-32 h-auto rounded border">
                    <label class="flex items-center mt-2">
                        <input type="checkbox" name="remove_question_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-red-600">Remove existing image</span>
                    </label>
                </div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Image (optional)</label>
                <input type="file" name="question_image" accept="image/png,image/jpeg,image/jpg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">PNG or JPG, max 2MB</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="editQuestionType" name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" onchange="window.toggleTrueFalseOptions('edit')">
                    <option value="MCQ">MCQ</option>
                    <option value="TRUE_FALSE">True/False</option>
                </select>
            </div>
            <div id="editQuestionTrueFalseOptions" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correct Answer</label>
                <select id="editQuestionCorrectAnswer" name="correct_answer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="True">True</option>
                    <option value="False">False</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                <input type="number" id="editQuestionOrderNo" name="order_no" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeEditQuestionModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Choice Modal -->
<div id="addChoiceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Choice</h3>
            <button onclick="window.closeAddChoiceModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Question Preview -->
        <div id="addChoiceQuestionPreview" class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <div class="mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Question Preview</span>
            </div>
            <div id="addChoiceQuestionSection" class="text-xs text-gray-600 mb-1"></div>
            <div id="addChoiceQuestionSubsection" class="text-xs text-gray-600 mb-2"></div>
            <p id="addChoiceQuestionText" class="text-sm text-gray-900 font-medium"></p>
        </div>
        <form id="addChoiceForm">
            @csrf
            <input type="hidden" id="addChoiceQuestionId" name="question_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Choice Text</label>
                <input type="text" name="choice_text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Choice Image (optional)</label>
                <input type="file" name="choice_image" accept="image/png,image/jpeg,image/jpg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">PNG or JPG, max 2MB</p>
            </div>
            <div class="mb-4">
                <input type="hidden" name="is_correct" value="0">
                <label class="flex items-center">
                    <input type="checkbox" name="is_correct" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Is Correct Answer</span>
                </label>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeAddChoiceModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="button" id="addChoiceCreateAndAddAnother" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Create & Add Another</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Choice Modal -->
<div id="editChoiceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[90%] md:w-[600px] lg:w-[700px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Choice</h3>
            <button onclick="window.closeEditChoiceModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Question Preview -->
        <div id="editChoiceQuestionPreview" class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg max-h-40 overflow-y-auto">
            <div class="mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Question Preview</span>
            </div>
            <div id="editChoiceQuestionSection" class="text-xs text-gray-600 mb-1"></div>
            <div id="editChoiceQuestionSubsection" class="text-xs text-gray-600 mb-2"></div>
            <p id="editChoiceQuestionText" class="text-sm text-gray-900 font-medium"></p>
        </div>
        <form id="editChoiceForm">
            @csrf
            <input type="hidden" id="editChoiceId" name="choice_id">
            <input type="hidden" id="editChoiceHasImage" value="0">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Choice Text</label>
                <input type="text" id="editChoiceText" name="choice_text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <div id="editChoiceImagePreview" class="hidden mb-2">
                    <img id="editChoiceImageThumbnail" src="" alt="Choice Image" class="w-32 h-auto rounded border">
                    <label class="flex items-center mt-2">
                        <input type="checkbox" name="remove_choice_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-red-600">Remove existing image</span>
                    </label>
                </div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Choice Image (optional)</label>
                <input type="file" name="choice_image" accept="image/png,image/jpeg,image/jpg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">PNG or JPG, max 2MB</p>
            </div>
            <div class="mb-4">
                <input type="hidden" name="is_correct" value="0">
                <label class="flex items-center">
                    <input type="checkbox" id="editChoiceIsCorrect" name="is_correct" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Is Correct Answer</span>
                </label>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.closeEditChoiceModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

