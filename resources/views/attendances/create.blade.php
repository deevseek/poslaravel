<x-app-layout title="Catat Absensi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Catat Absensi</h1>
            <p class="text-gray-600">Rekam kehadiran pegawai dengan menghadap kamera/webcam untuk proses face recognition.</p>
        </div>
        <a href="{{ route('attendances.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form id="attendance-form" action="{{ route('attendances.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Jenis Absensi</label>
                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-700">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="attendance_type" value="checkin" class="text-blue-600"
                            {{ old('attendance_type', 'checkin') === 'checkin' ? 'checked' : '' }}>
                        Check-in
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="attendance_type" value="checkout" class="text-blue-600"
                            {{ old('attendance_type') === 'checkout' ? 'checked' : '' }}>
                        Check-out
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">Pilih check-out jika ingin mencatat jam pulang dari absensi yang sudah ada.</p>
                @error('attendance_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
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
                    <input type="time" id="check_in_time" name="check_in_time" value="{{ old('check_in_time', $workStart ?? now()->format('H:i')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('check_in_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Jam masuk standar: {{ $workStart ?? '09:00' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="check_out_time">Jam Check-out (opsional)</label>
                    <input type="time" id="check_out_time" name="check_out_time" value="{{ old('check_out_time', $workEnd ?? '') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('check_out_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Jam pulang standar: {{ $workEnd ?? '17:00' }}</p>
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
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- KIRI: KAMERA -->
                <div class="flex justify-center">
                    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-black w-[260px] h-[180px]">
                        <video
                            id="face-recognition-webcam"
                            class="h-full w-full object-cover"
                            autoplay
                            playsinline
                            muted>
                        </video>
                    </div>
                </div>

                <!-- KANAN: STATUS & KONTROL -->
                <div class="space-y-3">
                    <p id="face-recognition-webcam-status" class="text-xs text-gray-500">
                        Izinkan akses kamera agar pratinjau webcam tampil.
                    </p>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            id="face-recognition-capture"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                            Scan Wajah
                        </button>
                        <span class="text-xs text-gray-500">Klik untuk mengambil foto terbaru</span>
                    </div>

                    <!-- WAJIB ADA: snapshot hidden input -->
                    <input type="hidden" id="face_recognition_snapshot" name="face_recognition_snapshot"
                        value="{{ old('face_recognition_snapshot') }}">

                    <!-- WAJIB ADA: canvas hidden -->
                    <canvas id="face-recognition-canvas" class="hidden"></canvas>

                    <div>
                        <img
                            id="face-recognition-preview"
                            src="{{ old('face_recognition_snapshot') }}"
                            alt="Pratinjau scan wajah"
                            class="{{ old('face_recognition_snapshot') ? '' : 'hidden' }} h-24 w-24 rounded-lg border border-gray-200 object-cover">
                    </div>

                    @error('face_recognition_snapshot')
                        <p class="text-sm text-red-600">{{ $message }}</p>
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
        const captureButton = document.getElementById('face-recognition-capture');
        const snapshotInput = document.getElementById('face_recognition_snapshot');
        const previewImage = document.getElementById('face-recognition-preview');
        const canvasElement = document.getElementById('face-recognition-canvas');
        const detectedNameElement = document.getElementById('detected-employee-name');
        const detectedStatusElement = document.getElementById('detected-employee-status');
        const attendanceForm = document.getElementById('attendance-form');
        const attendanceTypeInputs = document.querySelectorAll('input[name="attendance_type"]');
        const checkInInput = document.getElementById('check_in_time');
        const checkOutInput = document.getElementById('check_out_time');
        const csrfToken = attendanceForm.querySelector('input[name="_token"]').value;
        let autoSubmitInProgress = false;
        let autoScanInterval = null;
        let autoScanInFlight = false;

        const getAttendanceType = () => {
            return document.querySelector('input[name="attendance_type"]:checked')?.value || 'checkin';
        };

        const updateAttendanceTypeState = () => {
            const attendanceType = getAttendanceType();
            if (attendanceType === 'checkout') {
                checkInInput.required = false;
                checkInInput.disabled = true;
                checkInInput.classList.add('bg-gray-100');
                checkOutInput.required = true;
            } else {
                checkInInput.disabled = false;
                checkInInput.required = true;
                checkInInput.classList.remove('bg-gray-100');
                checkOutInput.required = false;
            }
        };

        updateAttendanceTypeState();

        if (navigator.mediaDevices?.getUserMedia) {
            navigator.mediaDevices
                .getUserMedia({ video: { facingMode: 'user' } })
                .then((stream) => {
                    videoElement.srcObject = stream;
                    return videoElement.play();
                })
                .then(() => {
                    statusElement.textContent = 'Arahkan wajah ke kamera. Sistem akan memindai otomatis.';
                    startAutoScan();
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

        const captureSnapshot = async () => {
            if (!videoElement.srcObject) {
                statusElement.textContent = 'Kamera belum siap. Silakan izinkan akses kamera terlebih dahulu.';
                return null;
            }

            if (!videoElement.videoWidth || !videoElement.videoHeight) {
                statusElement.textContent = 'Kamera belum siap. Tunggu sebentar, lalu coba lagi.';
                return null;
            }

            await new Promise((resolve) => setTimeout(resolve, 200));

            const width = videoElement.videoWidth;
            const height = videoElement.videoHeight;
            canvasElement.width = width;
            canvasElement.height = height;
            const context = canvasElement.getContext('2d');
            context.imageSmoothingEnabled = false;
            context.drawImage(videoElement, 0, 0, width, height);
            const snapshot = canvasElement.toDataURL('image/jpeg', 0.9);
            snapshotInput.value = snapshot;
            previewImage.src = snapshot;
            previewImage.classList.remove('hidden');
            return snapshot;
        };

        const handleIdentification = async (snapshot) => {
            if (!snapshot || autoSubmitInProgress || autoScanInFlight) {
                return;
            }

            autoScanInFlight = true;
            detectedStatusElement.textContent = 'Memverifikasi wajah...';

            try {
                const response = await fetch('{{ route('attendances.identify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        face_recognition_snapshot: snapshot,
                        attendance_date: document.getElementById('attendance_date').value,
                    }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    resetDetectedEmployee();
                    switch (data.error) {
                        case 'no_face_detected':
                            detectedStatusElement.textContent = 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup, lalu coba lagi.';
                            break;
                        case 'multiple_faces_detected':
                            detectedStatusElement.textContent = 'Terdapat lebih dari satu wajah pada foto. Silakan ulangi.';
                            break;
                        case 'face_not_matched':
                            detectedStatusElement.textContent = 'Wajah terdeteksi tetapi tidak cocok dengan data karyawan.';
                            break;
                        case 'service_unavailable':
                            detectedStatusElement.textContent = 'Layanan face recognition sedang tidak tersedia. Coba lagi nanti.';
                            break;
                        default:
                            detectedStatusElement.textContent = data.message || 'Wajah belum terdaftar atau tidak dikenali.';
                            break;
                    }
                    return;
                }

                const employeeLabel = `${data.employee.name}${data.employee.position ? ' - ' + data.employee.position : ''}`;

                employeeSelect.value = data.employee.id;
                detectedNameElement.textContent = employeeLabel;

                const attendanceType = getAttendanceType();
                if (attendanceType === 'checkin' && data.already_attended) {
                    detectedStatusElement.textContent = 'Absensi untuk karyawan ini sudah tercatat hari ini.';
                    stopAutoScan();
                    return;
                }

                if (attendanceType === 'checkout') {
                    if (!data.already_attended) {
                        detectedStatusElement.textContent = 'Belum ada absensi check-in untuk karyawan ini hari ini.';
                        return;
                    }

                    if (data.has_checked_out) {
                        detectedStatusElement.textContent = 'Checkout untuk karyawan ini sudah tercatat hari ini.';
                        stopAutoScan();
                        return;
                    }
                }

                detectedStatusElement.textContent = 'Wajah terdeteksi. Absensi akan disimpan otomatis...';
                autoSubmitInProgress = true;
                attendanceForm.submit();
            } catch (error) {
                resetDetectedEmployee();
                detectedStatusElement.textContent = 'Gagal memverifikasi wajah. Silakan coba lagi.';
            } finally {
                autoScanInFlight = false;
            }
        };

        attendanceForm.addEventListener('submit', (event) => {
            if (!snapshotInput.value) {
                event.preventDefault();
                detectedStatusElement.textContent = 'Silakan lakukan scan wajah terlebih dahulu sebelum menyimpan absensi.';
            }
        });

        const startAutoScan = () => {
            if (autoScanInterval) {
                return;
            }

            autoScanInterval = setInterval(async () => {
                if (autoSubmitInProgress || autoScanInFlight) {
                    return;
                }

                const snapshot = await captureSnapshot();
                await handleIdentification(snapshot);
            }, 4000);
        };

        const stopAutoScan = () => {
            if (autoScanInterval) {
                clearInterval(autoScanInterval);
                autoScanInterval = null;
            }
        };

        captureButton.addEventListener('click', async () => {
            resetDetectedEmployee();
            const snapshot = await captureSnapshot();
            await handleIdentification(snapshot);
        });

        attendanceTypeInputs.forEach((input) => {
            input.addEventListener('change', () => {
                updateAttendanceTypeState();
                resetDetectedEmployee();
            });
        });
    </script>
</x-app-layout>
