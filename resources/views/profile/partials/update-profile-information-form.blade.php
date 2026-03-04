<section x-data="{ photoName: null, photoPreview: null }">
    <!-- Cropper.js dependencies -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Actualiza la información de tu perfil y la dirección de correo electrónico.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" class="hidden" x-ref="photo" accept="image/*" name="photo" x-on:change="
                                let file = $refs.photo.files[0];
                                if (file) {
                                    photoName = file.name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        $dispatch('open-modal', 'cropper-modal');
                                        let img = document.getElementById('cropper-image');
                                        img.src = e.target.result;
                                        // Wait a moment for modal to render, then attach cropper
                                        setTimeout(() => {
                                            if (window.profileCropper) window.profileCropper.destroy();
                                            window.profileCropper = new Cropper(img, {
                                                aspectRatio: 1,
                                                viewMode: 1,
                                                autoCropArea: 1,
                                                dragMode: 'move',
                                            });
                                        }, 150);
                                    };
                                    reader.readAsDataURL(file);
                                }
                        " />

            <x-input-label for="photo" :value="__('Foto de Perfil')" />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                    class="rounded-full h-20 w-20 object-cover border-2 border-gray-100 shadow-sm">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview">
                <img :src="photoPreview"
                    class="rounded-full h-20 w-20 object-cover border-2 border-emerald-500 shadow-sm">
            </div>

            <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                {{ __('Seleccionar Nueva Foto') }}
            </x-secondary-button>

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Tu dirección de correo electrónico no ha sido verificada.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Haz clic aquí para volver a enviar el correo de verificación.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>

    <!-- Modal for Cropper -->
    <x-modal name="cropper-modal" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                Ajustar Foto de Perfil
            </h2>
            <div
                class="w-full h-80 bg-gray-50 dark:bg-gray-900 flex justify-center items-center overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <img id="cropper-image" src="" alt="Cropper" class="max-w-full max-h-full block">
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button
                    x-on:click="$dispatch('close'); if(window.profileCropper) { window.profileCropper.destroy(); window.profileCropper = null; $refs.photo.value = ''; }">
                    Cancelar
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="
                    if(window.profileCropper) {
                        const canvas = window.profileCropper.getCroppedCanvas({
                            width: 400,
                            height: 400,
                            imageSmoothingEnabled: true,
                            imageSmoothingQuality: 'high',
                        });
                        
                        canvas.toBlob((blob) => {
                            let file = new File([blob], photoName || 'profile.jpg', { type: blob.type });
                            let dt = new DataTransfer();
                            dt.items.add(file);
                            $refs.photo.files = dt.files;
                            
                            photoPreview = canvas.toDataURL();
                            
                            window.profileCropper.destroy();
                            window.profileCropper = null;
                            $dispatch('close');
                        }, 'image/jpeg', 0.9);
                    }
                ">
                    Recortar y Aplicar
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</section>