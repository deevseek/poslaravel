<x-app-layout title="Edit Karyawan">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Karyawan</h1>
            <p class="text-gray-600">Perbarui data karyawan yang sudah ada.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="position">Posisi</label>
                    <input type="text" id="position" name="position" value="{{ old('position', $employee->position) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700" for="address">Alamat</label>
                <textarea id="address" name="address" rows="2"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('address', $employee->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="join_date">Tanggal Bergabung</label>
                    <input type="date" id="join_date" name="join_date" value="{{ old('join_date', optional($employee->join_date)->toDateString()) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('join_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="base_salary">Gaji Pokok</label>
                    <input type="number" id="base_salary" name="base_salary" min="0" step="0.01" value="{{ old('base_salary', $employee->base_salary) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('base_salary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                    <label class="text-sm font-semibold text-gray-700" for="is_active">Aktif</label>
                </div>
            </div>

            <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-blue-700">Pendaftaran Face Recognition</p>
                    @if ($employee->face_recognition_registered_at)
                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Terdaftar {{ $employee->face_recognition_registered_at->format('d M Y H:i') }}</span>
                    @else
                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">Belum terdaftar</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-blue-600">Perbarui pemindaian ulang melalui kamera. Centang reset untuk menghapus data wajah lama.</p>
                <p class="mt-2 text-xs text-blue-600">Modul laravel-face-auth memastikan wajah terlihat jelas sebelum data disimpan.</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div class="space-y-3 md:col-span-2">
                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                            <video id="face-recognition-webcam" class="h-56 w-full object-cover" autoplay playsinline muted></video>
                        </div>
                        <p id="face-recognition-webcam-status" class="text-xs text-gray-500">Izinkan akses kamera untuk memulai pemindaian wajah.</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" id="face-recognition-capture"
                                class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-blue-700">
                                Rekam Wajah
                            </button>
                            <span class="text-xs text-gray-500">Rekam ulang jika data wajah berubah.</span>
                        </div>
                        <input type="hidden" id="face_recognition_snapshot" name="face_recognition_snapshot" value="{{ old('face_recognition_snapshot') }}">
                        <canvas id="face-recognition-canvas" class="hidden"></canvas>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold text-gray-700">Scan Terbaru</p>
                                <img id="face-recognition-preview" src="{{ old('face_recognition_snapshot') }}" alt="Pratinjau scan wajah"
                                    class="mt-2 {{ old('face_recognition_snapshot') ? '' : 'hidden' }} h-24 w-24 rounded-lg border border-gray-200 object-cover">
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-700">Scan Saat Ini</p>
                                @if ($employee->face_recognition_scan_path)
                                    <img src="{{ Storage::url($employee->face_recognition_scan_path) }}" alt="Scan wajah {{ $employee->name }}"
                                        class="mt-2 h-24 w-24 rounded-lg border border-gray-200 object-cover">
                                @else
                                    <p class="mt-2 text-xs text-gray-500">Belum ada scan wajah tersimpan.</p>
                                @endif
                            </div>
                        </div>
                        @error('face_recognition_snapshot')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <input type="checkbox" id="reset_face_recognition" name="reset_face_recognition" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600" {{ old('reset_face_recognition') ? 'checked' : '' }}>
                        <label class="text-sm font-semibold text-gray-700" for="reset_face_recognition">Reset data wajah</label>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <input type="checkbox" id="remove_face_recognition_scan" name="remove_face_recognition_scan" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600" {{ old('remove_face_recognition_scan') ? 'checked' : '' }}>
                        <label class="text-sm font-semibold text-gray-700" for="remove_face_recognition_scan">Hapus scan wajah tersimpan</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('employees.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>

    <script>
        const videoElement = document.getElementById('face-recognition-webcam');
        const statusElement = document.getElementById('face-recognition-webcam-status');
        const captureButton = document.getElementById('face-recognition-capture');
        const snapshotInput = document.getElementById('face_recognition_snapshot');
        const previewImage = document.getElementById('face-recognition-preview');
        const canvasElement = document.getElementById('face-recognition-canvas');

        const updatePreview = (dataUrl) => {
            previewImage.src = dataUrl;
            previewImage.classList.remove('hidden');
        };

        const captureSnapshot = () => {
            if (! videoElement.srcObject) {
                statusElement.textContent = 'Kamera belum aktif. Pastikan izin kamera sudah diberikan.';
                return false;
            }

            if (! videoElement.videoWidth || ! videoElement.videoHeight) {
                return false;
            }

            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            const context = canvasElement.getContext('2d');
            context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            const dataUrl = canvasElement.toDataURL('image/png');
            snapshotInput.value = dataUrl;
            updatePreview(dataUrl);
            statusElement.textContent = 'Scan wajah berhasil direkam dan siap disimpan.';
            return true;
        };

        if (snapshotInput.value) {
            updatePreview(snapshotInput.value);
        }

        if (navigator.mediaDevices?.getUserMedia) {
            navigator.mediaDevices
                .getUserMedia({ video: { facingMode: 'user' } })
                .then((stream) => {
                    videoElement.srcObject = stream;
                    return videoElement.play();
                })
                .then(() => {
                    statusElement.textContent = 'Arahkan wajah ke kamera. Sistem akan merekam otomatis, atau klik tombol rekam bila diperlukan.';
                    if (! snapshotInput.value) {
                        setTimeout(() => {
                            if (! captureSnapshot()) {
                                statusElement.textContent = 'Kamera aktif. Pastikan wajah terlihat jelas, lalu klik tombol rekam untuk menyimpan data wajah.';
                            }
                        }, 1000);
                    }
                })
                .catch(() => {
                    statusElement.textContent = 'Tidak dapat mengakses kamera. Pastikan izin kamera sudah diaktifkan.';
                });
        } else {
            statusElement.textContent = 'Perangkat ini tidak mendukung akses kamera melalui browser.';
        }

        captureButton.addEventListener('click', () => {
            if (! captureSnapshot()) {
                statusElement.textContent = 'Kamera aktif. Pastikan wajah terlihat jelas, lalu klik tombol rekam kembali.';
            }
        });
    </script>
</x-app-layout>
