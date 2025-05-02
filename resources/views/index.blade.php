<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Amazon S3 Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Görsel Yükleme</h1>

        <!-- Dosya yükleme alanı -->
        <div
            class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center hover:bg-blue-50 transition-colors cursor-pointer mb-6">
            <div class="flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-500 mb-2" width="40" height="40"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p class="text-lg font-medium text-gray-700 mb-1">Dosyaları buraya sürükleyin</p>
                <p class="text-sm text-gray-500 mb-4">veya</p>
                <label class="cursor-pointer">
                    <span
                        class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-md font-medium transition-colors">
                        Dosya Seç
                    </span>
                    <input type="file" class="hidden" accept="image/*" multiple>
                </label>
                <p class="text-xs text-gray-500 mt-4">JPG, PNG veya GIF - Maks. 5MB</p>
            </div>
        </div>

        <!-- Yüklenen görselleri görüntüleme butonu -->
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

    <!-- Yüklenen görseller modalı -->
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
                    <!-- Görsel 1 -->
                    <div class="border rounded-lg overflow-hidden">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <img src="/api/placeholder/400/300" alt="image1.jpg" class="w-full h-full object-cover">
                        </div>
                        <div class="p-3 border-t">
                            <p class="font-medium text-gray-800 truncate">image1.jpg</p>
                            <p class="text-sm text-gray-500">2.4 MB</p>
                        </div>
                    </div>

                    <!-- Görsel 2 -->
                    <div class="border rounded-lg overflow-hidden">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <img src="/api/placeholder/400/300" alt="gorsel2.png" class="w-full h-full object-cover">
                        </div>
                        <div class="p-3 border-t">
                            <p class="font-medium text-gray-800 truncate">gorsel2.png</p>
                            <p class="text-sm text-gray-500">1.8 MB</p>
                        </div>
                    </div>

                    <!-- Görsel 3 -->
                    <div class="border rounded-lg overflow-hidden">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <img src="/api/placeholder/400/300" alt="tatil_fotografi.jpg"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-3 border-t">
                            <p class="font-medium text-gray-800 truncate">tatil_fotografi.jpg</p>
                            <p class="text-sm text-gray-500">3.2 MB</p>
                        </div>
                    </div>
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
    </script>
</body>

</html>
