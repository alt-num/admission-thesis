<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>University Admission Application Form</title>
    <style>
        @page {
            margin: 6mm 10mm 6mm 10mm; /* top, right, bottom, left */
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }
        td, th {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }
        .no-border {
            border: none !important;
        }
        .header-container {
            margin-bottom: 8px;
            border: none;
        }
        .header-left {
            width: 65%;
            vertical-align: top;
            border: none;
        }
        .header-right {
            width: 35%;
            vertical-align: top;
            text-align: right;
            border: none;
        }
        .essu-logo {
            width: 75px;
            height: 75px;
            vertical-align: top;
        }
        .essu-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            font-family: "Times New Roman", serif;
        }
        .essu-motto {
            font-size: 11pt;
            margin: 3px 0;
            padding: 0;
            line-height: 1.3;
            font-family: "Times New Roman", serif;
        }
        .form-title {
            font-size: 13pt;
            font-weight: bold;
            margin: 8px 0 0 0;
            padding: 0;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .form-subtitle {
            font-size: 10pt;
            margin: 2px 0 0 0;
            padding: 0;
            line-height: 1.2;
        }
        .bagong-logo {
            width: 75px;
            height: 75px;
            margin-bottom: 8px;
        }
        .photo-box {
            width: 110px;
            height: 140px;
            border: 2px solid #000;
            margin: 0 auto;
            display: block;
            position: relative;
            background-color: #fff;
            overflow: hidden;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .label-cell {
            font-weight: bold;
            background-color: #f5f5f5;
            width: 20%;
            font-size: 8pt;
        }
        .value-cell {
            width: 30%;
            font-size: 9pt;
        }
        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            font-size: 9pt;
        }
        .checkbox {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            display: inline-block;
            margin-right: 3px;
            vertical-align: middle;
            text-align: center;
            line-height: 10px;
            font-size: 8px;
        }
        .checkbox.checked {
            background-color: #000;
            color: white;
        }
        .declaration-text {
            font-size: 8pt;
            line-height: 1.3;
            margin: 3px 0;
        }
        .signature-line {
            border-top: 1px solid #000;
            text-align: center;
            margin-top: 25px;
            padding-top: 3px;
            font-size: 8pt;
        }
        .footer-text {
            font-size: 7pt;
            line-height: 1.4;
            margin-top: 5px;
        }
        .version-info {
            font-size: 7pt;
            margin-top: 3px;
        }
        .empty-cell {
            min-height: 15px;
        }
        .evaluation-table {
            font-size: 8pt;
        }
        .evaluation-table td {
            padding: 2px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header Section - ROW 1: 3-Column Logo Layout -->
    <table class="no-border" style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 5px;">
        <tr>
            <td class="no-border" style="width: 120px; text-align: center; vertical-align: middle; padding: 5px;">
                @if(file_exists($essu_logo))
                    <img src="{{ $essu_logo }}" alt="ESSU Logo" style="width:90px;height:90px;object-fit:contain;">
                @endif
            </td>
            <td class="no-border" style="vertical-align: middle; padding: 5px 10px;">
                <p class="essu-name" style="margin: 0; padding: 0; margin-bottom: 3px;">EASTERN SAMAR STATE UNIVERSITY</p>
                <p class="essu-motto" style="margin: 0; padding: 0;">Excellence • Accountability • Service</p>
            </td>
            <td class="no-border" style="width: 120px; text-align: center; vertical-align: middle; padding: 5px;">
                @if(file_exists($bagong_logo))
                    <img src="{{ $bagong_logo }}" alt="Bagong Pilipinas" style="width:110px;height:110px;object-fit:contain;">
                @endif
            </td>
        </tr>
    </table>

    <!-- ROW 2: Form Title -->
    <table class="no-border" style="width: 100%; border: none; margin-bottom: 5px;">
        <tr>
            <td class="no-border" style="padding: 0; text-align: left;">
                <p class="form-title" style="margin: 0; padding: 0; margin-bottom: 2px;">UNIVERSITY ADMISSION APPLICATION FORM*</p>
                <p class="form-subtitle" style="margin: 0; padding: 0;">(Undergraduate Program)</p>
            </td>
        </tr>
    </table>

    <!-- APP_REF_NO Block with Photo Box (Combined with Program Choices) -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
        <tr>
            <td style="vertical-align: top; padding: 2px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
                    <tr>
                        <td class="label-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">APP_REF NO.</td>
                        <td class="value-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">{{ $applicant->app_ref_no ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">SCHOOL YEAR</td>
                        <td class="value-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">{{ $applicant->school_year ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">CAMPUS</td>
                        <td class="value-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">{{ $applicant->campus->campus_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="section-header" style="padding:4px 0; font-weight:bold; line-height:1.3; font-size: 8pt;" colspan="2">DEGREE/PROGRAM CHOICES</td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">FIRST CHOICE</td>
                        <td class="value-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">{{ $applicant->preferredCourse1->course_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">SECOND CHOICE</td>
                        <td class="value-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">{{ $applicant->preferredCourse2->course_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="padding:3px 4px; line-height:1.2; height:16px; font-size: 8pt;">THIRD CHOICE</td>
                        <td class="value-cell" style="padding:3px 4px; line-height:1.2; height:16px ; font-size: 8pt;">{{ $applicant->preferredCourse3->course_name ?? '' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 140px; vertical-align: top; text-align: center; padding: 2px;">
                <div style="width:120px;height:160px;border:1px solid black;margin:0 auto;overflow:hidden;background-color:#fff;">
                    @if($photo_path && file_exists($photo_path))
                        <img src="{{ $photo_path }}" alt="Applicant Photo" style="width:100%;height:100%;object-fit:cover;">
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Applicant Name -->
    <table>
        <tr>
            <th colspan="3" style="background:#ddd; text-align:center;">
                APPLICANT NAME (Please Print)
            </th>
        </tr>
        <tr>
            <td style="width:33%; padding:3px; font-weight:bold;">
                FAMILY NAME<br>
                <span style="font-weight:normal;">{{ $applicant->last_name ?? '' }}</span>
            </td>
            <td style="width:33%; padding:3px; font-weight:bold;">
                FIRST NAME<br>
                <span style="font-weight:normal;">{{ $applicant->first_name ?? '' }}</span>
            </td>
            <td style="width:33%; padding:3px; font-weight:bold;">
                MIDDLE NAME<br>
                <span style="font-weight:normal;">{{ $applicant->middle_name ?? '' }}</span>
            </td>
        </tr>
    </table>

    <!-- Contact and Personal Information -->
    <table>
        <tr>
            <td class="label-cell">EMAIL ADDRESS</td>
            <td class="value-cell">{{ $applicant->email ?? '' }}</td>
            <td class="label-cell">CONTACT NUMBER</td>
            <td class="value-cell">{{ $applicant->contact_number ?? '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">DATE OF BIRTH (mm/dd/yyyy)</td>
            <td class="value-cell">{{ $applicant->birth_date ? $applicant->birth_date->format('m/d/Y') : '' }}</td>
            <td class="label-cell">PLACE OF BIRTH (Town, Province)</td>
            <td class="value-cell">{{ $applicant->place_of_birth ?? '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">SEX</td>
            <td class="value-cell">{{ $applicant->sex ?? '' }}</td>
            <td class="label-cell">CIVIL STATUS</td>
            <td class="value-cell">{{ $applicant->civil_status ?? '' }}</td>
        </tr>
    </table>

    <!-- Permanent Address -->
    <table>
        <tr>
            <td class="section-header" colspan="4">PERMANENT ADDRESS</td>
        </tr>
        <tr>
            <td class="label-cell">BARANGAY</td>
            <td class="value-cell">{{ $applicant->barangay ?? '' }}</td>
            <td class="label-cell">MUNICIPALITY</td>
            <td class="value-cell">{{ $applicant->municipality ?? '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">PROVINCE</td>
            <td class="value-cell" colspan="3">{{ $applicant->province ?? '' }}</td>
        </tr>
    </table>

    <!-- Last School Attended -->
    <table>
        <tr>
            <td class="section-header" colspan="4">LAST SCHOOL ATTENDED</td>
        </tr>
        <tr>
            <!-- NAME OF SCHOOL -->
            <td colspan="2" style="padding:3px; vertical-align:top;">
                <span style="font-weight:bold;">NAME OF SCHOOL</span><br>
                <span style="font-weight:normal;">{{ $applicant->last_school_attended ?? '' }}</span>
            </td>
            <!-- ADDRESS -->
            <td colspan="2" style="padding:3px; vertical-align:top;">
                <span style="font-weight:bold;">ADDRESS</span><br>
                <span style="font-weight:normal;">{{ $applicant->school_address ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">YEAR</td>
            <td class="value-cell">{{ $applicant->year_graduated ?? '' }}</td>
            <td class="label-cell">GEN. AVERAGE</td>
            <td class="value-cell">{{ $applicant->gen_average ?? '' }}</td>
        </tr>
    </table>

    <!-- Declaration and Evaluation Section -->
    <table>
        <tr>
            <td style="width: 50%; border-right: 2px solid #000; vertical-align: top;">
                <table style="border: none; width: 100%;">
                    <!-- DECLARATION HEADER -->
                    <tr>
                        <th colspan="4" style="background:#ddd; text-align:center; border: 1px solid #000;">
                            DECLARATION
                        </th>
                    </tr>
                    <!-- PHYSICAL CONDITION -->
                    <tr>
                        <td colspan="4" style="padding:3px; font-weight:bold; border: 1px solid #000;">
                            Do you have a physical condition which may affect your performance in College?
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:3px; border: 1   px solid #000;">
                            @if($applicant->declaration)
                                @if($applicant->declaration->physical_condition_flag)
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;background:#000;"></span> YES
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> NO
                                @else
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> YES
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;background:#000;"></span> NO
                                @endif
                            @else
                                <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> YES
                                &nbsp;&nbsp;&nbsp;
                                <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> NO
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:4px; padding-top:0; border: 1px solid #000;">
                            <strong>If yes, please state the physical condition:</strong>
                            @if($applicant->declaration && $applicant->declaration->physical_condition_desc)
                                {{ $applicant->declaration->physical_condition_desc }}
                            @endif
                        </td>
                    </tr>
                    <!-- DISCIPLINARY ACTION -->
                    <tr>
                        <td colspan="4" style="padding:3px; font-weight:bold; border: 1px solid #000;">
                            Have you been subjected to any disciplinary action?
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:3px; border: 1px solid #000;">
                            @if($applicant->declaration)
                                @if($applicant->declaration->disciplinary_action_flag)
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;background:#000;"></span> YES
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> NO
                                @else
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> YES
                                    &nbsp;&nbsp;&nbsp;
                                    <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;background:#000;"></span> NO
                                @endif
                            @else
                                <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> YES
                                &nbsp;&nbsp;&nbsp;
                                <span style="display:inline-block;width:10px;height:10px;border:1px solid #000;"></span> NO
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:4px; padding-top:0; border: 1px solid #000;">
                            <strong>If yes, please state the disciplinary action:</strong>
                            @if($applicant->declaration && $applicant->declaration->disciplinary_action_desc)
                                {{ $applicant->declaration->disciplinary_action_desc }}
                            @endif
                        </td>
                    </tr>
                    <!-- PRIVACY NOTE -->
                    <tr>
                        <td colspan="4" style="padding:4px; font-size:10px; border: 1px solid #000;">
                            The information on this form will be used in accordance with the University's policy on personal data.
                        </td>
                    </tr>
                    <!-- CERTIFICATION -->
                    <tr>
                        <td colspan="4" style="padding:4px; border: 1px solid #000;">
                            <div class="declaration-text">
                                I certify that the information above is true, complete and correct. I understand that falsification or withholding of information on this form will nullify and/or subject me to dismissal from the University.
                            </div>
                        </td>
                    </tr>
                    <!-- SIGNATURE -->
                    <tr>
                        <td colspan="4" style="padding-top:5px; padding-bottom:5px; text-align:center;">

                            <!-- Printed Name ABOVE the line -->
                            <div style="font-size:11px; margin-bottom:0;">
                                {{ $declaration_signature_name }}
                            </div>

                            <!-- Signature Line WITHIN the same block -->
                            <div style="border-bottom:1px solid #000; width:100%; line-height:0; margin-top:;"></div>

                            <div style="font-weight:bold; font-size:11px; margin-top:2px;">
                                SIGNATURE OVER PRINTED NAME
                            </div>

                            <!-- Date -->
                            <div style="border-bottom:1px solid #000; width:100%; height:14px; margin-top:10px;">
                                <div style="font-size:9pt; padding-top:2px;">{{ $declaration_date }}</div>
                            </div>
                            <div style="font-weight:bold; font-size:11px; margin-top:2px;">
                                DATE
                            </div>

                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table style="border: none; width: 100%;">
                    <tr>
                        <td class="section-header" style="border: 1px solid #000; font-size: 8pt;">
                            To be filled-up by ATO & evaluating college (Date and Signature only)
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">
                            <table class="evaluation-table" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="border: 1px solid #000; font-weight: bold;">Degree/Program</td>
                                    <td style="border: 1px solid #000; font-weight: bold;">1st choice</td>
                                    <td style="border: 1px solid #000; font-weight: bold;">2nd choice</td>
                                    <td style="border: 1px solid #000; font-weight: bold;">3rd choice</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; font-weight: bold;">Admitted</td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; font-weight: bold;">Forwarded to Another College</td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; font-weight: bold;">Not Admitted</td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; font-weight: bold;">Others</td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                    <td style="border: 1px solid #000;" class="empty-cell"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">
                            <div style="font-size: 8pt; font-weight: bold; margin-bottom: 3px;">REMARKS</div>
                            <div style="font-size: 8pt; margin-top: 10px;">
                                Admitted in the College of <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 100px;">&nbsp;</span> degree of <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 100px;">&nbsp;</span> with the <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 80px;">&nbsp;</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">
                            <div class="signature-line">
                                <strong>HEAD, ADMISSION SERVICES OFFICE</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">
                            <div class="signature-line">
                                <strong>DATE</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Instructions -->
    <table class="no-border" style="margin-top: 5px;">
        <tr>
            <td class="no-border" style="font-size: 7pt; line-height: 1.4;">
                <div class="footer-text">
                    *Please attach a photocopy of your Form 138 (Report Card) and Certificate of Good Moral Character. When complete please submit/email to The University Admission & Testing Office (Borongan Campus) /admission@essu.edu.ph. For enquiries, contact Admission and Testing Office at 0951 484 3389.
                </div>
                <div class="version-info">
                    ESSU-ACAD-200.a | Version 5<br>
                    Effective: March 15, 2024
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
