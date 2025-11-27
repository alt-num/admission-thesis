@props(['name', 'label', 'courses', 'selected' => null, 'required' => false, 'error' => null])

<div x-data="{
    open: false,
    search: '',
    selected: {{ $selected ?: 'null' }},
    selectedText: '{{ $selected && $courses->firstWhere('course_id', $selected) ? $courses->firstWhere('course_id', $selected)->course_code . ' - ' . $courses->firstWhere('course_id', $selected)->course_name : '' }}',
    courses: {{ $courses->map(fn($c) => ['id' => $c->course_id, 'text' => $c->course_code . ' - ' . $c->course_name])->toJson() }},
    get filteredCourses() {
        if (this.search === '') return this.courses;
        return this.courses.filter(course => 
            course.text.toLowerCase().includes(this.search.toLowerCase())
        );
    },
    selectCourse(course) {
        this.selected = course.id;
        this.selectedText = course.text;
        this.open = false;
        this.search = '';
    }
}" class="relative">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    
    <!-- Hidden select for form submission -->
    <select name="{{ $name }}" 
            id="{{ $name }}" 
            x-model="selected"
            @if($required) required @endif
            class="hidden">
        <option value="">Select course...</option>
        @foreach($courses as $course)
            <option value="{{ $course->course_id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
        @endforeach
    </select>

    <!-- Custom searchable dropdown -->
    <div class="mt-1">
        <button type="button"
                @click="open = !open"
                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <span class="block truncate" x-text="selectedText || 'Select course...'"></span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </span>
        </button>

        <!-- Dropdown panel -->
        <div x-show="open"
             @click.away="open = false"
             x-transition
             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
            <!-- Search input -->
            <div class="sticky top-0 bg-white p-2 border-b">
                <input type="text"
                       x-model="search"
                       placeholder="Type to search..."
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            
            <!-- Options list -->
            <template x-for="course in filteredCourses" :key="course.id">
                <div @click="selectCourse(course)"
                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-50"
                     :class="{ 'bg-blue-100': selected === course.id }">
                    <span class="block truncate" x-text="course.text"></span>
                    <span x-show="selected === course.id" class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </div>
            </template>
            
            <div x-show="filteredCourses.length === 0" class="py-2 px-3 text-sm text-gray-500">
                No courses found
            </div>
        </div>
    </div>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

