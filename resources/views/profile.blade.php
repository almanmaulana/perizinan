<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-user-circle text-blue-400"></i>
                Profil Saya
            </h2>

            <a href="{{ route('dashboard') }}"
               class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-lg shadow flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10 min-h-screen text-gray-900 dark:text-gray-200 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-5xl mx-auto px-4 flex flex-col gap-8">

            {{-- ALERT --}}
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 p-3 rounded-lg shadow text-center">
                    {{ session('success') }}
                </div>
            @endif

            {{-- CARD PROFIL --}}
            <div class="bg-gray-100 dark:bg-[#2f2f2f] rounded-2xl shadow-xl p-8 flex flex-col md:flex-row gap-8">

                {{-- FOTO PROFIL --}}
                <div class="flex flex-col items-center md:w-1/3">

                    <div class="relative w-44 h-44">
                        <img id="foto-preview"
                             src="{{ $user->foto ? asset('storage/'.$user->foto) : asset('image/user.png') }}"
                             alt="Foto {{ $user->nama }}"
                             class="w-44 h-44 rounded-xl object-cover border border-gray-300 dark:border-gray-600 shadow-lg cursor-pointer"
                             onclick="document.getElementById('foto-modal').classList.remove('hidden')">

                        {{-- TOMBOL KAMERA (rapi kanan bawah) --}}
                        <label for="foto-input"
                               class="absolute bottom-2 right-2 bg-blue-600 text-white p-2 rounded-full shadow cursor-pointer hover:opacity-90">
                            <i class="fa-solid fa-camera"></i>
                        </label>
                    </div>

                    {{-- FORM UPLOAD FOTO --}}
                    <form id="foto-form" method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="mt-5 w-full text-center">
                        @csrf
                        <input type="file" name="foto" id="foto-input" class="hidden" accept="image/*">

                        <button id="btn-foto" disabled
                                class="mt-3 bg-blue-600 opacity-50 cursor-not-allowed hover:bg-blue-700 px-4 py-2 rounded-lg text-white w-full transition">
                            Simpan Foto
                        </button>
                    </form>

                </div>

                {{-- INFORMASI PROFIL --}}
                <div class="md:w-2/3">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-blue-400"></i> Informasi Akun
                    </h3>

                    <form method="POST" action="{{ route('profile.update') }}" id="profile-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label>Nama</label>
                                <input type="text" name="nama" value="{{ $user->nama }}"
                                       class="profile-input w-full px-3 py-2 rounded bg-gray-200 dark:bg-[#1f1f1f] border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200">
                            </div>

                            <div>
                                <label>Email</label>
                                <input type="email" name="email" value="{{ $user->email }}"
                                       class="profile-input w-full px-3 py-2 rounded bg-gray-200 dark:bg-[#1f1f1f] border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200">
                            </div>

                            <div>
                                <label>No HP</label>
                                <input type="text" name="no_hp" value="{{ $user->no_hp }}"
                                       class="profile-input w-full px-3 py-2 rounded bg-gray-200 dark:bg-[#1f1f1f] border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200">
                            </div>

                            <div>
                                <label>Kode Keluarga</label>

                                @if($user->role === 'wali_santri')
                                    <input type="text" value="{{ $user->kode_keluarga }}" readonly
                                           class="w-full px-3 py-2 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded border border-gray-300 dark:border-gray-700">
                                    <p class="text-red-600 dark:text-red-400 text-sm">Tidak dapat diubah.</p>
                                @else
                                    <input type="text" name="kode_keluarga" value="{{ $user->kode_keluarga }}"
                                           class="profile-input w-full px-3 py-2 rounded bg-gray-200 dark:bg-[#1f1f1f] border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200">
                                @endif
                            </div>

                        </div>

                        <button id="btn-profile" disabled
                                class="mt-4 bg-green-600 opacity-50 cursor-not-allowed hover:bg-green-700 px-5 py-2 rounded-lg text-white w-full">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- UBAH PASSWORD --}}
            <div class="bg-gray-100 dark:bg-[#2f2f2f] p-6 rounded-2xl shadow-xl">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-blue-400"></i> Ubah Password
                </h3>

                <form method="POST" action="{{ route('profile.password') }}" id="password-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf

                    <input type="password" id="pass1" name="password" placeholder="Password baru"
                           class="w-full px-3 py-2 rounded bg-gray-200 dark:bg-[#1f1f1f] border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200">

                    <input type="password" id="pass2" name="password_confirmation" placeholder="Konfirmasi password"
                           class="w-full px-3 py-2 rounded bg-gray-200 dark:bg-[#1f1f1f] border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200">

                    <button id="btn-password" disabled
                            class="bg-yellow-600 opacity-50 cursor-not-allowed hover:bg-yellow-700 px-4 py-2 rounded-lg text-white">
                        Update Password
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- MODAL FOTO --}}
    <div id="foto-modal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
        <img id="foto-modal-img" src="{{ $user->foto ? asset('storage/'.$user->foto) : asset('image/user.png') }}" class="max-h-[90vh] max-w-[90vw] rounded-xl shadow-lg">
    </div>

    {{-- SCRIPT --}}
    <script>
        const fotoInput = document.getElementById('foto-input');
        const fotoPreview = document.getElementById('foto-preview');
        const fotoModalImg = document.getElementById('foto-modal-img');
        const btnFoto = document.getElementById('btn-foto');
        const fotoForm = document.getElementById('foto-form');

        fotoInput.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = e => {
                fotoPreview.src = e.target.result;
                fotoModalImg.src = e.target.result;
            };
            reader.readAsDataURL(file);

            btnFoto.disabled = false;
            btnFoto.classList.remove('opacity-50','cursor-not-allowed');
        });

        const profileInputs = document.querySelectorAll('.profile-input');
        const btnProfile = document.getElementById('btn-profile');
        let originalValues = {};
        profileInputs.forEach(i => originalValues[i.name] = i.value);

        profileInputs.forEach(input => {
            input.addEventListener('input', () => {
                let changed = false;
                profileInputs.forEach(i => { 
                    if (i.value !== originalValues[i.name]) changed = true;
                });
                btnProfile.disabled = !changed;
                btnProfile.classList.toggle('opacity-50', !changed);
                btnProfile.classList.toggle('cursor-not-allowed', !changed);
            });
        });

        const pass1 = document.getElementById('pass1');
        const pass2 = document.getElementById('pass2');
        const btnPass = document.getElementById('btn-password');

        function checkPassword() {
            let ok = pass1.value && pass1.value === pass2.value;
            btnPass.disabled = !ok;
            btnPass.classList.toggle('opacity-50', !ok);
            btnPass.classList.toggle('cursor-not-allowed', !ok);
        }

        pass1.addEventListener('input', checkPassword);
        pass2.addEventListener('input', checkPassword);
    </script>
</x-app-layout>
