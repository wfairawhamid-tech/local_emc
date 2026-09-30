<?php

namespace App\Libraries;

/**
 * DocumentTranslator
 * Centralized presentation translation layer for Medical Documents (Time Slip & Medical Certificate).
 * Dynamically converts departments, clinics, relationships, purposes, and labels to 100% pure
 * English (BI) or 100% pure Bahasa Malaysia (BM) without modifying canonical database records.
 */
class DocumentTranslator
{
    /**
     * Department and Clinic translation dictionary
     */
    protected static array $departmentDictionary = [
        'perubatan am' => [
            'en'        => 'General Medicine',
            'bm'        => 'Perubatan Am',
            'clinic_en' => 'General Medicine',
            'clinic_bm' => 'Perubatan Am',
        ],
        'jabatan perubatan am' => [
            'en'        => 'Department of General Medicine',
            'bm'        => 'Jabatan Perubatan Am',
            'clinic_en' => 'General Medicine',
            'clinic_bm' => 'Perubatan Am',
        ],
        'klinik perubatan am' => [
            'en'        => 'General Medicine Clinic',
            'bm'        => 'Klinik Perubatan Am',
            'clinic_en' => 'General Medicine Clinic',
            'clinic_bm' => 'Klinik Perubatan Am',
        ],
        'jabatan kecemasan' => [
            'en'        => 'Emergency Department',
            'bm'        => 'Jabatan Kecemasan',
            'clinic_en' => 'Emergency Department',
            'clinic_bm' => 'Jabatan Kecemasan',
        ],
        'jabatan kecemasan & trauma' => [
            'en'        => 'Emergency & Trauma Department',
            'bm'        => 'Jabatan Kecemasan & Trauma',
            'clinic_en' => 'Emergency & Trauma Department',
            'clinic_bm' => 'Jabatan Kecemasan & Trauma',
        ],
        'kecemasan & trauma' => [
            'en'        => 'Emergency & Trauma Department',
            'bm'        => 'Jabatan Kecemasan & Trauma',
            'clinic_en' => 'Emergency & Trauma Department',
            'clinic_bm' => 'Jabatan Kecemasan & Trauma',
        ],
        'kecemasan' => [
            'en'        => 'Emergency Department',
            'bm'        => 'Jabatan Kecemasan',
            'clinic_en' => 'Emergency Department',
            'clinic_bm' => 'Jabatan Kecemasan',
        ],
        'klinik surgeri' => [
            'en'        => 'Surgery Clinic',
            'bm'        => 'Klinik Surgeri',
            'clinic_en' => 'Surgery Clinic',
            'clinic_bm' => 'Klinik Surgeri',
        ],
        'jabatan surgeri' => [
            'en'        => 'Department of Surgery',
            'bm'        => 'Jabatan Surgeri',
            'clinic_en' => 'Surgery',
            'clinic_bm' => 'Surgeri',
        ],
        'surgeri' => [
            'en'        => 'Surgery Clinic',
            'bm'        => 'Klinik Surgeri',
            'clinic_en' => 'Surgery Clinic',
            'clinic_bm' => 'Klinik Surgeri',
        ],
        'pembedahan' => [
            'en'        => 'Surgery Clinic',
            'bm'        => 'Klinik Pembedahan',
            'clinic_en' => 'Surgery Clinic',
            'clinic_bm' => 'Klinik Pembedahan',
        ],
        'klinik ortopedik' => [
            'en'        => 'Orthopaedic Clinic',
            'bm'        => 'Klinik Ortopedik',
            'clinic_en' => 'Orthopaedic Clinic',
            'clinic_bm' => 'Klinik Ortopedik',
        ],
        'jabatan ortopedik' => [
            'en'        => 'Department of Orthopaedics',
            'bm'        => 'Jabatan Ortopedik',
            'clinic_en' => 'Orthopaedics',
            'clinic_bm' => 'Ortopedik',
        ],
        'ortopedik' => [
            'en'        => 'Orthopaedic Clinic',
            'bm'        => 'Klinik Ortopedik',
            'clinic_en' => 'Orthopaedic Clinic',
            'clinic_bm' => 'Klinik Ortopedik',
        ],
        'perubatan keluarga' => [
            'en'        => 'Family Medicine',
            'bm'        => 'Perubatan Keluarga',
            'clinic_en' => 'Family Medicine',
            'clinic_bm' => 'Perubatan Keluarga',
        ],
        'jabatan perubatan keluarga' => [
            'en'        => 'Department of Family Medicine',
            'bm'        => 'Jabatan Perubatan Keluarga',
            'clinic_en' => 'Family Medicine',
            'clinic_bm' => 'Perubatan Keluarga',
        ],
        'klinik perubatan keluarga' => [
            'en'        => 'Family Medicine Clinic',
            'bm'        => 'Klinik Perubatan Keluarga',
            'clinic_en' => 'Family Medicine Clinic',
            'clinic_bm' => 'Klinik Perubatan Keluarga',
        ],
        'pediatrik' => [
            'en'        => 'Department of Paediatrics',
            'bm'        => 'Jabatan Pediatrik',
            'clinic_en' => 'Paediatrics',
            'clinic_bm' => 'Pediatrik',
        ],
        'klinik pediatrik' => [
            'en'        => 'Paediatric Clinic',
            'bm'        => 'Klinik Pediatrik',
            'clinic_en' => 'Paediatrics',
            'clinic_bm' => 'Pediatrik',
        ],
        'oftalmologi' => [
            'en'        => 'Department of Ophthalmology',
            'bm'        => 'Jabatan Oftalmologi',
            'clinic_en' => 'Ophthalmology',
            'clinic_bm' => 'Oftalmologi',
        ],
        'klinik mata' => [
            'en'        => 'Eye Clinic',
            'bm'        => 'Klinik Mata',
            'clinic_en' => 'Ophthalmology',
            'clinic_bm' => 'Mata',
        ],
        'otorinolaringologi' => [
            'en'        => 'Department of Otorhinolaryngology (ENT)',
            'bm'        => 'Jabatan Otorinolaringologi (ENT)',
            'clinic_en' => 'ENT',
            'clinic_bm' => 'ENT',
        ],
        'klinik ent' => [
            'en'        => 'ENT Clinic',
            'bm'        => 'Klinik ENT',
            'clinic_en' => 'ENT',
            'clinic_bm' => 'ENT',
        ],
        'obstetrik & ginekologi' => [
            'en'        => 'Department of Obstetrics & Gynaecology (O&G)',
            'bm'        => 'Jabatan Obstetrik & Ginekologi (O&G)',
            'clinic_en' => 'Obstetrics & Gynaecology',
            'clinic_bm' => 'Obstetrik & Ginekologi',
        ],
        'klinik o&g' => [
            'en'        => 'O&G Clinic',
            'bm'        => 'Klinik O&G',
            'clinic_en' => 'Obstetrics & Gynaecology',
            'clinic_bm' => 'Obstetrik & Ginekologi',
        ],
        'psikiatri' => [
            'en'        => 'Department of Psychiatry',
            'bm'        => 'Jabatan Psikiatri',
            'clinic_en' => 'Psychiatry',
            'clinic_bm' => 'Psikiatri',
        ],
        'klinik psikiatri' => [
            'en'        => 'Psychiatry Clinic',
            'bm'        => 'Klinik Psikiatri',
            'clinic_en' => 'Psychiatry',
            'clinic_bm' => 'Psikiatri',
        ],
        'dermatologi' => [
            'en'        => 'Department of Dermatology',
            'bm'        => 'Jabatan Dermatologi',
            'clinic_en' => 'Dermatology',
            'clinic_bm' => 'Dermatologi',
        ],
        'kardiologi' => [
            'en'        => 'Department of Cardiology',
            'bm'        => 'Jabatan Kardiologi',
            'clinic_en' => 'Cardiology',
            'clinic_bm' => 'Kardiologi',
        ],
        'nefrologi' => [
            'en'        => 'Department of Nephrology',
            'bm'        => 'Jabatan Nefrologi',
            'clinic_en' => 'Nephrology',
            'clinic_bm' => 'Nefrologi',
        ],
        'neurologi' => [
            'en'        => 'Department of Neurology',
            'bm'        => 'Jabatan Neurologi',
            'clinic_en' => 'Neurology',
            'clinic_bm' => 'Neurologi',
        ],
        'radiologi' => [
            'en'        => 'Department of Radiology',
            'bm'        => 'Jabatan Radiologi',
            'clinic_en' => 'Radiology',
            'clinic_bm' => 'Radiologi',
        ],
        'anestesiologi' => [
            'en'        => 'Department of Anaesthesiology',
            'bm'        => 'Jabatan Anestesiologi',
            'clinic_en' => 'Anaesthesiology',
            'clinic_bm' => 'Anestesiologi',
        ],
        'pergigian' => [
            'en'        => 'Dental Clinic',
            'bm'        => 'Klinik Pergigian',
            'clinic_en' => 'Dental',
            'clinic_bm' => 'Pergigian',
        ],
        'rehabilitasi' => [
            'en'        => 'Rehabilitation Medicine Clinic',
            'bm'        => 'Klinik Rehabilitasi Perubatan',
            'clinic_en' => 'Rehabilitation',
            'clinic_bm' => 'Rehabilitasi',
        ],
        'fisioterapi' => [
            'en'        => 'Physiotherapy Unit',
            'bm'        => 'Unit Fisioterapi',
            'clinic_en' => 'Physiotherapy',
            'clinic_bm' => 'Fisioterapi',
        ],
        'wad perubatan lelaki' => [
            'en'        => 'Male Medical Ward',
            'bm'        => 'Wad Perubatan Lelaki',
            'clinic_en' => 'Male Medical Ward',
            'clinic_bm' => 'Wad Perubatan Lelaki',
        ],
        'wad perubatan perempuan' => [
            'en'        => 'Female Medical Ward',
            'bm'        => 'Wad Perubatan Perempuan',
            'clinic_en' => 'Female Medical Ward',
            'clinic_bm' => 'Wad Perubatan Perempuan',
        ],
        'wad surgeri' => [
            'en'        => 'Surgical Ward',
            'bm'        => 'Wad Surgeri',
            'clinic_en' => 'Surgical Ward',
            'clinic_bm' => 'Wad Surgeri',
        ],
        'wad kanak-kanak' => [
            'en'        => 'Paediatric Ward',
            'bm'        => 'Wad Kanak-Kanak',
            'clinic_en' => 'Paediatric Ward',
            'clinic_bm' => 'Wad Kanak-Kanak',
        ],
        'wad bersalin' => [
            'en'        => 'Maternity Ward',
            'bm'        => 'Wad Bersalin',
            'clinic_en' => 'Maternity Ward',
            'clinic_bm' => 'Wad Bersalin',
        ],
        'unit rawatan rapi' => [
            'en'        => 'Intensive Care Unit (ICU)',
            'bm'        => 'Unit Rawatan Rapi (ICU)',
            'clinic_en' => 'Intensive Care Unit (ICU)',
            'clinic_bm' => 'Unit Rawatan Rapi (ICU)',
        ],
    ];

    /**
     * Relationship vocabulary mapping
     */
    protected static array $relationDictionary = [
        'bapa'      => ['en' => 'father',   'bm' => 'bapanya'],
        'ayah'      => ['en' => 'father',   'bm' => 'bapanya'],
        'father'    => ['en' => 'father',   'bm' => 'bapanya'],
        'ibu'       => ['en' => 'mother',   'bm' => 'ibunya'],
        'emak'      => ['en' => 'mother',   'bm' => 'ibunya'],
        'mother'    => ['en' => 'mother',   'bm' => 'ibunya'],
        'anak'      => ['en' => 'child',    'bm' => 'anaknya'],
        'child'     => ['en' => 'child',    'bm' => 'anaknya'],
        'suami'     => ['en' => 'husband',  'bm' => 'suaminya'],
        'husband'   => ['en' => 'husband',  'bm' => 'suaminya'],
        'isteri'    => ['en' => 'wife',     'bm' => 'isterinya'],
        'wife'      => ['en' => 'wife',     'bm' => 'isterinya'],
        'penjaga'   => ['en' => 'guardian', 'bm' => 'penjaganya'],
        'guardian'  => ['en' => 'guardian', 'bm' => 'penjaganya'],
        'abang'     => ['en' => 'brother',  'bm' => 'abangnya'],
        'kakak'     => ['en' => 'sister',   'bm' => 'kakaknya'],
        'adik'      => ['en' => 'sibling',  'bm' => 'adiknya'],
        'saudara'   => ['en' => 'relative', 'bm' => 'saudaranya'],
        'rakan'     => ['en' => 'friend',   'bm' => 'rakannya'],
        'jiran'     => ['en' => 'neighbour','bm' => 'jirannya'],
        'majikan'   => ['en' => 'employer', 'bm' => 'majikannya'],
        'pekerja'   => ['en' => 'employee', 'bm' => 'pekerjanya'],
    ];

    /**
     * Purpose / Reason for visit dictionary
     */
    protected static array $purposeDictionary = [
        'mendapatkan rawatan'                                => ['en' => 'Medical treatment',                    'bm' => 'Mendapatkan rawatan'],
        'rawatan perubatan'                                  => ['en' => 'Medical treatment',                    'bm' => 'Rawatan perubatan'],
        'rawatan'                                            => ['en' => 'Medical treatment',                    'bm' => 'Rawatan perubatan'],
        'pemeriksaan perubatan'                              => ['en' => 'Medical examination',                  'bm' => 'Pemeriksaan perubatan'],
        'pemeriksaan'                                        => ['en' => 'Medical examination',                  'bm' => 'Pemeriksaan perubatan'],
        'rawatan susulan'                                    => ['en' => 'Follow-up treatment',                  'bm' => 'Rawatan susulan'],
        'tca'                                                => ['en' => 'Follow-up appointment',                'bm' => 'Temujanji rawatan susulan'],
        'tca rawatan'                                        => ['en' => 'Follow-up treatment',                  'bm' => 'Rawatan susulan'],
        'tca ct scan'                                        => ['en' => 'Follow-up CT SCAN',                    'bm' => 'Pemeriksaan susulan CT SCAN'],
        'rawatan tca'                                        => ['en' => 'Follow-up treatment',                  'bm' => 'Rawatan susulan'],
        'mendapatkan rawatan dan pemeriksaan perubatan'      => ['en' => 'Medical treatment and examination',    'bm' => 'Mendapatkan rawatan dan pemeriksaan perubatan'],
        'demam'                                              => ['en' => 'Fever treatment',                      'bm' => 'Rawatan demam'],
        'sakit kepala'                                       => ['en' => 'Headache treatment',                   'bm' => 'Rawatan sakit kepala'],
        'sakit perut'                                        => ['en' => 'Abdominal pain treatment',             'bm' => 'Rawatan sakit perut'],
        'sakit mata'                                         => ['en' => 'Eye treatment',                        'bm' => 'Rawatan sakit mata'],
        'gastrik'                                            => ['en' => 'Gastritis treatment',                  'bm' => 'Rawatan gastrik'],
        'batuk'                                              => ['en' => 'Cough treatment',                      'bm' => 'Rawatan batuk'],
        'selsema'                                            => ['en' => 'Flu/Cold treatment',                   'bm' => 'Rawatan selsema'],
        'cirit-birit'                                        => ['en' => 'Diarrhoea treatment',                  'bm' => 'Rawatan cirit-birit'],
        'mendapatkan rawatan kesihatan untuk bapa'           => ['en' => 'Medical treatment for father',         'bm' => 'Mendapatkan rawatan kesihatan untuk bapa'],
        'mendapatkan rawatan kesihatan'                      => ['en' => 'Medical treatment',                    'bm' => 'Mendapatkan rawatan kesihatan'],
        'mendapat rawatan kesihatan'                         => ['en' => 'Medical treatment',                    'bm' => 'Mendapatkan rawatan kesihatan'],
        'rawatan tca abang'                                  => ['en' => 'Follow-up treatment for brother',      'bm' => 'Rawatan susulan abang'],
        'tca abang'                                          => ['en' => 'Follow-up treatment for brother',      'bm' => 'Rawatan susulan abang'],
        'medical treament'                                   => ['en' => 'Medical treatment',                    'bm' => 'Mendapatkan rawatan'],
        'medical treatment'                                  => ['en' => 'Medical treatment',                    'bm' => 'Mendapatkan rawatan'],
        'kecemasan'                                          => ['en' => 'Emergency treatment',                  'bm' => 'Rawatan kecemasan'],
    ];

    /**
     * Common MC Diagnosis dictionary
     */
    protected static array $diagnosisDictionary = [
        'demam'            => ['en' => 'Fever',                    'bm' => 'Demam'],
        'sakit kepala'     => ['en' => 'Headache',                 'bm' => 'Sakit kepala'],
        'sakit perut'      => ['en' => 'Abdominal pain',           'bm' => 'Sakit perut'],
        'sakit mata'       => ['en' => 'Eye pain / Conjunctivitis','bm' => 'Sakit mata'],
        'gastrik'          => ['en' => 'Gastritis',                'bm' => 'Gastrik'],
        'batuk'            => ['en' => 'Cough',                    'bm' => 'Batuk'],
        'selsema'          => ['en' => 'Upper Respiratory Tract Infection (URTI)', 'bm' => 'Selsema / Jangkitan Saluran Pernafasan'],
        'cirit-birit'      => ['en' => 'Acute Gastroenteritis (AGE) / Diarrhoea',  'bm' => 'Cirit-birit (Gastroenteritis Akut)'],
        'kecederaan ringan'=> ['en' => 'Minor soft tissue injury', 'bm' => 'Kecederaan tisu lembut ringan'],
        'covid'            => ['en' => 'COVID-19 Infection',       'bm' => 'Jangkitan COVID-19'],
    ];

    /**
     * Normalize key for dictionary lookup
     */
    protected static function normalizeKey(string $str): string
    {
        $normalized = trim(strtolower($str));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return $normalized;
    }

    /**
     * Translate full department name (used in doctor credentials, letterheads, audit)
     *
     * @param string $department Canonical department name from DB
     * @param string $lang 'BI' or 'BM'
     * @return string
     */
    public static function translateDepartment(string $department, string $lang = 'BI'): string
    {
        $dept = trim($department);
        if ($dept === '') {
            return ($lang === 'BM') ? 'Perubatan Am' : 'General Medicine';
        }

        $key = self::normalizeKey($dept);

        if (isset(self::$departmentDictionary[$key])) {
            return ($lang === 'BM')
                ? self::$departmentDictionary[$key]['bm']
                : self::$departmentDictionary[$key]['en'];
        }

        // Substring matching if exact match not found
        foreach (self::$departmentDictionary as $entryKey => $trans) {
            if (str_contains($key, $entryKey) || str_contains($entryKey, $key)) {
                return ($lang === 'BM') ? $trans['bm'] : $trans['en'];
            }
        }

        // If not found in dictionary, return original if BM; format cleanly if BI
        if ($lang === 'BM') {
            return $dept;
        }

        // Simple cleanup for English fallback
        $enClean = preg_replace('/^Jabatan\s+/i', '', $dept);
        $enClean = preg_replace('/^Klinik\s+/i', '', $enClean);
        return $enClean . ' Department';
    }

    /**
     * Translate clinic name specifically formatted for attendance sentences
     * e.g., "at the [Clinic] clinic" in English, or "di klinik [Clinic]" in Malay.
     *
     * @param string $department
     * @param string $lang 'BI' or 'BM'
     * @return string
     */
    public static function translateClinic(string $department, string $lang = 'BI'): string
    {
        $dept = trim($department);
        if ($dept === '') {
            return ($lang === 'BM') ? 'Perubatan Am' : 'General Medicine';
        }

        $key = self::normalizeKey($dept);

        if (isset(self::$departmentDictionary[$key])) {
            return ($lang === 'BM')
                ? self::$departmentDictionary[$key]['clinic_bm']
                : self::$departmentDictionary[$key]['clinic_en'];
        }

        // Substring matching
        foreach (self::$departmentDictionary as $entryKey => $trans) {
            if (str_contains($key, $entryKey) || str_contains($entryKey, $key)) {
                return ($lang === 'BM') ? $trans['clinic_bm'] : $trans['clinic_en'];
            }
        }

        // Fallback: strip "Jabatan " and "Klinik " for clean sentence rendering
        $cleaned = preg_replace('/^(Jabatan|Klinik)\s+/i', '', $dept);
        return !empty($cleaned) ? $cleaned : $dept;
    }

    /**
     * Translate relationship / attendee phrase contextually
     *
     * @param string $visitType 'diri_sendiri', 'anak', 'suami_isteri', 'ibu_bapa', 'lain'
     * @param string|null $companionRelation custom relation if visitType is 'lain'
     * @param string $attendantGender 'male' or 'female'
     * @param bool $patientIsMale
     * @param string|null $patientName
     * @param string $lang 'BI' or 'BM'
     * @return string
     */
    public static function translateRelationship(
        string $visitType,
        ?string $companionRelation = null,
        string $attendantGender = 'female',
        bool $patientIsMale = false,
        ?string $patientName = null,
        string $lang = 'BI'
    ): string {
        $isBM    = (strtoupper($lang) === 'BM');
        $pronoun = ($attendantGender === 'male') ? 'his' : 'her';

        // 1. Self Treatment
        if ($visitType === 'diri_sendiri' || empty($visitType)) {
            if ($isBM) {
                return 'dirinya sendiri';
            }
            return ($attendantGender === 'male') ? 'himself' : 'herself';
        }

        // 2. Accompanying Child
        if ($visitType === 'anak') {
            if ($isBM) {
                return !empty($patientName) ? 'anaknya (' . trim($patientName) . ')' : 'anaknya';
            }
            return !empty($patientName) ? $pronoun . ' child (' . trim($patientName) . ')' : $pronoun . ' child';
        }

        // 3. Accompanying Spouse
        if ($visitType === 'suami_isteri') {
            if ($isBM) {
                $spBM = ($attendantGender === 'male') ? 'isterinya' : 'suaminya';
                return !empty($patientName) ? $spBM . ' (' . trim($patientName) . ')' : $spBM;
            }
            $spEN = ($attendantGender === 'male') ? 'his wife' : 'her husband';
            return !empty($patientName) ? $spEN . ' (' . trim($patientName) . ')' : $spEN;
        }

        // 4. Accompanying Parent
        if ($visitType === 'ibu_bapa') {
            if ($isBM) {
                $parBM = $patientIsMale ? 'bapanya' : 'ibunya';
                return !empty($patientName) ? $parBM . ' (' . trim($patientName) . ')' : $parBM;
            }
            $parEN = $pronoun . ' ' . ($patientIsMale ? 'father' : 'mother');
            return !empty($patientName) ? $parEN . ' (' . trim($patientName) . ')' : $parEN;
        }

        // 5. Custom or Other Relationship
        $custom = trim($companionRelation ?? '');
        $customKey = self::normalizeKey($custom);

        if (!empty($customKey) && isset(self::$relationDictionary[$customKey])) {
            $entry = self::$relationDictionary[$customKey];
            if ($isBM) {
                return !empty($patientName) ? $entry['bm'] . ' (' . trim($patientName) . ')' : $entry['bm'];
            }
            return !empty($patientName) ? $pronoun . ' ' . $entry['en'] . ' (' . trim($patientName) . ')' : $pronoun . ' ' . $entry['en'];
        }

        // Generic other fallback
        if ($isBM) {
            $otherBM = !empty($custom) ? $custom : 'penjaganya';
            return !empty($patientName) ? $otherBM . ' (' . trim($patientName) . ')' : $otherBM;
        }

        $otherEN = !empty($custom) ? $custom : 'his/her guardian';
        return !empty($patientName) ? $otherEN . ' (' . trim($patientName) . ')' : $otherEN;
    }

    /**
     * Translate treatment purpose / reason for visit
     *
     * @param string|null $purpose
     * @param string $lang 'BI' or 'BM'
     * @return string
     */
    public static function translatePurpose(?string $purpose, string $lang = 'BI'): string
    {
        $text = trim($purpose ?? '');
        if ($text === '') {
            return ($lang === 'BM') ? 'Mendapatkan rawatan' : 'Medical treatment';
        }

        $key = self::normalizeKey($text);

        if (isset(self::$purposeDictionary[$key])) {
            return ($lang === 'BM')
                ? self::$purposeDictionary[$key]['bm']
                : self::$purposeDictionary[$key]['en'];
        }

        // Substring / prefix matching
        foreach (self::$purposeDictionary as $entryKey => $trans) {
            if (str_starts_with($key, $entryKey) || str_contains($key, $entryKey)) {
                return ($lang === 'BM') ? $trans['bm'] : $trans['en'];
            }
        }

        // Return original if not recognized
        return $text;
    }

    /**
     * Translate medical diagnosis (for Medical Certificate)
     *
     * @param string|null $diagnosis
     * @param string $lang 'BI' or 'BM'
     * @return string
     */
    public static function translateDiagnosis(?string $diagnosis, string $lang = 'BI'): string
    {
        $text = trim($diagnosis ?? '');
        if ($text === '') {
            return '';
        }

        $key = self::normalizeKey($text);

        if (isset(self::$diagnosisDictionary[$key])) {
            return ($lang === 'BM')
                ? self::$diagnosisDictionary[$key]['bm']
                : self::$diagnosisDictionary[$key]['en'];
        }

        foreach (self::$diagnosisDictionary as $entryKey => $trans) {
            if (str_contains($key, $entryKey)) {
                return ($lang === 'BM') ? $trans['bm'] : $trans['en'];
            }
        }

        return $text;
    }

    /**
     * Get all active translations for client-side JavaScript synchronization
     *
     * @return array
     */
    public static function getClientDictionary(): array
    {
        return [
            'departments' => self::$departmentDictionary,
            'purposes'    => self::$purposeDictionary,
            'relations'   => self::$relationDictionary,
        ];
    }
}
