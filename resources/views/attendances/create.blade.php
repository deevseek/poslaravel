<x-app-layout title="Catat Absensi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Catat Absensi</h1>
            <p class="text-gray-600">Rekam kehadiran pegawai dengan menghadap kamera/webcam untuk proses face recognition.</p>
        </div>
        <a href="{{ route('attendances.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form action="{{ route('attendances.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="employee_id">Karyawan</label>
                    <select id="employee_id" name="employee_id" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="">Pilih karyawan</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} {{ $employee->position ? ' - ' . $employee->position : '' }} {{ $employee->face_recognition_registered_at ? '' : '(Wajah belum terdaftar)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-3 rounded-lg border border-dashed border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-700">
                        <p class="font-semibold">Hasil scan wajah</p>
                        <p id="detected-employee-name" class="text-blue-900">Belum ada karyawan terdeteksi.</p>
                        <p id="detected-employee-status" class="text-xs text-blue-600">Sistem akan menampilkan nama karyawan dan menyimpan absensi otomatis setelah wajah dikenali.</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="attendance_date">Tanggal</label>
                    <input type="date" id="attendance_date" name="attendance_date" value="{{ old('attendance_date', now()->toDateString()) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('attendance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="check_in_time">Jam Check-in</label>
                    <input type="time" id="check_in_time" name="check_in_time" value="{{ old('check_in_time', now()->format('H:i')) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('check_in_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="check_out_time">Jam Check-out (opsional)</label>
                    <input type="time" id="check_out_time" name="check_out_time" value="{{ old('check_out_time') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('check_out_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Metode Absensi</label>
                <div class="mt-1 flex items-center gap-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    <span class="text-lg">🙂</span>
                    <div>
                        <p class="font-semibold">Face Recognition via Webcam</p>
                        <p class="text-xs text-blue-600">Pegawai menghadap kamera/webcam, lalu sistem memverifikasi identitas melalui pengenalan wajah.</p>
                        <p class="text-xs text-blue-600">Pastikan wajah karyawan sudah terdaftar pada profil karyawan sebelum melakukan absensi.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                        <video id="face-recognition-webcam" class="h-56 w-full object-cover" autoplay playsinline muted></video>
                    </div>
                    <p id="face-recognition-webcam-status" class="mt-2 text-xs text-gray-500">
                        Izinkan akses kamera agar pratinjau webcam tampil.
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700" for="face_recognition_code">Kode Face Recognition</label>
                <input type="text" id="face_recognition_code" name="face_recognition_code" value="{{ old('face_recognition_code') }}" required
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                <p class="mt-1 text-xs text-gray-500">Kode ini harus sesuai dengan data wajah yang sudah didaftarkan agar absensi tidak bisa dipalsukan.</p>
                @error('face_recognition_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700" for="note">Catatan (opsional)</label>
                <textarea id="note" name="note" rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('attendances.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Absensi</button>
        </div>
    </form>

    <script>
        const videoElement = document.getElementById('face-recognition-webcam');
        const statusElement = document.getElementById('face-recognition-webcam-status');
        const employeeSelect = document.getElementById('employee_id');
        const faceCodeInput = document.getElementById('face_recognition_code');
        const detectedNameElement = document.getElementById('detected-employee-name');
        const detectedStatusElement = document.getElementById('detected-employee-status');
        const attendanceForm = document.querySelector('form');
        const csrfToken = document.querySelector('input[name="_token"]').value;
        let autoSubmitInProgress = false;
        let identifyTimeout = null;

        if (navigator.mediaDevices?.getUserMedia) {
            navigator.mediaDevices
                .getUserMedia({ video: { facingMode: 'user' } })
                .then((stream) => {
                    videoElement.srcObject = stream;
                    return videoElement.play();
                })
                .then(() => {
                    statusElement.textContent = 'Arahkan wajah ke kamera untuk memulai pemindaian.';
                })
                .catch(() => {
                    statusElement.textContent = 'Tidak dapat mengakses kamera. Pastikan izin kamera sudah diaktifkan.';
                });
        } else {
            statusElement.textContent = 'Perangkat ini tidak mendukung akses kamera melalui browser.';
        }

        const resetDetectedEmployee = () => {
            detectedNameElement.textContent = 'Belum ada karyawan terdeteksi.';
            detectedStatusElement.textContent = 'Sistem akan menampilkan nama karyawan dan menyimpan absensi otomatis setelah wajah dikenali.';
        };

        const handleIdentification = async () => {
            const faceCode = faceCodeInput.value.trim();
            if (faceCode.length < 3 || autoSubmitInProgress) {
                return;
            }

            detectedStatusElement.textContent = 'Memverifikasi wajah...';

            try {
                const response = await fetch('{{ route('attendances.identify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        face_recognition_code: faceCode,
                        attendance_date: document.getElementById('attendance_date').value,
                    }),
                });

                if (!response.ok) {
                    resetDetectedEmployee();
                    detectedStatusElement.textContent = 'Wajah belum terdaftar atau tidak dikenali.';
                    return;
                }

                const data = await response.json();
                const employeeLabel = `${data.employee.name}${data.employee.position ? ' - ' + data.employee.position : ''}`;

                employeeSelect.value = data.employee.id;
                detectedNameElement.textContent = employeeLabel;

                if (data.already_attended) {
                    detectedStatusElement.textContent = 'Absensi untuk karyawan ini sudah tercatat hari ini.';
                    return;
                }

                detectedStatusElement.textContent = 'Wajah terdeteksi. Absensi akan disimpan otomatis...';
                autoSubmitInProgress = true;
                attendanceForm.submit();
            } catch (error) {
                resetDetectedEmployee();
                detectedStatusElement.textContent = 'Gagal memverifikasi wajah. Silakan coba lagi.';
            }
        };

        faceCodeInput.addEventListener('input', () => {
            resetDetectedEmployee();
            clearTimeout(identifyTimeout);
            identifyTimeout = setTimeout(handleIdentification, 600);
        });
    </script>
</x-app-layout>
