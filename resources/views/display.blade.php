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
            0% {
                transform: scale(0.7);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 via-white to-blue-50 text-gray-800 min-h-screen p-6 md:p-10 font-sans">

    <div class="max-w-7xl mx-auto">
        <h1
            class="text-4xl md:text-5xl font-black text-center mb-10 tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
            LAYAR ANTRIAN ADMISI
        </h1>

        <!-- ================= CURRENT CALL BANNER ================= -->
        <div id="callingBanner"
            class="bg-white/80 backdrop-blur-md text-purple-600 text-center text-3xl md:text-4xl font-black py-8 mb-12 rounded-3xl shadow-xl border-2 border-purple-200 animate-pulse">
            MENUNGGU PANGGILAN...
        </div>

        <!-- ================= MULTI LOKET ================= -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div id="loket1"
                class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl text-center border border-purple-100 shadow-sm transition hover:shadow-md">
            </div>
            <div id="loket2"
                class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl text-center border border-blue-100 shadow-sm transition hover:shadow-md">
            </div>
            <div id="loket3"
                class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl text-center border border-indigo-100 shadow-sm transition hover:shadow-md">
            </div>
            <div id="loket4"
                class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl text-center border border-purple-100 shadow-sm transition hover:shadow-md">
            </div>
        </div>

        <!-- ================= LIST SECTION ================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- WAITING -->
            <div class="bg-white/60 backdrop-blur-sm p-8 rounded-3xl border border-yellow-100 shadow-sm">
                <h2 class="text-2xl font-bold mb-6 text-yellow-600 flex items-center gap-2">
                    <span class="w-3 h-3 bg-yellow-400 rounded-full"></span>
                    Waiting List
                </h2>
                <div id="waitingList" class="space-y-3 text-xl font-medium"></div>
            </div>

            <!-- COMPLETED -->
            <div class="bg-white/60 backdrop-blur-sm p-8 rounded-3xl border border-green-100 shadow-sm">
                <h2 class="text-2xl font-bold mb-6 text-green-600 flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                    Recently Completed
                </h2>
                <div id="completedList" class="space-y-3 text-xl font-medium"></div>
            </div>
        </div>
    </div>

    <script>

        let lastGlobalCalledId = null;

        setInterval(loadDisplay, 2000);
        loadDisplay();

        function loadDisplay() {
            fetch('/api/queues')
                .then(res => res.json())
                .then(data => {

                    /* ================= GLOBAL CALLING BANNER ================= */

                    const latestCalled = data
                        .filter(q => q.status === 'called')
                        .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0];

                    if (latestCalled) {

                        document.getElementById('callingBanner').innerHTML =
                            `IS CALLING: ${latestCalled.name} - LOKET ${latestCalled.loket}`;

                        if (lastGlobalCalledId !== latestCalled.id) {
                            lastGlobalCalledId = latestCalled.id;
                            speakName(latestCalled.name, latestCalled.queue_number, latestCalled.loket);
                        }

                    } else {
                        document.getElementById('callingBanner').innerHTML =
                            `MENUNGGU PANGGILAN...`;
                    }

                    /* ================= MULTI LOKET ================= */

                    [1, 2, 3, 4].forEach(loket => {

                        const active = data
                            .filter(q => q.status === 'called' && Number(q.loket) === loket)
                            .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0];

                        const container = document.getElementById(`loket${loket}`);

                        if (active) {

                            container.innerHTML = `
            <h2 class="text-sm font-black mb-4 text-purple-400 uppercase tracking-widest">
                LOKET ${loket}
            </h2>
            <div class="text-5xl font-black text-gray-800 animate-pop mb-2">
                ${active.name}
            </div>
            <div class="text-2xl font-bold py-2 px-4 bg-purple-50 text-purple-600 rounded-2xl inline-block shadow-inner">
                ${active.queue_number}
            </div>
        `;

                        } else {

                            container.innerHTML = `
            <h2 class="text-sm font-black mb-4 text-gray-300 uppercase tracking-widest">
                LOKET ${loket}
            </h2>
            <div class="text-3xl font-bold text-gray-200">
                OFFLINE
            </div>
        `;
                        }

                    });

                    /* ================= WAITING ================= */

                    const waiting = data.filter(q => q.status === 'waiting');

                    document.getElementById('waitingList').innerHTML =
                        waiting.length
                            ? waiting.map(q => `
    <div class="bg-white/50 p-4 rounded-2xl mb-2 flex justify-between items-center shadow-sm">
        <span class="text-gray-500 font-bold">${q.queue_number}</span>
        <span class="text-gray-800 font-black">${q.name}</span>
    </div>
`).join('')
                            : `<div class="text-gray-400 italic text-center py-4">No one in line</div>`;


                    /* ================= COMPLETED ================= */

                    const completed = data
                        .filter(q => q.status === 'completed')
                        .slice(-5)
                        .reverse();

                    document.getElementById('completedList').innerHTML =
                        completed.length
                            ? completed.map(q => `
    <div class="bg-white/50 p-4 rounded-2xl mb-2 flex justify-between items-center opacity-70">
        <span class="text-gray-400 font-bold">${q.queue_number}</span>
        <span class="text-gray-600 font-bold">${q.name}</span>
    </div>
`).join('')
                            : `<div class="text-gray-400 italic text-center py-4">No history yet</div>`;

                });
        }


        /* ================= VOICE ================= */

        /* ================= EXPERT AUDIO ENGINE ================= */

        /* ================= EXPERT AUDIO ENGINE ================= */

        /* ================= BULLETPROOF UNIVERSAL AUDIO ENGINE ================= */

        // 🌍 GLOBAL LANGUAGE TRIGGER (V5)
        let activeLang = localStorage.getItem('queue_lang') || 'ID';

        window.addEventListener('storage', (e) => {
            if (e.key === 'queue_lang') {
                activeLang = e.newValue;
                console.log("V5 Engine: Display Syncing Language ->", activeLang);
            }
        });

        function speakName(name, number, loket) {
            // 🔍 DYNAMIC LANGUAGE CHECK
            const currentLang = localStorage.getItem('queue_lang') || 'ID';
            window.speechSynthesis.cancel();

            let message = "";
            let speechLang = "id-ID";

            if (currentLang === 'EN') {
                message = `Calling for ${name}, queue number ${number}, please proceed to counter ${loket}.`;
                speechLang = "en-US";
            } else if (currentLang === 'ZH') {
                message = `请 ${name}, ${number} 号, 到 ${loket} 号窗口.`;
                speechLang = "zh-CN";
            } else {
                const formatPhonetic = (str) => {
                    return str.toString().split('').map(char => {
                        if (char === '0') return 'kosong';
                        return char;
                    }).join(', ');
                };
                const phoneticNumber = formatPhonetic(number);
                message = `Panggilan untuk saudara, ${name}. Nomor antrian, ${phoneticNumber}. Silakan menuju ke, loket ${loket}.`;
                speechLang = "id-ID";
            }

            const utterance = new SpeechSynthesisUtterance(message);
            utterance.rate = 0.8;
            utterance.pitch = 1.0;
            utterance.lang = speechLang;

            const setVoice = () => {
                const voices = window.speechSynthesis.getVoices();
                let chosenVoice = null;

                if (currentLang === 'EN') {
                    chosenVoice = voices.find(v => v.lang.toLowerCase().includes("en-us") && (v.name.toLowerCase().includes("female") || v.name.toLowerCase().includes("google") || v.name.toLowerCase().includes("natural")))
                        || voices.find(v => v.lang.toLowerCase().includes("en-us"));
                } else if (currentLang === 'ZH') {
                    chosenVoice = voices.find(v => v.lang.toLowerCase().includes("zh-cn") && (v.name.toLowerCase().includes("female") || v.name.toLowerCase().includes("google") || v.name.toLowerCase().includes("natural")))
                        || voices.find(v => v.lang.toLowerCase().includes("zh-cn"));
                } else {
                    const idVoices = voices.filter(v => v.lang.toLowerCase().includes("id-id") || v.lang.toLowerCase().includes("id_id"));
                    chosenVoice = idVoices.find(v => {
                        const n = v.name.toLowerCase();
                        return (n.includes("gadis") || n.includes("natural") || n.includes("online")) && !n.includes("andika");
                    }) || idVoices.find(v => {
                        const n = v.name.toLowerCase();
                        return (n.includes("google") || n.includes("siti") || n.includes("female")) && !n.includes("andika");
                    }) || idVoices.find(v => !v.name.toLowerCase().includes("andika")) || idVoices[0];
                }

                if (chosenVoice) {
                    utterance.voice = chosenVoice;
                    console.log(`V5.1 Engine Display: [${currentLang}] Selected -`, chosenVoice.name);
                }

                window.speechSynthesis.speak(utterance);
            };

            if (window.speechSynthesis.getVoices().length > 0) {
                setVoice();
            } else {
                window.speechSynthesis.onvoiceschanged = setVoice;
            }
        }

    </script>

</body>

</html>