<!DOCTYPE html>
<html>
<head>
    <title>Reservasi Antrian</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
    <h1 class="text-2xl font-bold mb-4">Reservasi Antrian</h1>

    <input type="text" id="name"
        placeholder="Masukkan Nama"
        class="border p-3 rounded-xl w-full mb-4">

    <button onclick="ambilAntrian()"
        class="bg-blue-600 text-white px-6 py-3 rounded-xl w-full">
        Ambil Nomor Antrian
    </button>

    <div id="result" class="mt-6 text-lg font-bold"></div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

let myQueueId = null;
let myQueueNumber = null;
let pollingInterval = null;

function ambilAntrian(){

    let name = document.getElementById('name').value;

    if(!name){
        alert("Masukkan nama dulu");
        return;
    }

    fetch('/api/queues',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':csrf
        },
        body:JSON.stringify({name:name})
    })
    .then(res=>res.json())
    .then(data=>{

        myQueueId = data.id;
        myQueueNumber = data.queue_number;

        document.getElementById('result').innerHTML =
            "Nomor Antrian Anda:<br><span class='text-3xl text-blue-600'>"
            + data.queue_number + "</span><br><br>"
            + "<span class='text-gray-500'>Menunggu dipanggil...</span>";

        document.getElementById('name').disabled = true;

        mulaiPolling();
    });
}

function mulaiPolling(){

    pollingInterval = setInterval(()=>{

        fetch('/api/queues')
        .then(res=>res.json())
        .then(data=>{

            const myData = data.find(q => q.id === myQueueId);

            if(!myData) return;

            // 🔔 Kalau dipanggil
            if(myData.status === 'called'){
                document.getElementById('result').innerHTML =
                    "<span class='text-green-600 text-2xl'>ANDA DIPANGGIL</span><br><br>"
                    + "Silakan menuju ke<br>"
                    + "<span class='text-3xl font-bold'>LOKET "
                    + myData.loket + "</span>";

                vibratePhone();
            }

            // ✅ Kalau selesai
            if(myData.status === 'completed'){
                document.getElementById('result').innerHTML =
                    "<span class='text-blue-600 text-2xl'>Layanan Selesai</span><br><br>"
                    + "Terima kasih ";

                resetApp();
            }

            // ❌ Kalau cancel
            if(myData.status === 'canceled'){
                document.getElementById('result').innerHTML =
                    "<span class='text-red-600 text-2xl'>Antrian Dibatalkan</span>";

                resetApp();
            }

        });

    },3000);
}

function resetApp(){
    clearInterval(pollingInterval);

    setTimeout(()=>{
        document.getElementById('name').value = '';
        document.getElementById('name').disabled = false;
        document.getElementById('result').innerHTML = '';
        myQueueId = null;
    },4000);
}

function vibratePhone(){
    if(navigator.vibrate){
        navigator.vibrate([500,200,500]);
    }
}
</script>

</body>
</html>
