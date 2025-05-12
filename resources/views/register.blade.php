<!DOCTYPE html>
<html lang="en" class="bg-gradient-to-br from-blue-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Device Registration - Centric Ltd</title>
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
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-thumb { background: #e1e4ea; border-radius: 20px;}
    html.dark ::-webkit-scrollbar-thumb { background: #2d2d3b;}
    /* Accessibility focus */
    *:focus { outline: 2.5px solid #3b82f6 !important; outline-offset: 2px;}
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

  <!-- Registration Form -->
  <div class="flex flex-col items-center justify-center min-h-screen p-4 relative z-10">
    <div class="glass max-w-xl w-full p-8 shadow-xl">
      <div class="flex flex-row items-center gap-4 mb-4">
        <i class="fas fa-train-subway text-4xl text-blue-600"></i>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-wide">Device Registration</h1>
      </div>
      
      @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 rounded-xl text-red-700 dark:text-red-200 font-bold">
          {{ session('error') }}
        </div>
      @endif
      
      <form method="POST" action="{{ route('register.device') }}" autocomplete="off" class="space-y-7">
        @csrf
        <div>
          <label class="block text-lg font-bold text-gray-700 dark:text-gray-100 mb-1">Station Name</label>
          <input required type="text" id="station_name" name="station_name"
            class="w-full rounded-xl border p-3 text-lg bg-white/80 dark:bg-gray-900/70 dark:border-gray-700 focus:ring-2 focus:ring-blue-400 dark:text-gray-200"
            placeholder="e.g. Nairobi Central">
        </div>
        <div>
          <label class="block text-lg font-bold text-gray-700 dark:text-gray-100 mb-1">Device Serial Number</label>
          <input required type="text" id="device_serial_number" name="device_serial_number"
            class="w-full rounded-xl border p-3 text-lg bg-white/80 dark:bg-gray-900/70 dark:border-gray-700 focus:ring-2 focus:ring-blue-400 dark:text-gray-200"
            placeholder="e.g. SN-123456">
        </div>
        <div>
          <label class="block text-lg font-bold text-gray-700 dark:text-gray-100 mb-1">Device Model</label>
          <input required type="text" id="device_model" name="device_model"
            class="w-full rounded-xl border p-3 text-lg bg-white/80 dark:bg-gray-900/70 dark:border-gray-700 focus:ring-2 focus:ring-blue-400 dark:text-gray-200"
            placeholder="e.g. LG TV 32MQ2">
        </div>
        <button type="submit"
          class="w-full py-3 mt-3 text-lg font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md transition-all">
          Register Device
        </button>
      </form>
    </div>
  </div>

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
  </script>
</body>
</html>