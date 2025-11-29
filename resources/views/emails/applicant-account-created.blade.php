Dear {{ $fullName }},

Your ESSU Admission account has been successfully created.

ACCOUNT DETAILS:
- Applicant Reference Number: {{ $appRefNo ?? $applicant->app_ref_no ?? 'N/A' }}
- Username: {{ $username }}
- Password: {{ $temporaryPassword }}
- Campus: {{ $campusName }}

IMPORTANT REMINDER:
You must complete your profile BEFORE taking the exam. Please log in to your account and fill out all required information.

If you have any questions, please contact the admission office.

Best regards,
ESSU Admission System

