@extends('layouts.applicant')

@section('title', 'Complete Your Profile - ESSU Applicant Portal')

@push('head')
<script src="/js/alpine.js" defer></script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Complete Your Profile</h2>
            <p class="mt-2 text-sm text-gray-600">Please provide your accurate information. Fields marked with * are required.</p>
        </div>

        <form action="{{ route('applicant.profile.complete.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- ID Photo Upload -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ID Photo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">
                            Upload ID Photo *
                            @if($applicant->photo_path)
                                <span class="text-green-600 text-xs">(Current photo uploaded)</span>
                            @endif
                        </label>
                        <input type="file" 
                               name="photo" 
                               id="photo" 
                               accept="image/png,image/jpg,image/jpeg"
                               {{ empty($applicant->photo_path) ? 'required' : '' }}
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="mt-1 text-xs text-gray-500">Accepted formats: PNG, JPG, JPEG. Maximum size: 2MB</p>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <img id="photoPreview"
                             src="{{ $applicant->photo_path ? asset('storage/' . $applicant->photo_path) : '' }}"
                             alt="Photo Preview"
                             style="display: {{ $applicant->photo_path ? 'block' : 'none' }};
                                    width: 120px;
                                    margin-top: 10px;
                                    border-radius: 8px;
                                    border: 1px solid #ccc;">
                        @if($applicant->examAttempts()->exists())
                            <p class="mt-2 text-sm text-yellow-600">Note: Photo cannot be changed after taking the exam.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                
                @php
                    $hasExamAttempt = $applicant->examAttempts()->exists();
                    $needsRevision = $applicant->needs_revision ?? false;
                    $restrictedAfterExam = $needsRevision && $hasExamAttempt;
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                        <input type="text" 
                               name="first_name" 
                               id="first_name" 
                               value="{{ old('first_name', $applicant->first_name) }}"
                               {{ $restrictedAfterExam ? 'readonly' : 'required' }}
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" 
                               name="middle_name" 
                               id="middle_name" 
                               value="{{ old('middle_name', $applicant->middle_name) }}"
                               {{ $restrictedAfterExam ? 'readonly' : '' }}
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        @error('middle_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                        <input type="text" 
                               name="last_name" 
                               id="last_name" 
                               value="{{ old('last_name', $applicant->last_name) }}"
                               {{ $restrictedAfterExam ? 'readonly' : 'required' }}
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date *</label>
                        <input type="date" 
                               name="birth_date" 
                               id="birth_date" 
                               value="{{ old('birth_date', $applicant->birth_date?->format('Y-m-d')) }}"
                               {{ $restrictedAfterExam ? 'readonly' : 'required' }}
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Place of Birth -->
                    <div>
                        <label for="place_of_birth" class="block text-sm font-medium text-gray-700">Place of Birth (Town, Province)*</label>
                        <input type="text" 
                               name="place_of_birth" 
                               id="place_of_birth" 
                               value="{{ old('place_of_birth', $applicant->place_of_birth) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('place_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sex -->
                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700">Sex *</label>
                        <select name="sex" 
                                id="sex" 
                                {{ $restrictedAfterExam ? 'disabled' : 'required' }}
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                            <option value="">Select...</option>
                            <option value="Male" {{ old('sex', $applicant->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $applicant->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @if($restrictedAfterExam)
                            <input type="hidden" name="sex" value="{{ $applicant->sex }}">
                        @endif
                        @error('sex')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Civil Status -->
                    <div>
                        <label for="civil_status" class="block text-sm font-medium text-gray-700">Civil Status *</label>
                        <select name="civil_status" 
                                id="civil_status" 
                                {{ $restrictedAfterExam ? 'disabled' : 'required' }}
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                            <option value="">Select...</option>
                            <option value="Single" {{ old('civil_status', $applicant->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status', $applicant->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('civil_status', $applicant->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status', $applicant->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @if($restrictedAfterExam)
                            <input type="hidden" name="civil_status" value="{{ $applicant->civil_status }}">
                        @endif
                        @error('civil_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $applicant->email) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number *</label>
                        <input type="text" 
                               name="contact_number" 
                               id="contact_number" 
                               value="{{ old('contact_number', $applicant->contact_number) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Barangay -->
                    <div>
                        <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay *</label>
                        <input type="text" 
                               name="barangay" 
                               id="barangay" 
                               value="{{ old('barangay', $applicant->barangay) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('barangay')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Municipality -->
                    <div>
                        <label for="municipality" class="block text-sm font-medium text-gray-700">Municipality *</label>
                        <input type="text" 
                               name="municipality" 
                               id="municipality" 
                               value="{{ old('municipality', $applicant->municipality) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('municipality')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Province -->
                    <div class="md:col-span-2">
                        <label for="province" class="block text-sm font-medium text-gray-700">Province *</label>
                        <input type="text" 
                               name="province" 
                               id="province" 
                               value="{{ old('province', $applicant->province) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Educational Background -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Educational Background</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Last School Attended -->
                    <div>
                        <label for="last_school_attended" class="block text-sm font-medium text-gray-700">Last School Attended *</label>
                        <input type="text" 
                               name="last_school_attended" 
                               id="last_school_attended" 
                               value="{{ old('last_school_attended', $applicant->last_school_attended) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('last_school_attended')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- School Address -->
                    <div>
                        <label for="school_address" class="block text-sm font-medium text-gray-700">School Address *</label>
                        <input type="text" 
                               name="school_address" 
                               id="school_address" 
                               value="{{ old('school_address', $applicant->school_address) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('school_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Year Graduated -->
                    <div>
                        <label for="year_graduated" class="block text-sm font-medium text-gray-700">Year Graduated *</label>
                        <input type="number" 
                               name="year_graduated" 
                               id="year_graduated" 
                               value="{{ old('year_graduated', $applicant->year_graduated) }}"
                               min="1950"
                               max="{{ date('Y') + 1 }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('year_graduated')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- General Average -->
                    <div>
                        <label for="gen_average" class="block text-sm font-medium text-gray-700">General Average *</label>
                        <input type="number" 
                               name="gen_average" 
                               id="gen_average" 
                               value="{{ old('gen_average', $applicant->gen_average) }}"
                               min="65"
                               max="100"
                               step="0.01"
                               {{ $restrictedAfterExam ? 'readonly' : 'required' }}
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm {{ $restrictedAfterExam ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        @error('gen_average')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Preferred Courses -->
            <div class="pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Preferred Courses</h3>
                <p class="text-sm text-gray-600 mb-4">Please select three different courses in order of preference. Type to search.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Preferred Course 1 -->
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: {{ old('preferred_course_1', $applicant->preferred_course_1) ?: 'null' }},
                        selectedText: '{{ old('preferred_course_1', $applicant->preferred_course_1) ? $courses->firstWhere('course_id', old('preferred_course_1', $applicant->preferred_course_1))->course_code . ' - ' . $courses->firstWhere('course_id', old('preferred_course_1', $applicant->preferred_course_1))->course_name : '' }}',
                        courses: {{ $courses->map(fn($c) => ['id' => $c->course_id, 'text' => $c->course_code . ' - ' . $c->course_name])->toJson() }},
                        restricted: {{ $restrictedAfterExam ? 'true' : 'false' }},
                        get filteredCourses() {
                            if (this.search === '') return this.courses;
                            return this.courses.filter(course => 
                                course.text.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        selectCourse(course) {
                            if (this.restricted) return;
                            this.selected = course.id;
                            this.selectedText = course.text;
                            this.open = false;
                            this.search = '';
                        }
                    }" class="relative">
                        <label for="preferred_course_1" class="block text-sm font-medium text-gray-700">1st Choice *</label>
                        
                        <!-- Hidden select for form submission -->
                        <select name="preferred_course_1" 
                                id="preferred_course_1" 
                                x-model="selected"
                                required
                                class="hidden">
                            <option value="">Select course...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->course_id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                            @endforeach
                        </select>

                        <!-- Custom searchable dropdown -->
                        <div class="mt-1">
                            <button type="button"
                                    @click="if (!restricted) open = !open"
                                    :disabled="restricted"
                                    :class="restricted ? 'bg-gray-100 cursor-not-allowed' : 'bg-white cursor-pointer'"
                                    class="relative w-full border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <span class="block truncate" x-text="selectedText || 'Select course...'"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </span>
                            </button>

                            <!-- Dropdown panel -->
                            <div x-show="open && !restricted"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                <!-- Search input -->
                                <div class="sticky top-0 bg-white p-2 border-b">
                                    <input type="text"
                                           x-model="search"
                                           placeholder="Type to search..."
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <!-- Options list -->
                                <template x-for="course in filteredCourses" :key="course.id">
                                    <div @click="selectCourse(course)"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-green-50"
                                         :class="{ 'bg-green-100': selected === course.id }">
                                        <span class="block truncate" x-text="course.text"></span>
                                        <span x-show="selected === course.id" class="absolute inset-y-0 right-0 flex items-center pr-4 text-green-600">
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
                        
                        @error('preferred_course_1')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Preferred Course 2 -->
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: {{ old('preferred_course_2', $applicant->preferred_course_2) ?: 'null' }},
                        selectedText: '{{ old('preferred_course_2', $applicant->preferred_course_2) ? $courses->firstWhere('course_id', old('preferred_course_2', $applicant->preferred_course_2))->course_code . ' - ' . $courses->firstWhere('course_id', old('preferred_course_2', $applicant->preferred_course_2))->course_name : '' }}',
                        courses: {{ $courses->map(fn($c) => ['id' => $c->course_id, 'text' => $c->course_code . ' - ' . $c->course_name])->toJson() }},
                        restricted: {{ $restrictedAfterExam ? 'true' : 'false' }},
                        get filteredCourses() {
                            if (this.search === '') return this.courses;
                            return this.courses.filter(course => 
                                course.text.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        selectCourse(course) {
                            if (this.restricted) return;
                            this.selected = course.id;
                            this.selectedText = course.text;
                            this.open = false;
                            this.search = '';
                        }
                    }" class="relative">
                        <label for="preferred_course_2" class="block text-sm font-medium text-gray-700">2nd Choice *</label>
                        
                        <!-- Hidden select for form submission -->
                        <select name="preferred_course_2" 
                                id="preferred_course_2" 
                                x-model="selected"
                                {{ $restrictedAfterExam ? 'disabled' : 'required' }}
                                class="hidden">
                            <option value="">Select course...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->course_id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                            @endforeach
                        </select>

                        <!-- Custom searchable dropdown -->
                        <div class="mt-1">
                            <button type="button"
                                    @click="if (!restricted) open = !open"
                                    :disabled="restricted"
                                    :class="restricted ? 'bg-gray-100 cursor-not-allowed' : 'bg-white cursor-pointer'"
                                    class="relative w-full border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <span class="block truncate" x-text="selectedText || 'Select course...'"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </span>
                            </button>

                            <!-- Dropdown panel -->
                            <div x-show="open && !restricted"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                <!-- Search input -->
                                <div class="sticky top-0 bg-white p-2 border-b">
                                    <input type="text"
                                           x-model="search"
                                           placeholder="Type to search..."
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <!-- Options list -->
                                <template x-for="course in filteredCourses" :key="course.id">
                                    <div @click="selectCourse(course)"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-green-50"
                                         :class="{ 'bg-green-100': selected === course.id }">
                                        <span class="block truncate" x-text="course.text"></span>
                                        <span x-show="selected === course.id" class="absolute inset-y-0 right-0 flex items-center pr-4 text-green-600">
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
                        
                        @error('preferred_course_2')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>

                    <!-- Preferred Course 3 -->
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: {{ old('preferred_course_3', $applicant->preferred_course_3) ?: 'null' }},
                        selectedText: '{{ old('preferred_course_3', $applicant->preferred_course_3) ? $courses->firstWhere('course_id', old('preferred_course_3', $applicant->preferred_course_3))->course_code . ' - ' . $courses->firstWhere('course_id', old('preferred_course_3', $applicant->preferred_course_3))->course_name : '' }}',
                        courses: {{ $courses->map(fn($c) => ['id' => $c->course_id, 'text' => $c->course_code . ' - ' . $c->course_name])->toJson() }},
                        restricted: {{ $restrictedAfterExam ? 'true' : 'false' }},
                        get filteredCourses() {
                            if (this.search === '') return this.courses;
                            return this.courses.filter(course => 
                                course.text.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        selectCourse(course) {
                            if (this.restricted) return;
                            this.selected = course.id;
                            this.selectedText = course.text;
                            this.open = false;
                            this.search = '';
                        }
                    }" class="relative">
                        <label for="preferred_course_3" class="block text-sm font-medium text-gray-700">3rd Choice *</label>
                        
                        <!-- Hidden select for form submission -->
                        <select name="preferred_course_3" 
                                id="preferred_course_3" 
                                x-model="selected"
                                {{ $restrictedAfterExam ? 'disabled' : 'required' }}
                                class="hidden">
                            <option value="">Select course...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->course_id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                            @endforeach
                        </select>

                        @if($restrictedAfterExam)
                            <input type="hidden" name="preferred_course_3" value="{{ $applicant->preferred_course_3 }}">
                        @endif
                        <!-- Custom searchable dropdown -->
                        <div class="mt-1">
                            <button type="button"
                                    @click="if (!restricted) open = !open"
                                    :disabled="restricted"
                                    :class="restricted ? 'bg-gray-100 cursor-not-allowed' : 'bg-white cursor-pointer'"
                                    class="relative w-full border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <span class="block truncate" x-text="selectedText || 'Select course...'"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </span>
                            </button>

                            <!-- Dropdown panel -->
                            <div x-show="open && !restricted"
                                 @click.away="if (!restricted) open = false"
                                 x-transition
                                 class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                <!-- Search input -->
                                <div class="sticky top-0 bg-white p-2 border-b">
                                    <input type="text"
                                           x-model="search"
                                           placeholder="Type to search..."
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <!-- Options list -->
                                <template x-for="course in filteredCourses" :key="course.id">
                                    <div @click="selectCourse(course)"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-green-50"
                                         :class="{ 'bg-green-100': selected === course.id }">
                                        <span class="block truncate" x-text="course.text"></span>
                                        <span x-show="selected === course.id" class="absolute inset-y-0 right-0 flex items-center pr-4 text-green-600">
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
                        
                        @error('preferred_course_3')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($restrictedAfterExam)
                            <p class="mt-1 text-xs text-gray-500">Cannot be changed after exam</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    Save Profile & Continue
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('photo').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('photoPreview');
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>
@endsection

