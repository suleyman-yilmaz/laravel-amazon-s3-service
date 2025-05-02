<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Amazon S3 Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .modal-hidden {
            display: none;
        }

        .modal-visible {
            display: flex;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 p-8">
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Görsel Yükleme</h1>
        <form id="uploadForm" action="{{ route('s3.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div
                class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center hover:bg-blue-50 transition-colors cursor-pointer mb-6">
                <div class="flex flex-col items-center">
                    <label class="cursor-pointer">
                        <span
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-md font-medium transition-colors">
                            Dosya Seç
                        </span>
                        <input type="file" id="imageInput" name="images[]" class="hidden" accept="image/*" multiple>
                    </label>
                    <p class="text-xs text-gray-500 mt-4">JPG, PNG veya GIF - Maks. 5MB</p>
                </div>
                <div id="previewContainer" class="flex flex-wrap gap-2 mt-4 justify-center"></div>
            </div>
            <button type="submit"
                class="bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-md font-medium transition-colors mb-4">
                Yükle
            </button>
        </form>
        <button id="showModalBtn"
            class="flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-900 text-white py-2 px-6 rounded-md font-medium transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            Yüklenen Görselleri Görüntüle
        </button>
    </div>
    <div id="imagesModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 modal-hidden">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-auto m-4">
            <div class="flex justify-between items-center p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">Yüklenen Görseller</h2>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($images as $item)
                        <div class="border rounded-lg overflow-hidden">
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="p-3 border-t">
                                <p class="font-medium text-gray-800 truncate">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($item['size'] / 1024, 2) }} KB</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        const showModalBtn = document.getElementById('showModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const imagesModal = document.getElementById('imagesModal');

        showModalBtn.addEventListener('click', function() {
            imagesModal.classList.remove('modal-hidden');
            imagesModal.classList.add('modal-visible');
        });

        closeModalBtn.addEventListener('click', function() {
            imagesModal.classList.remove('modal-visible');
            imagesModal.classList.add('modal-hidden');
        });

        imagesModal.addEventListener('click', function(e) {
            if (e.target === imagesModal) {
                imagesModal.classList.remove('modal-visible');
                imagesModal.classList.add('modal-hidden');
            }
        });

        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');

        imageInput.addEventListener('change', function(event) {
            const files = event.target.files;
            previewContainer.innerHTML = '';

            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-16 h-16 object-cover rounded border border-gray-300 shadow';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>

</html>
