<!DOCTYPE html>
<html>
<head>
    <title>Queue Display</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .animate-pop {
            animation: pop 0.5s ease;
        }

        @keyframes pop {
            0% { transform: scale(0.7); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>

<body class="bg-black text-white min-h-screen p-10">

<h1 class="text-4xl text-center mb-6 tracking-widest">
    LAYAR ANTRIAN
</h1>

<!-- ================= CURRENT CALL BANNER ================= -->
<div id="callingBanner" class="bg-yellow-500 text-black text-center text-3xl font-bold py-4 mb-8 rounded-xl">
    MENUNGGU PANGGILAN...
</div>

<!-- ================= MULTI LOKET ================= -->
<div class="grid grid-cols-3 gap-8 mb-12">

    <div id="loket1" class="bg-gray-900 p-8 rounded-xl text-center"></div>
    <div id="loket2" class="bg-gray-900 p-8 rounded-xl text-center"></div>
    <div id="loket3" class="bg-gray-900 p-8 rounded-xl text-center"></div>

</div>

<!-- ================= LIST SECTION ================= -->
<div class="grid grid-cols-2 gap-8">

    <!-- WAITING -->
    <div class="bg-gray-900 p-6 rounded-xl">
        <h2 class="text-2xl mb-4 text-yellow-300">Waiting</h2>
        <div id="waitingList" class="space-y-2 text-lg"></div>
    </div>

    <!-- COMPLETED -->
    <div class="bg-gray-900 p-6 rounded-xl">
        <h2 class="text-2xl mb-4 text-green-300">Completed</h2>
        <div id="completedList" class="space-y-2 text-lg"></div>
    </div>

</div>

<script>

let lastCalledPerLoket = {1:null,2:null,3:null};
let lastGlobalCalledId = null;

setInterval(loadDisplay,2000);
loadDisplay();

function loadDisplay(){
fetch('/api/queues')
.then(res=>res.json())
.then(data=>{

/* ================= GLOBAL CALLING BANNER ================= */

const latestCalled = data
    .filter(q=>q.status==='called')
    .sort((a,b)=>new Date(b.updated_at)-new Date(a.updated_at))[0];

if(latestCalled){

    document.getElementById('callingBanner').innerHTML =
        `IS CALLING: ${latestCalled.name} - LOKET ${latestCalled.loket}`;

    if(lastGlobalCalledId !== latestCalled.id){
        lastGlobalCalledId = latestCalled.id;
        speakName(latestCalled.name, latestCalled.loket);
    }

}else{
    document.getElementById('callingBanner').innerHTML =
        `MENUNGGU PANGGILAN...`;
}

/* ================= MULTI LOKET ================= */

[1,2,3].forEach(loket=>{

    const active = data
        .filter(q=>q.status==='called' && Number(q.loket) === loket)
        .sort((a,b)=>new Date(b.updated_at)-new Date(a.updated_at))[0];

    const container = document.getElementById(`loket${loket}`);

    if(active){

        container.innerHTML = `
            <h2 class="text-2xl mb-4 text-yellow-400">
                LOKET ${loket}
            </h2>
            <div class="text-5xl font-bold animate-pop">
                ${active.name}
            </div>
            <div class="text-xl mt-2 text-gray-400">
                ${active.queue_number}
            </div>
        `;

    }else{

        container.innerHTML = `
            <h2 class="text-2xl mb-4 text-gray-400">
                LOKET ${loket}
            </h2>
            <div class="text-3xl text-gray-600">
                Kosong
            </div>
        `;
    }

});

/* ================= WAITING ================= */

const waiting = data.filter(q=>q.status==='waiting');

document.getElementById('waitingList').innerHTML =
waiting.length
? waiting.map(q=>`
    <div class="border-b border-gray-700 pb-1">
        ${q.queue_number} - ${q.name}
    </div>
`).join('')
: `<div class="text-gray-500">Tidak ada antrian</div>`;


/* ================= COMPLETED ================= */

const completed = data
    .filter(q=>q.status==='completed')
    .slice(-5)
    .reverse();

document.getElementById('completedList').innerHTML =
completed.length
? completed.map(q=>`
    <div class="border-b border-gray-700 pb-1">
        ${q.queue_number} - ${q.name}
    </div>
`).join('')
: `<div class="text-gray-500">Belum ada selesai</div>`;

});
}


/* ================= VOICE ================= */

function speakName(name,loket){
    const msg=new SpeechSynthesisUtterance(
        "Saudara "+name+", silakan menuju ke loket "+loket
    );
    msg.lang='id-ID';
    msg.rate=0.9;
    speechSynthesis.cancel();
    speechSynthesis.speak(msg);
}

</script>

</body>
</html>
