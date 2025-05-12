<!DOCTYPE html>
<html lang="en" class="bg-gradient-to-br from-blue-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Train Station Display - {{ $stationName ?? 'Centric Ltd' }}</title>
  <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      font-family: 'Inter', Arial, sans-serif;
      background: inherit;
    }
    .glass {
      background: rgba(255,255,255,0.15);
      box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 2rem;
      border: 1.5px solid rgba(255,255,255,0.25);
    }
    .neu {
      box-shadow: 4px 4px 20px #b8b9be, -4px -4px 20px #fff;
      border-radius: 1.5rem;
      background: #f6f6fa;
    }
    .neu-dark {
      box-shadow: 4px 4px 20px #23232a, -4px -4px 20px #363646;
      border-radius: 1.5rem;
      background: #23232a;
    }
    .bg-animated {
      position: fixed;
      top:0; left:0; width:100vw; height:100vh;
      z-index: 0;
      pointer-events: none;
      opacity: 0.22;
    }
    .text-shadow {
      text-shadow: 0 2px 8px rgba(0,0,0,0.14);
    }
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-thumb { background: #e1e4ea; border-radius: 20px;}
    html.dark ::-webkit-scrollbar-thumb { background: #2d2d3b;}
    /* Accessibility focus */
    *:focus { outline: 2.5px solid #3b82f6 !important; outline-offset: 2px;}
    .hidden { display: none !important; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 transition-colors duration-700 relative overflow-x-hidden">

  <!-- Animated Background -->
  <canvas id="bg-anim" class="bg-animated"></canvas>

  <!-- Dark/Light Mode Toggle -->
  <button aria-label="Toggle dark mode"
    class="fixed top-4 right-4 z-40 p-3 neu dark:neu-dark text-blue-900 dark:text-blue-200 transition-colors"
    id="toggleModeBtn">
    <i class="fas fa-moon hidden dark:inline"></i>
    <i class="fas fa-sun dark:hidden"></i>
  </button>

  <!-- Main Display Content -->
  <main class="min-h-screen flex flex-col items-center justify-center px-2 py-12 relative z-10">
    <div class="max-w-6xl w-full mx-auto px-2">
      <div class="glass neu dark:neu-dark p-8 mb-8 shadow-xl border-2 border-blue-100 dark:border-blue-900">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-4xl lg:text-5xl font-extrabold text-blue-900 dark:text-blue-200 text-shadow tracking-wide">{{ $stationName }} Station</h1>
            <h2 class="text-2xl lg:text-3xl font-bold text-blue-600 dark:text-blue-200 mt-1 mb-2 text-shadow">Train Schedule Display</h2>
          </div>
          <div class="flex flex-col items-end">
            <span id="displayTime" class="mb-2 text-lg text-gray-600 dark:text-gray-300 font-mono font-bold"></span>
            <span id="lastUpdated" class="text-sm text-gray-500 dark:text-gray-400">Last updated: <span>{{ now()->format('H:i:s') }}</span></span>
          </div>
        </div>
      </div>

      <!-- Notices Section -->
      <section class="mb-8">
        <div class="glass neu dark:neu-dark p-6 border-l-8 border-yellow-400 shadow-lg">
          <div class="flex items-center gap-3 mb-2">
            <i class="fas fa-bullhorn text-yellow-500 text-2xl"></i>
            <h3 class="text-2xl font-bold text-yellow-800 dark:text-yellow-200">Important Notices</h3>
          </div>
          <div id="noticesList" class="space-y-6">
            @if(count($notices) > 0)
              @foreach($notices as $notice)
                <div class="glass p-4 rounded-xl border-l-4 border-yellow-400 shadow transition-all">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    <span class="text-xl font-bold text-red-700 dark:text-yellow-200">{{ $notice['title'] }}</span>
                  </div>
                  <p class="text-lg mt-1 text-gray-900 dark:text-gray-100">{{ $notice['message'] }}</p>
                  <small class="block mt-2 text-gray-500 dark:text-gray-300">
                    {{ isset($notice['created_at']) ? \Carbon\Carbon::parse($notice['created_at'])->format('M d, Y H:i') : '' }}
                  </small>
                </div>
              @endforeach
            @else
              <div class="text-lg text-gray-700 dark:text-gray-200 font-semibold">No notices at this time.</div>
            @endif
          </div>
        </div>
      </section>

      <!-- Trips Section -->
      <section>
        <div class="flex items-center gap-3 mb-3">
          <i class="fas fa-train text-blue-600 text-2xl"></i>
          <h3 class="text-2xl font-bold text-blue-800 dark:text-blue-200">Upcoming Trips</h3>
        </div>
        <div id="tripsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          @if(count($trips) > 0)
            @foreach($trips as $trip)
              @php
                $status = "On Time";
                $statusClass = "text-green-600 dark:text-green-300";
                $statusIcon = "fa-check-circle";
                if (!empty($trip['public_notices'])) {
                  foreach ($trip['public_notices'] as $notice) {
                    if ($notice['type'] === 'cancellation') {
                      $status = "Cancelled";
                      $statusClass = "text-gray-500 dark:text-gray-300";
                      $statusIcon = "fa-ban";
                      break;
                    } elseif ($notice['type'] === 'delay') {
                      $status = "Delayed";
                      $statusClass = "text-red-600 dark:text-red-300";
                      $statusIcon = "fa-clock";
                    }
                  }
                }
              @endphp
              <div class="glass neu dark:neu-dark p-6 rounded-2xl shadow-lg border-l-8 border-blue-500 mb-2 flex flex-col justify-between h-full transition-all">
                <div>
                  <h4 class="text-2xl font-bold text-blue-800 dark:text-blue-200 mb-1 text-shadow">
                    {{ $trip['title'] ?? ($trip['end_to']['name'] ?? 'Trip') }}
                  </h4>
                  <div class="mb-2 text-lg font-mono text-gray-800 dark:text-gray-100 leading-snug">
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">From:</span> {{ $trip['start_from']['name'] ?? '' }}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">To:</span> {{ $trip['end_to']['name'] ?? '' }}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">Departure:</span> {{ $trip['schedule']['start_from'] ?? '' }}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">Arrival:</span> {{ $trip['schedule']['end_at'] ?? '' }}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">Duration:</span> {{ $trip['route']['time'] ?? '' }}</span>
                  </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                  <span class="text-xl font-extrabold {{ $statusClass }}">
                    <i class="fas {{ $statusIcon }}"></i> {{ $status }}
                  </span>
                </div>
                @if(!empty($trip['public_notices']))
                  @foreach($trip['public_notices'] as $notice)
                    <div class="mt-2 p-2 bg-yellow-100 dark:bg-yellow-900 rounded text-sm text-yellow-900 dark:text-yellow-200 shadow">
                      <strong>{{ $notice['title'] }}</strong>: {{ $notice['message'] }}
                    </div>
                  @endforeach
                @endif
              </div>
            @endforeach
          @else
            <div class="col-span-full glass p-6 rounded-xl text-xl font-bold text-center text-blue-600 dark:text-blue-200 shadow">
              No upcoming trips scheduled.
            </div>
          @endif
        </div>
      </section>
    </div>
  </main>

  <!-- Accessibility Live Region for Announcements -->
  <div id="ariaLive" class="sr-only" aria-live="polite"></div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
  <script>
    // Mode toggle
    const html = document.documentElement;
    const toggleBtn = document.getElementById('toggleModeBtn');
    function setMode(dark) {
      if (dark) html.classList.add('dark');
      else html.classList.remove('dark');
      // Update icons
      toggleBtn.querySelector('.fa-moon').classList.toggle('hidden', !dark);
      toggleBtn.querySelector('.fa-sun').classList.toggle('hidden', dark);
    }
    function getPrefMode() {
      return localStorage.getItem('mode') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }
    setMode(getPrefMode() === 'dark');
    toggleBtn.onclick = () => {
      const dark = !html.classList.contains('dark');
      setMode(dark);
      localStorage.setItem('mode', dark ? 'dark' : 'light');
    };

    // Animated background
    function animateBackground() {
      const canvas = document.getElementById('bg-anim');
      const ctx = canvas.getContext('2d');
      let w = canvas.width = window.innerWidth;
      let h = canvas.height = window.innerHeight;
      let circles = Array.from({length: 28}, () => ({
        x: Math.random() * w,
        y: Math.random() * h,
        r: 20 + Math.random() * 50,
        dx: (Math.random() - 0.5) * 0.4,
        dy: (Math.random() - 0.5) * 0.4,
        c: `rgba(${180+Math.floor(Math.random()*60)},${190+Math.floor(Math.random()*40)},${230+Math.floor(Math.random()*20)},0.16)`
      }));
      function resize() {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
      }
      window.addEventListener('resize', resize);
      function loop() {
        ctx.clearRect(0,0,w,h);
        for (const c of circles) {
          ctx.beginPath();
          ctx.arc(c.x, c.y, c.r, 0, 2 * Math.PI);
          ctx.fillStyle = c.c;
          ctx.fill();
          c.x += c.dx; c.y += c.dy;
          if (c.x < -c.r) c.x = w + c.r;
          if (c.x > w + c.r) c.x = -c.r;
          if (c.y < -c.r) c.y = h + c.r;
          if (c.y > h + c.r) c.y = -c.r;
        }
        requestAnimationFrame(loop);
      }
      loop();
    }
    animateBackground();

    // Update clock
    function updateClock() {
      const now = new Date();
      document.getElementById('displayTime').textContent =
        now.toLocaleDateString(undefined,{weekday:'short', year:'numeric', month:'short', day:'numeric'}) + ' ' +
        now.toLocaleTimeString(undefined, {hour:'2-digit',minute:'2-digit',second:'2-digit'});
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Auto-refresh logic
    function refreshData() {
      fetch('{{ route("refresh.display") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          console.error(data.error);
          return;
        }

        // Update last updated time
        const now = new Date();
        document.querySelector('#lastUpdated span').textContent = 
          now.toLocaleTimeString(undefined, {hour:'2-digit',minute:'2-digit',second:'2-digit'});

        // Update notices
        let noticesHtml = '';
        if (data.notices && data.notices.length > 0) {
          data.notices.forEach(notice => {
            noticesHtml += `
              <div class="glass p-4 rounded-xl border-l-4 border-yellow-400 shadow transition-all">
                <div class="flex items-center gap-2">
                  <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                  <span class="text-xl font-bold text-red-700 dark:text-yellow-200">${notice.title}</span>
                </div>
                <p class="text-lg mt-1 text-gray-900 dark:text-gray-100">${notice.message}</p>
                <small class="block mt-2 text-gray-500 dark:text-gray-300">
                  ${notice.created_at ? new Date(notice.created_at).toLocaleString() : ''}
                </small>
              </div>
            `;
          });
        } else {
          noticesHtml = '<div class="text-lg text-gray-700 dark:text-gray-200 font-semibold">No notices at this time.</div>';
        }
        document.getElementById('noticesList').innerHTML = noticesHtml;

        // Update trips
        let tripsHtml = '';
        if (data.trips && data.trips.length > 0) {
          data.trips.forEach(trip => {
            let status = "On Time", statusClass="text-green-600 dark:text-green-300", statusIcon="fa-check-circle";
            if (trip.public_notices && trip.public_notices.length > 0) {
              trip.public_notices.forEach(notice => {
                if (notice.type === 'cancellation') {
                  status = "Cancelled";
                  statusClass = "text-gray-500 dark:text-gray-300";
                  statusIcon = "fa-ban";
                } else if (notice.type === 'delay' && status !== "Cancelled") {
                  status = "Delayed";
                  statusClass = "text-red-600 dark:text-red-300";
                  statusIcon = "fa-clock";
                }
              });
            }

            let tripNotices = '';
            if (trip.public_notices && trip.public_notices.length > 0) {
              trip.public_notices.forEach(notice => {
                tripNotices += `
                  <div class="mt-2 p-2 bg-yellow-100 dark:bg-yellow-900 rounded text-sm text-yellow-900 dark:text-yellow-200 shadow">
                    <strong>${notice.title}</strong>: ${notice.message}
                  </div>
                `;
              });
            }

            tripsHtml += `
              <div class="glass neu dark:neu-dark p-6 rounded-2xl shadow-lg border-l-8 border-blue-500 mb-2 flex flex-col justify-between h-full transition-all">
                <div>
                  <h4 class="text-2xl font-bold text-blue-800 dark:text-blue-200 mb-1 text-shadow">
                    ${trip.title || (trip.end_to ? trip.end_to.name : "Trip")}
                  </h4>
                  <div class="mb-2 text-lg font-mono text-gray-800 dark:text-gray-100 leading-snug">
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">From:</span> ${trip.start_from?.name || ''}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">To:</span> ${trip.end_to?.name || ''}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">Departure:</span> ${trip.schedule?.start_from || ''}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">Arrival:</span> ${trip.schedule?.end_at || ''}</span>
                    <span class="block"><span class="font-bold text-gray-700 dark:text-gray-200">Duration:</span> ${trip.route?.time || ''}</span>
                  </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                  <span class="text-xl font-extrabold ${statusClass}">
                    <i class="fas ${statusIcon}"></i> ${status}
                  </span>
                </div>
                ${tripNotices}
              </div>
            `;
          });
        } else {
          tripsHtml = '<div class="col-span-full glass p-6 rounded-xl text-xl font-bold text-center text-blue-600 dark:text-blue-200 shadow">No upcoming trips scheduled.</div>';
        }
        document.getElementById('tripsList').innerHTML = tripsHtml;

        // Accessibility announcement
        document.getElementById('ariaLive').textContent = 'Refreshed train schedule and notices';
      })
      .catch(error => {
        console.error('Error refreshing data:', error);
      });
    }

    // Auto-refresh every 30s
    setInterval(refreshData, 30000);
  </script>
</body>
</html>