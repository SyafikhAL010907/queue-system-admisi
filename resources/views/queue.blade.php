<x-app-layout>
    <x-slot name="header">
        <div class="relative flex items-center justify-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
                {{ __('Manajemen Antrian Admisi') }}
            </h2>
            <div class="absolute right-0 flex gap-4">
                <a href="/display" target="_blank"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition text-sm">
                    Lihat Display
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 fade-in">

            <!-- STATS COUNTER -->
            <div id="stats" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6 text-center"></div>

            <!-- FORM TAMBAH -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm mb-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4 text-center">Tambah Antrian Baru</h3>
                <form id="addForm" class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="name" placeholder="Nama Customer"
                        class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 p-3 rounded-xl w-full focus:ring-2 focus:ring-blue-400 outline-none"
                        required>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition whitespace-nowrap">
                        Tambah Data
                    </button>
                </form>
            </div>

            <!-- TABLE MANAJEMEN -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden text-gray-900 dark:text-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">Queue</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">Name</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="queueTable">
                        <!-- Loading placeholder atau data awal jika ada -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // 🔄 AUTO REFRESH
        setInterval(loadQueues, 5000);
        loadQueues();

        // LOAD DATA
        function loadQueues() {
            fetch('/api/queues')
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    let waiting = 0, called = 0, completed = 0, canceled = 0;

                    data.forEach((q, i) => {
                        if (q.status == 'waiting') waiting++;
                        if (q.status == 'called') called++;
                        if (q.status == 'completed') completed++;
                        if (q.status == 'canceled') canceled++;

                        if (q.status === 'completed' || q.status === 'canceled') return;

                        html += `
                    <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 fade-in">
                        <td class="p-4">${i + 1}</td>
                        <td class="p-4 font-bold">${q.queue_number}</td>
                        <td class="p-4">${q.name}</td>
                        <td class="p-4 capitalize">${q.status}</td>
                        <td class="p-4 flex flex-wrap gap-2 justify-center">
                            <button onclick="callQueue(${q.id}, '${q.name}', 1)" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition">Call L1</button>
                            <button onclick="callQueue(${q.id}, '${q.name}', 2)" class="bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded text-xs transition">Call L2</button>
                            <button onclick="callQueue(${q.id}, '${q.name}', 3)" class="bg-indigo-500 hover:bg-indigo-600 text-white px-2 py-1 rounded text-xs transition">Call L3</button>
                            <button onclick="updateStatus(${q.id},'completed')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs transition">Done</button>
                            <button onclick="updateStatus(${q.id},'canceled')" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition">Cancel</button>
                        </td>
                    </tr>`;
                    });

                    document.getElementById('queueTable').innerHTML = html || '<tr><td colspan="5" class="p-4 text-center text-gray-500">Antrian kosong.</td></tr>';

                    document.getElementById('stats').innerHTML = `
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200">Waiting<br><b class="text-2xl">${waiting}</b></div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-4 rounded-xl border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200">Called<br><b class="text-2xl">${called}</b></div>
                    <div class="bg-green-100 dark:bg-green-900/30 p-4 rounded-xl border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200">Completed<br><b class="text-2xl">${completed}</b></div>
                    <div class="bg-red-100 dark:bg-red-900/30 p-4 rounded-xl border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200">Canceled<br><b class="text-2xl">${canceled}</b></div>
                `;
                });
        }

        // ➕ ADD QUEUE AJAX
        document.getElementById('addForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let name = this.name.value;

            fetch('/api/queues', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ name: name })
            }).then(() => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Antrian berhasil ditambahkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                this.reset();
                loadQueues();
            });
        });

        // 🔁 UPDATE STATUS
        function updateStatus(id, status) {
            let endpoint = status === 'completed' ? 'complete' : 'cancel';

            fetch(`/api/queues/${id}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            }).then(() => {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Status antrian berhasil diubah',
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false
                });
                loadQueues();
            });
        }

        // 🔊 VOICE
        function speakQueue(name, loket) {
            const msg = new SpeechSynthesisUtterance(`Saudara ${name}, silakan menuju ke loket ${loket}`);
            msg.lang = "id-ID";
            msg.rate = 0.9;
            window.speechSynthesis.speak(msg);
        }

        function callQueue(id, name, loket) {
            fetch('/api/queues')
                .then(res => res.json())
                .then(data => {
                    const stillActive = data.find(q => q.status === 'called' && Number(q.loket) === loket);

                    if (stillActive) {
                        Swal.fire('Loket Masih Aktif!', `Loket ${loket} masih melayani ${stillActive.name}.`, 'warning');
                        return;
                    }

                    fetch(`/api/queues/${id}/call`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ loket: loket })
                    })
                        .then(() => {
                            speakQueue(name, loket);
                            Swal.fire({
                                title: 'Dipanggil!',
                                text: `Antrian dipanggil ke Loket ${loket}`,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadQueues();
                        });
                });
        }
    </script>
</x-app-layout>