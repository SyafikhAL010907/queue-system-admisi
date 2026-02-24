<!DOCTYPE html>
<html>
<head>
    <title>Queue Management Pro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {opacity:0; transform: translateY(10px);}
        to {opacity:1; transform: translateY(0);}
    }

    /* DARK MODE FIX */
    .dark-mode {
        background: #0f172a !important;
    }

    .dark-mode .bg-white {
        background: #1e293b !important;
        color: white !important;
    }

    .dark-mode table {
        color: white;
    }

    .dark-mode thead {
        background: #334155 !important;
    }

    .dark-mode input {
        background: #334155 !important;
        color: white !important;
        border-color: #475569 !important;
    }
</style>
</head>

<body id="body" class="bg-gradient-to-br from-blue-100 via-white to-purple-100 min-h-screen p-10 transition-all duration-500">

<div class="max-w-6xl mx-auto fade-in">

    <!-- HEADER -->
    <div class="bg-white p-6 rounded-2xl shadow-xl mb-6 text-center">
        <h1 class="text-4xl font-bold mb-2">
           Manajemen Antrian Admisi
        </h1>
        <p class="text-gray-500">Admin Dashboard</p>

        <div class="mt-4 flex justify-center gap-4">
           <button onclick="toggleDarkMode()"
                class="bg-gray-800 dark:bg-yellow-400 p-2 rounded-lg transition">

                <!-- MOON ICON -->
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-white dark:hidden" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 12.79A9 9 0 1111.21 3
                        7 7 0 0021 12.79z" />
                </svg>

                <!-- SUN ICON -->
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-gray-900 hidden dark:block"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414
                        1.414M6.05 17.95l-1.414 1.414m12.728
                        0l-1.414-1.414M6.05 6.05L4.636 4.636M12 8a4
                        4 0 100 8 4 4 0 000-8z"/>
                </svg>

            </button>

            <a href="/display" target="_blank"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                Lihat Display
            </a>
        </div>
    </div>

    <!-- FORM -->
    <div class="bg-white p-6 rounded-2xl shadow-lg mb-6">
        <form id="addForm" class="flex gap-3">
            <input type="text" name="name" placeholder="Nama Customer"
                class="border p-3 rounded-xl w-full focus:ring-2 focus:ring-blue-400"
                required>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">
                Tambah Data antrian (masukkan nama)
            </button>
        </form>
    </div>

    <!-- STATS -->
    <div id="stats" class="grid grid-cols-4 gap-6 mb-6 text-center"></div>
    <div id="loket" class="grid grid-cols-4 gap-6 mb-6 text-center"></div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">#</th>
                    <th class="p-4">Queue</th>
                    <th class="p-4">Name</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>
            <tbody id="queueTable"></tbody>
        </table>
    </div>

</div>

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
        let waiting=0, called=0, completed=0, canceled=0;

       data.forEach((q,i)=>{

    if(q.status=='waiting') waiting++;
    if(q.status=='called') called++;
    if(q.status=='completed') completed++;
    if(q.status=='canceled') canceled++;

    // ❌ Jangan tampilkan yang selesai / cancel
    if(q.status === 'completed' || q.status === 'canceled'){
        return;
    }

    html += `
    <tr class="border-t fade-in">
        <td class="p-3">${i+1}</td>
        <td class="p-3 font-bold">${q.queue_number}</td>
        <td class="p-3">${q.name}</td>
        <td class="p-3 capitalize">${q.status}</td>
        <td class="p-3 flex gap-2 justify-center">
            <button onclick="callQueue(${q.id}, '${q.name}', 1)"
                class="bg-blue-500 text-white px-2 py-1 rounded text-sm">
                Call L1
            </button>

            <button onclick="callQueue(${q.id}, '${q.name}', 2)"
                class="bg-purple-500 text-white px-2 py-1 rounded text-sm">
                Call L2
            </button>

            <button onclick="callQueue(${q.id}, '${q.name}', 3)"
                class="bg-indigo-500 text-white px-2 py-1 rounded text-sm">
                Call L3
            </button>

            <button onclick="updateStatus(${q.id},'completed')"
                class="bg-green-500 text-white px-2 py-1 rounded text-sm">
                Done
            </button>

            <button onclick="updateStatus(${q.id},'canceled')"
                class="bg-red-500 text-white px-2 py-1 rounded text-sm">
                Cancel
            </button>
        </td>
    </tr>`;
});

        document.getElementById('queueTable').innerHTML = html;

        document.getElementById('stats').innerHTML = `
            <div class="bg-yellow-100 p-4 rounded-xl">Waiting<br><b>${waiting}</b></div>
            <div class="bg-blue-100 p-4 rounded-xl">Called<br><b>${called}</b></div>
            <div class="bg-green-100 p-4 rounded-xl">Completed<br><b>${completed}</b></div>
            <div class="bg-red-100 p-4 rounded-xl">Canceled<br><b>${canceled}</b></div>
        `;

    });



}

// ➕ ADD QUEUE AJAX
document.getElementById('addForm').addEventListener('submit',function(e){
    e.preventDefault();
    let name = this.name.value;

    fetch('/api/queues',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':csrf
        },
        body:JSON.stringify({name:name})
    }).then(()=>{
        Swal.fire('Berhasil!','Antrian ditambahkan','success');
        this.reset();
        loadQueues();
    });
});

// 🔁 UPDATE STATUS
function updateStatus(id,status){

    let endpoint = '';

    if(status === 'completed'){
        endpoint = 'complete';
    }

    if(status === 'canceled'){
        endpoint = 'cancel';
    }

    fetch(`/api/queues/${id}/${endpoint}`,{
        method:'POST',
        headers:{
            'X-CSRF-TOKEN':csrf
        }
    }).then(()=>{
        Swal.fire('Updated!','Status berhasil diubah','success');
        loadQueues();
    });
}

// 🔊 VOICE
function speakQueue(name, loket){
    const msg = new SpeechSynthesisUtterance(
        "Saudara " + name + ", silakan menuju ke loket " + loket
    );
    msg.lang = "id-ID";
    msg.rate = 0.9;
    window.speechSynthesis.speak(msg);
}

// 🌙 DARK MODE
function toggleDarkMode(){
    const body = document.getElementById('body');
    body.classList.toggle('dark-mode');

    if(body.classList.contains('dark-mode')){
        localStorage.setItem('theme','dark');
    } else {
        localStorage.setItem('theme','light');
    }
}

/* LOAD THEME SAAT REFRESH */
if(localStorage.getItem('theme') === 'dark'){
    document.getElementById('body').classList.add('dark-mode');
}

function callQueue(id, name, loket){

    // 1️⃣ Cek dulu apakah loket masih ada yang dipanggil
    fetch('/api/queues')
    .then(res => res.json())
    .then(data => {

        const stillActive = data.find(q =>
            q.status === 'called' && Number(q.loket) === loket
        );

        if(stillActive){
            Swal.fire(
                'Loket Masih Aktif!',
                `Loket ${loket} masih melayani ${stillActive.name}.
                Silakan klik DONE terlebih dahulu.`,
                'warning'
            );
            return;
        }

        // 2️⃣ Jika kosong → lanjut panggil
        fetch(`/api/queues/${id}/call`,{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':csrf
            },
            body: JSON.stringify({loket: loket})
        })
        .then(()=>{
            speakQueue(name, loket);

            Swal.fire(
                'Dipanggil!',
                `Antrian dipanggil ke Loket ${loket}`,
                'success'
            );

            loadQueues();
        });

    });
}


document.getElementById('loket').innerHTML = loketHtml;
</script>

</body>
</html>
